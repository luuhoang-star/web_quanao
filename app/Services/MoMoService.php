<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MoMoService
{
    /**
     * Lấy cấu hình MoMo (Ưu tiên Cache site_settings do Admin cấu hình, dự phòng config services).
     */
    public static function getConfig(): array
    {
        $settings = Cache::get('site_settings', []);

        $partnerCode = ! empty($settings['momo_gateway_partner_code'])
            ? $settings['momo_gateway_partner_code']
            : config('services.momo.partner_code', 'MOMOBKUN20180529');

        $accessKey = ! empty($settings['momo_gateway_access_key'])
            ? $settings['momo_gateway_access_key']
            : config('services.momo.access_key', 'klm05TvNBzhg7h7j');

        $secretKey = ! empty($settings['momo_gateway_secret_key'])
            ? $settings['momo_gateway_secret_key']
            : config('services.momo.secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');

        $isProduction = ($settings['momo_gateway_env'] ?? 'sandbox') === 'production';
        $endpoint = $isProduction
            ? 'https://payment.momo.vn/v2/gateway/api/create'
            : config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');

        return [
            'partner_code' => $partnerCode,
            'access_key' => $accessKey,
            'secret_key' => $secretKey,
            'endpoint' => $endpoint,
            'is_production' => $isProduction,
        ];
    }

    /**
     * Tạo yêu cầu thanh toán MoMo Gateway (captureWallet).
     */
    public static function createPayment(Order $order): array
    {
        $config = self::getConfig();
        $partnerCode = $config['partner_code'];
        $accessKey = $config['access_key'];
        $secretKey = $config['secret_key'];
        $endpoint = $config['endpoint'];

        $requestId = $order->order_code.'_'.time();
        // Định danh MoMo orderId duy nhất cho từng lần gọi thanh toán
        $momoOrderId = $order->order_code.'_'.Str::upper(Str::random(4));
        $amount = (string) (int) round($order->total_amount);
        $orderInfo = 'Thanh toán đơn hàng #'.$order->order_code;
        $redirectUrl = route('payment.momo.callback');
        $ipnUrl = route('api.webhook.momo');
        $extraData = base64_encode(json_encode([
            'order_code' => $order->order_code,
            'order_id' => $order->id,
        ]));
        $requestType = 'captureWallet';

        // Xây dựng chuỗi rawHash ký số theo chuẩn MoMo v2
        $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$momoOrderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'ATINO Store',
            'storeId' => 'ATINO Store',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $momoOrderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->post($endpoint, $payload);

            if (! $response->successful()) {
                Log::error('MoMo Gateway create request failed HTTP '.$response->status(), [
                    'order' => $order->order_code,
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'payUrl' => null,
                    'message' => 'Lỗi kết nối máy chủ MoMo (HTTP '.$response->status().')',
                    'raw' => $response->json(),
                ];
            }

            $resData = $response->json();

            if (($resData['resultCode'] ?? -1) === 0 && ! empty($resData['payUrl'])) {
                return [
                    'success' => true,
                    'payUrl' => $resData['payUrl'],
                    'deeplink' => $resData['deeplink'] ?? null,
                    'qrCodeUrl' => $resData['qrCodeUrl'] ?? null,
                    'message' => 'Tạo liên kết thanh toán MoMo thành công',
                    'raw' => $resData,
                ];
            }

            Log::warning('MoMo Gateway create returned non-zero code', [
                'order' => $order->order_code,
                'resultCode' => $resData['resultCode'] ?? null,
                'message' => $resData['message'] ?? 'Không rõ',
            ]);

            return [
                'success' => false,
                'payUrl' => null,
                'message' => $resData['message'] ?? 'Khởi tạo thanh toán MoMo thất bại',
                'raw' => $resData,
            ];
        } catch (\Throwable $th) {
            Log::error('Exception in MoMo createPayment: '.$th->getMessage(), [
                'order' => $order->order_code,
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'payUrl' => null,
                'message' => 'Ngoại lệ khi gọi cổng MoMo: '.$th->getMessage(),
                'raw' => null,
            ];
        }
    }

    /**
     * Xác thực chữ ký phản hồi từ MoMo (dùng cho cả Callback và IPN Webhook).
     */
    public static function verifySignature(array $data): bool
    {
        if (empty($data['signature'])) {
            return false;
        }

        $config = self::getConfig();
        $accessKey = $config['access_key'];
        $secretKey = $config['secret_key'];

        $amount = (string) ($data['amount'] ?? '');
        $extraData = (string) ($data['extraData'] ?? '');
        $message = (string) ($data['message'] ?? '');
        $orderId = (string) ($data['orderId'] ?? '');
        $orderInfo = (string) ($data['orderInfo'] ?? '');
        $orderType = (string) ($data['orderType'] ?? '');
        $partnerCode = (string) ($data['partnerCode'] ?? '');
        $payType = (string) ($data['payType'] ?? '');
        $requestId = (string) ($data['requestId'] ?? '');
        $responseTime = (string) ($data['responseTime'] ?? '');
        $resultCode = (string) ($data['resultCode'] ?? '');
        $transId = (string) ($data['transId'] ?? '');

        $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&message={$message}&orderId={$orderId}&orderInfo={$orderInfo}&orderType={$orderType}&partnerCode={$partnerCode}&payType={$payType}&requestId={$requestId}&responseTime={$responseTime}&resultCode={$resultCode}&transId={$transId}";
        $expectedSignature = hash_hmac('sha256', $rawHash, $secretKey);

        return hash_equals($expectedSignature, (string) $data['signature']);
    }
}
