<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    use HasFactory;
    protected $table        = 'customer_info';
    protected $fillable     = [
        'name', 
        'phone',
        'zalo',
        'email',
    ];
    public $timestamps = true;

    // public static function getList($params = null){
    //     $result     = self::select('*')
    //                     /* tìm theo tên */
    //                     ->when(!empty($params['search_name']), function($query) use($params){
    //                         $query->where('name', 'like', '%'.$params['search_name'].'%');
    //                     })
    //                     ->orderBy('created_at', 'DESC')
    //                     ->with('seo')
    //                     ->paginate($params['paginate']);
    //     return $result;
    // }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Customer();
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

    public function address() {
        return $this->hasMany(\App\Models\CustomerAddress::class, 'customer_info_id', 'id');
    }

    public function orders() {
        return $this->hasMany(\App\Models\Order::class, 'customer_info_id', 'id');
    }
}
