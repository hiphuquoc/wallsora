<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedirectInfo extends Model {
    use HasFactory;
    protected $table        = 'redirect_info';
    protected $fillable     = [
        'orl_url', 
        'new_url'
    ];
    public $timestamps      = false;

    public static function getList($params = null){
        $result     = self::select('*')
                        ->orderBy('id', 'DESC')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RedirectInfo();
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
}
