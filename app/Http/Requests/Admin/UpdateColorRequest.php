<?php

namespace App\Http\Requests\Admin;

use App\Models\Color;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateColorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim((string) $this->name),
            ]);
        }
        if ($this->filled('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->code)),
            ]);
        }
        if ($this->has('hex_code')) {
            $hex = trim((string) $this->hex_code);
            if (! str_starts_with($hex, '#')) {
                $hex = '#'.$hex;
            }
            $this->merge([
                'hex_code' => strtoupper($hex),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $color = $this->route('color');
        $colorId = $color instanceof Color ? $color->id : $color;

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('colors', 'name')->ignore($colorId)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('colors', 'code')->ignore($colorId)],
            'hex_code' => ['required', 'string', 'max:10', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên màu sắc.',
            'name.unique' => 'Tên màu sắc này đã tồn tại trên hệ thống.',
            'code.unique' => 'Mã màu/code này đã được sử dụng.',
            'hex_code.required' => 'Vui lòng chọn hoặc nhập mã màu HEX.',
            'hex_code.regex' => 'Mã màu HEX không đúng định dạng (Ví dụ: #FF0000, #000000).',
        ];
    }
}
