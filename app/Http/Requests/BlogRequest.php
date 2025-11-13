<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueSlug;

class BlogRequest extends FormRequest
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
            'name'                      => 'required|max:255',
            'description'               => 'required',
            'ordering'                  => 'min:0',
            'content'                   => 'required',
            'seo_title'                 => 'required',
            'seo_description'           => 'required',
            'slug'                      => [
                'required',
                new UniqueSlug(request()),
                
            ],
            'rating_aggregate_count'    => 'required',
            'rating_aggregate_star'     => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'             => 'Tiêu đề trang không được để trống!',
            'name.max'                  => 'Tiêu đề trang không được vượt quá 255 ký tự!',
            'description'               => 'Mô tả trang không được để trống!',
            'ordering.min'              => 'Giá trị không được nhỏ hơn 0!',
            'content.required'          => 'Nội dung không được để trống!',
            'seo_title.required'        => 'Tiêu đề SEO không được để trống!',
            'seo_description.required'  => 'Mô tả SEO không được để trống!',
            'slug.required'             => 'Đường dẫn tĩnh không được để trống!',
            'rating_aggregate_count'    => 'Số lượt đánh giá không được để trống!',
            'rating_aggregate_star'     => 'Điểm đánh giá không được để trống!'
        ];
    }
}
