<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic;
use App\Models\SystemFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SliderController extends Controller {

    public static function upload($arrayImage, $params = null){
        $result     = [];
        if(!empty($arrayImage)){
            // ===== folder upload
            $folderUpload       = config('image.folder_upload');
            $extension          = config('image.extension');
            $name               = $params['name'] ?? time();
            $i                  = 0;
            foreach($arrayImage as $image){
                // ===== set filename & checkexists (Small)
                $filename       = $name.'-slider-'.time().'-'.$i;
                $filepath       = $folderUpload.$filename.'.'.$extension;
                ImageManagerStatic::make($image->getRealPath())
                    ->encode($extension, config('image.quality'))
                    ->save(Storage::path($filepath));
                $result[$i]['file_url']         = Storage::url($filepath);
                /* cập nhật thông tin CSDL */
                $arrayInsert                    = [];
                $arrayInsert['attachment_id']   = $params['attachment_id'] ?? 0;
                $arrayInsert['relation_table']  = $params['relation_table'] ?? null;
                $arrayInsert['file_name']       = $filename;
                $arrayInsert['file_path']       = $filepath;
                $arrayInsert['file_extension']  = $extension;
                $arrayInsert['file_type']       = 'slider';
                $idInsert                       = SystemFile::insertItem($arrayInsert);
                $result[$i]['file_id']          = $idInsert;
                ++$i;
            }
        }
        return $result;
    }

    public static function remove(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                /* xóa file */
                $infofile   = SystemFile::find($request->get('id'));
                $filePath   = Storage::path($infofile['file_path']);
                if(file_exists($filePath)) @unlink($filePath);
                /* xóa khỏi CSDL */
                $flag       = SystemFile::removeItem($request->get('id'));
                DB::commit();
                return $flag;
            } catch(\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }

    public static function removeById($id){
        if(!empty($id)){
            try {
                DB::beginTransaction();
                /* xóa file */
                $infofile   = SystemFile::find($id);
                $filePath   = Storage::path($infofile['file_path']);
                if(file_exists($filePath)) @unlink($filePath);
                /* xóa khỏi CSDL */
                $flag       = SystemFile::removeItem($id);
                DB::commit();
                return $flag;
            } catch(\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }

}
