<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    protected $table        = 'order_info';
    protected $fillable     = [
        'order_status_id',
        'code', 
        'customer_info_id',
        'product_count',
        'product_cash',
        'ship_cash',
        'total',
        'payment_method_info_id',
        'email',
        'note',
        'trans_id',
        'payment_status',
        'payment_type'
    ];
    public $timestamps = true;

    public static function getList($params = null){
        $result     = self::select('*')
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $query->where('name', 'like', '%'.$params['search_name'].'%');
                        })
                        ->orderBy('created_at', 'DESC')
                        ->with('status', 'customer', 'products.infoProduct', 'products.infoPrice', 'paymentMethod')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Order();
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

    public function status() {
        return $this->hasOne(\App\Models\OrderStatus::class, 'id', 'order_status_id');
    }

    public function customer() {
        return $this->hasOne(\App\Models\Customer::class, 'id', 'customer_info_id');
    }

    public function products() {
        return $this->hasMany(\App\Models\RelationOrderInfoProductInfo::class, 'product_info_id', 'id');
    }

    public function wallpapers() {
        return $this->hasMany(\App\Models\RelationOrderInfoWallpaperInfo::class, 'order_info_id', 'id');
    }

    public function paymentMethod() {
        return $this->hasOne(\App\Models\PaymentMethod::class, 'id', 'payment_method_info_id');
    }

    // public function province() {
    //     return $this->hasOne(\App\Models\Province::class, 'id', 'province_info_id');
    // }

    // public function district() {
    //     return $this->hasOne(\App\Models\District::class, 'id', 'district_info_id');
    // }
}
