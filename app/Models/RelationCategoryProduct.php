<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationCategoryProduct extends Model {
    use HasFactory;
    protected $table        = 'relation_category_product';
    protected $fillable     = [
        'product_info_id',
        'category_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationCategoryProduct();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoCategory() {
        return $this->hasOne(\App\Models\Category::class, 'id', 'category_info_id');
    }

    public function infoProduct() {
        return $this->hasOne(\App\Models\Product::class, 'id', 'product_info_id');
    }
}
