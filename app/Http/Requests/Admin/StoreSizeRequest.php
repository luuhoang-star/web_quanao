<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSizeRequest extends FormRequest
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
                'name' => strtoupper(trim((string) $this->name)),
            ]);
        }
        if ($this->filled('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->code)),
            ]);
        }
        if (! $this->filled('size_type')) {
            $this->merge([
                'size_type' => 'OTHER',
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
        return [
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('sizes', 'name'),
            ],
            'code' => ['nullable', 'string', 'max:50', 'unique:sizes,code'],
            'size_type' => ['nullable', 'string'],
            'specifications' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
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
            'name.required' => 'Vui lòng nhập tên kích cỡ (Ví dụ: S, M, L, XL, 29, 30...).',
            'name.unique' => 'Tên kích cỡ này đã tồn tại trên hệ thống, vui lòng chọn tên khác.',
            'code.unique' => 'Mã Size/Code này đã được sử dụng.',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên không âm.',
            'sort_order.min' => 'Thứ tự sắp xếp phải từ 0 trở lên.',
        ];
    }
}
