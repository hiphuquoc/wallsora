<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model {
    use HasFactory;
    protected $table        = 'seller_info';
    protected $fillable     = [
        'prefix_name',
        'name',
        'phone', 
        'zalo',
        'email',
        'address',
        'province_info_id',
        'district_info_id'
    ];
    public $timestamps = true;

    // public static function getList($params = null){
    //     $result     = self::select('*')
    //                     /* tìm theo tên */
    //                     ->when(!empty($params['search_name']), function($query) use($params){
    //                         $query->where('name', 'like', '%'.$params['search_name'].'%');
    //                     })
    //                     /* tìm theo nhãn hàng */
    //                     ->when(!empty($params['search_brand']), function($query) use($params){
    //                         $query->whereHas('brand', function($q) use ($params){
    //                             $q->where('id', $params['search_brand']);
    //                         });
    //                     })
    //                     /* tìm theo danh mục */
    //                     ->when(!empty($params['search_category']), function($query) use($params){
    //                         $query->whereHas('categories.infoCategory', function($q) use ($params){
    //                             $q->where('id', $params['search_category']);
    //                         });
    //                     })
    //                     ->orderBy('created_at', 'DESC')
    //                     ->with(['files' => function($query){
    //                         $query->where('relation_table', 'product_info');
    //                     }])
    //                     ->with('seo', 'prices.files', 'contents', 'brand', 'categories')
    //                     ->paginate($params['paginate']);
    //     return $result;
    // }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Seller();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        $flag           = false;
        if(!empty($id)&&!empty($params)){
            $model      = self::find($id);
            foreach($params as $key => $value) $model->{$key}  = $value;
            $flag       = $model->update();
        }
        return $flag;
    }

    // public function brand(){
    //     return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id');
    // }
}
