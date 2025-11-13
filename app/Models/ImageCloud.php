<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ImageCloud extends Model {
    use HasFactory;
    protected $table        = 'image_cloud';
    protected $fillable     = [
        'user_id',
        'folder_name',
        'file_name',
        'extension',
        'file_cloud',
        'width',
        'height',
        'file_size',
        'mine_type',
    ];
    public $timestamps = true;

    // public static function getList($params = null){
    //     $arrayLanguageAccept = [];
    //     foreach(config('language') as $language) if($language['key']!='vi') $arrayLanguageAccept[] = $language['key'];
    //     $result     = self::select('*')
    //                     ->whereDoesntHave('seo', function ($query) use($arrayLanguageAccept){
    //                         $query->whereIn('language', $arrayLanguageAccept);
    //                     })
    //                     /* tìm theo tên */
    //                     ->when(!empty($params['search_name']), function($query) use($params){
    //                         $query->whereHas('seo', function($subQuery) use($params){
    //                             $subQuery->where('title', 'like', '%'.$params['search_name'].'%');
    //                         });
    //                     })
    //                     ->orderBy('created_at', 'DESC')
    //                     ->with('categories')
    //                     ->paginate($params['paginate']);
    //     return $result;
    // }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new ImageCloud();
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
