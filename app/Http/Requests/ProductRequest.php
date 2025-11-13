<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueSlug;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'                     => 'required',
            // 'code'                      => 'required',
            'seo_title'                 => 'required',
            'seo_description'           => 'required',
            'rating_aggregate_count'    => 'required',
            'rating_aggregate_star'     => 'required',
            // 'prices'                    => 'required',
            // 'categories'                => 'required',
            'slug'                      => [
                'required',
                new UniqueSlug(request()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required'                     => 'Tiêu đề không được để trống!',
            'description.required'              => 'Mô tả không được để trống!',
            'code.required'                     => 'Mã sản phẩm không được để trống!',
            'seo_title.required'                => 'Tiêu đề Seo không được để trống!',
            'seo_description.required'          => 'Mô tả Seo không được để trống!',
            'rating_aggregate_count.required'   => 'Số lượt đánh giá không được để trống!',
            'rating_aggregate_star.required'    => 'Số sao không được để trống!',
            'prices.required'                   => 'Giá bán không được để trống!',
            'categories.required'               => 'Danh mục không được để trống!',
            // 'brand.required'                    => 'Nhãn hàng không được để trống!',
            // 'title_all.required'                => 'Tiêu đề của giá tất cả không được để trống!',
            // 'price_all.required'                => 'Giá tất cả không được để trống!',
            'slug.required'                     => 'Đường dẫn tĩnh không được để trống!'
        ];
    }
}
