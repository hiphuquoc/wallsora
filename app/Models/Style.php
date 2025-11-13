<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Style extends Model {
    use HasFactory;
    protected $table        = 'style_info';
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
            $model      = new Style();
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

    // public static function getArrayIdStyleRelatedByIdStyle($infoStyle, $variable){
    //     $idPage             = $infoStyle->seo->id;
    //     $arrayChild         = self::select('*')
    //                             ->whereHas('seo', function($query) use($idPage){
    //                                 $query->where('parent', $idPage);
    //                             })
    //                             ->with('seo')
    //                             ->get();
    //     /* kiểm tra đã là Style cha chưa => chưa thì lấy id Style cha gộp vào mảng */
    //     if(!empty($arrayChild)&&$arrayChild->isNotEmpty()){
    //         foreach($arrayChild as $child){
    //             $variable[]     = $child->id;
    //             self::getArrayIdStyleRelatedByIdStyle($child, $variable);
    //         }
    //     }
    //     return $variable;
    // }

    // public static function getTreeStyle(){
    //     $result     = self::select('style_info.*')
    //                     ->whereHas('seo', function($query){
    //                         $query->where('level', 1);
    //                     })
    //                     ->with('seo')
    //                     ->join('seo', 'seo.id', '=', 'style_info.seo_id')
    //                     ->orderBy('seo.ordering', 'DESC')
    //                     ->get();
    //     for($i=0;$i<$result->count();++$i){
    //         $result[$i]->childs  = self::getTreeStyleByInfoStyle($result[$i]);
    //     }
    //     return $result;
    // }

    // public static function getTreeStyleByInfoStyle($infoStyle){
    //     $result                 = new \Illuminate\Database\Eloquent\Collection;
    //     if(!empty($infoStyle)){
    //         $idPage             = $infoStyle->seo->id;
    //         $result             = self::select('style_info.*')
    //                                 ->whereHas('seo', function($query) use($idPage){
    //                                     $query->where('parent', $idPage);
    //                                 })
    //                                 ->with('seo')
    //                                 ->join('seo', 'seo.id', '=', 'style_info.seo_id')
    //                                 ->orderBy('seo.ordering', 'DESC')
    //                                 ->get();
    //         if($result->isNotEmpty()){
    //             for($i=0;$i<$result->count();++$i){
    //                 $result[$i]->childs = self::getTreeStyleByInfoStyle($result[$i]);
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
