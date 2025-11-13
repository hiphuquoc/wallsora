<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    use HasFactory;
    protected $table        = 'event_info';
    protected $fillable     = [
        'seo_id',
        'name', 
        'description',
        'en_seo_id',
        'en_name',
        'en_description',
        'icon'
    ];
    public $timestamps = true;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Event();
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

    // public static function getArrayIdEventRelatedByIdEvent($infoEvent, $variable){
    //     $idPage             = $infoEvent->seo->id;
    //     $arrayChild         = self::select('*')
    //                             ->whereHas('seo', function($query) use($idPage){
    //                                 $query->where('parent', $idPage);
    //                             })
    //                             ->with('seo')
    //                             ->get();
    //     /* kiểm tra đã là category cha chưa => chưa thì lấy id category cha gộp vào mảng */
    //     if(!empty($arrayChild)&&$arrayChild->isNotEmpty()){
    //         foreach($arrayChild as $child){
    //             $variable[]     = $child->id;
    //             self::getArrayIdEventRelatedByIdEvent($child, $variable);
    //         }
    //     }
    //     return $variable;
    // }

    // public static function getTreeEvent(){
    //     $result = self::select('event_info.*')
    //                 ->whereHas('seo', function ($query) {
    //                     $query->where('level', 1);
    //                 })
    //                 ->with('seo', 'en_seo', 'products')
    //                 ->join('seo', 'seo.id', '=', 'event_info.seo_id')
    //                 ->orderBy('seo.ordering', 'DESC')
    //                 ->get();
    //     for($i=0;$i<$result->count();++$i){
    //         $result[$i]->childs  = self::getTreeEventByInfoEvent($result[$i]);
    //     }
    //     return $result;
    // }

    // public static function getTreeEventByInfoEvent($infoEvent){
    //     $result                 = new \Illuminate\Database\Eloquent\Collection;
    //     if(!empty($infoEvent)){
    //         $idPage             = $infoEvent->seo->id;
    //         $result             = self::select('event_info.*')
    //                                 ->whereHas('seo', function($query) use($idPage){
    //                                     $query->where('parent', $idPage);
    //                                 })
    //                                 ->with('seo', 'en_seo', 'products')
    //                                 ->join('seo', 'seo.id', '=', 'event_info.seo_id')
    //                                 ->orderBy('seo.ordering', 'DESC')
    //                                 ->get();
    //         if($result->isNotEmpty()){
    //             for($i=0;$i<$result->count();++$i){
    //                 $result[$i]->childs = self::getTreeEventByInfoEvent($result[$i]);
    //             }
    //         }
    //     }
    //     return $result;
    // }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function en_seo() {
        return $this->hasOne(\App\Models\EnSeo::class, 'id', 'en_seo_id');
    }
}
