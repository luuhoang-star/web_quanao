<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'coupon_id',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total_amount',
        'status',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class)->latest();
    }

    public function latestReturn(): HasOne
    {
        return $this->hasOne(OrderReturn::class)->latestOfMany();
    }

    /**
     * Kiểm tra xem đơn hàng có thể hủy được không (chỉ hủy khi chờ xử lý - pending).
     */
    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Kiểm tra xem đơn hàng có yêu cầu đổi/trả đang active (chờ duyệt hoặc đã duyệt) hay không.
     */
    public function hasActiveReturnRequest(): bool
    {
        if ($this->relationLoaded('returns')) {
            return $this->returns->whereIn('status', ['pending', 'approved'])->isNotEmpty();
        }

        return $this->returns()->whereIn('status', ['pending', 'approved'])->exists();
    }

    /**
     * Kiểm tra xem đơn hàng có đủ điều kiện gửi yêu cầu đổi/trả hay không.
     * Điều kiện: Đã giao hàng thành công (completed), trong vòng 7 ngày, và chưa có yêu cầu active.
     */
    public function canBeReturned(): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        // Lấy thời điểm hoàn thành từ statusHistories hoặc fallback updated_at
        $completedTime = $this->statusHistories->where('status', 'completed')->last()?->created_at ?? $this->updated_at;

        if ($completedTime && $completedTime->lt(now()->subDays(7))) {
            return false;
        }

        return ! $this->hasActiveReturnRequest();
    }

    public const STATUS_LABELS = [
        'pending' => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'processing' => 'Đang chuẩn bị hàng',
        'shipping' => 'Đang giao hàng',
        'completed' => 'Giao thành công',
        'cancelled' => 'Đã hủy',
        'returned' => 'Trả hàng / Hoàn tiền',
    ];

    /**
     * Lấy tên tiếng Việt rõ ràng cho trạng thái đơn hàng.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * Ghi nhận một mốc lịch sử trạng thái mới cho đơn hàng.
     */
    public function logStatusHistory(string $status, ?string $note = null, string $byType = 'system', ?int $byId = null, ?string $byName = null): OrderStatusHistory
    {
        return $this->statusHistories()->create([
            'status' => $status,
            'note' => $note,
            'updated_by_type' => $byType,
            'updated_by_id' => $byId,
            'updated_by_name' => $byName ?? ($byType === 'system' ? 'Hệ thống' : ($byType === 'admin' ? 'Quản trị viên' : 'Khách hàng')),
        ]);
    }
}
