<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISO3166 extends Model {
    use HasFactory;
    protected $table        = 'iso_3166_info';
    protected $fillable     = [
        'name', 
        'alpha_2',
        'alpha_3',
        'country_code',
        'region',
        'region_code',
        'sub_region_code',
        'percent_discount',
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new ISO3166();
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

    public function timezones() {
        return $this->hasMany(\App\Models\Timezone::class, 'iso_3166_info_id', 'id');
    }
}
