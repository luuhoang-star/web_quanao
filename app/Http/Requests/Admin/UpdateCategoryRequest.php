<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        if ($this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug($this->slug),
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
        $category = $this->route('category');
        $categoryId = $category instanceof Category ? $category->id : $category;
        $parentId = $this->input('parent_id') ?: null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')
                    ->where(fn ($q) => $parentId ? $q->where('parent_id', $parentId) : $q->whereNull('parent_id'))
                    ->ignore($categoryId),
            ],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$categoryId]),
                function ($attribute, $value, $fail) use ($category) {
                    if ($value && $category instanceof Category) {
                        $descendantIds = $category->getDescendantIds();
                        if (in_array((int) $value, $descendantIds)) {
                            $fail('Không thể chọn danh mục con/cháu làm danh mục cha (Tránh tạo vòng lặp danh mục!).');
                        }
                    }
                },
            ],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
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
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Danh mục này đã tồn tại trong cùng danh mục cha được chọn.',
            'slug.unique' => 'Đường dẫn SEO (Slug) này đã tồn tại trên hệ thống.',
            'parent_id.exists' => 'Danh mục cha được chọn không hợp lệ.',
            'parent_id.not_in' => 'Danh mục không thể tự chọn chính nó làm danh mục cha.',
            'image.image' => 'Ảnh danh mục phải là định dạng hình ảnh.',
            'image.max' => 'Kích thước ảnh danh mục không được vượt quá 2MB.',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên không âm.',
            'sort_order.min' => 'Thứ tự sắp xếp phải từ 0 trở lên.',
        ];
    }
}
