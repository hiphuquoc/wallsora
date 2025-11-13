<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseCategory extends Model {
    public static function getArrayIdCategoryRelatedByIdCategory($infoCategory, $variable){
        $idPage             = $infoCategory->seo->id;
        $arrayChild         = self::select('*')
                                ->whereHas('seo', function($query) use($idPage){
                                    $query->where('parent', $idPage);
                                })
                                ->with('seo')
                                ->get();
        /* kiểm tra đã là category cha chưa => chưa thì lấy id category cha gộp vào mảng */
        if(!empty($arrayChild)&&$arrayChild->isNotEmpty()){
            foreach($arrayChild as $child){
                $variable[]     = $child->id;
                self::getArrayIdCategoryRelatedByIdCategory($child, $variable);
            }
        }
        return $variable;
    }

    public static function getTreeCategory($wheres = []){
        $tableName = (new static)->getTable(); // Lấy tên bảng của model cụ thể

        $query = self::select("$tableName.*")
            ->whereHas('seo', function ($query) {
                $query->where('level', 1);
            })
            ->with('seo')
            ->join('seo', 'seo.id', '=', "$tableName.seo_id")
            ->orderBy('seo.ordering', 'DESC');

        foreach ($wheres as $key => $where) {
            $query->where($key, $where);
        }

        $result = $query->get();
        for ($i = 0; $i < $result->count(); ++$i) {
            $result[$i]->childs = self::getTreeCategoryByInfoCategory($result[$i], $wheres);
        }

        return $result;
    }

    public static function getTreeCategoryByInfoCategory($infoCategory, $wheres){
        $result = new \Illuminate\Database\Eloquent\Collection;

        if (!empty($infoCategory)) {
            $idPage = $infoCategory->seo->id;
            $tableName = (new static)->getTable(); // Lấy tên bảng của model cụ thể

            $query = self::select("$tableName.*")
                ->whereHas('seo', function ($query) use ($idPage) {
                    $query->where('parent', $idPage);
                })
                ->with('seo')
                ->join('seo', 'seo.id', '=', "$tableName.seo_id")
                ->orderBy('seo.ordering', 'DESC');

            foreach ($wheres as $key => $where) {
                $query->where($key, $where);
            }

            $result = $query->get();

            if ($result->isNotEmpty()) {
                for ($i = 0; $i < $result->count(); ++$i) {
                    $result[$i]->childs = self::getTreeCategoryByInfoCategory($result[$i], $wheres);
                }
            }
        }

        return $result;
    }
}