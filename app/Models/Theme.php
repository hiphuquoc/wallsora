<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model {
    use HasFactory;
    protected $table        = 'theme_info';
    protected $fillable     = [
        'name',
        'type', 
        'status',
        'note'
    ];
    public $timestamps = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Theme();
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

    public function colors() {
        return $this->hasMany(\App\Models\ThemeColor::class, 'theme_info_id', 'id');
    }
}
