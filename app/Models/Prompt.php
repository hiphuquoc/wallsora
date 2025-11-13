<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model {
    use HasFactory;
    protected $table        = 'prompt_info';
    protected $fillable     = [
        'type',
        'reference_table', 
        'reference_name',
        'reference_prompt',
        'ai',
        'tool',
        'version',
    ];
    public $timestamps = false;

    public static function getList($params = null){
        $result     = self::select('*')
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $query->where('reference_table', 'like', '%'.$params['search_name'].'%')
                            ->orWhere('reference_name', 'like', '%'.$params['search_name'].'%')
                            ->orWhere('type', 'like', '%'.$params['search_name'].'%');
                        })
                        ->orderBy('reference_table', 'DESC')
                        ->orderBy('type', 'ASC')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Prompt();
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
