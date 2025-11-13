<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Upload;

use Intervention\Image\ImageManagerStatic;
use App\Models\SystemFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller {

    public static function upload($arrayImage, $params = null){
        $result     = [];
        if(!empty($arrayImage)){
            // ===== folder upload
            $folderUpload       = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
            $fileExtension      = config('image.extension');
            $i                  = 0;
            foreach($arrayImage as $image){
                $fileNameNonExtesion    = $params['name'].'-'.time().'-'.$i;
                $fileName               = $fileNameNonExtesion.'.'.$fileExtension;
                $dataPath               = Upload::uploadWallpaper($image, $fileName, $folderUpload);
                if(!empty($dataPath)){
                    /* lưu CSDL */
                    SystemFile::insertItem([
                        'attachment_id'         => $params['attachment_id'],
                        'relation_table'        => $params['relation_table'],
                        'file_name'             => $fileNameNonExtesion,
                        'file_path'             => $dataPath,
                        'file_extension'        => $fileExtension,
                        'file_type'             => $params['file_type'],
                    ]);
                    ++$i;
                }
                
            }
        }
        return $result;
    }

    public static function remove(Request $request){
        $id         = $request->get('id_file') ?? 0;
        $flag       = self::actionRemove($id); 
        return $flag;
    }

    public static function removeById($id){
        $id     = $id ?? 0;
        $flag       = self::actionRemove($id); 
        return $flag;
    }

    private static function actionRemove($id){
        if(!empty($id)){
            try {
                DB::beginTransaction();
                /* xóa file */
                $infofile       = SystemFile::find($id);
                /* xóa trên google cloud */
                \App\Helpers\Upload::deleteWallpaper($infofile->file_path);
                /* xóa khỏi CSDL */
                $flag           = SystemFile::removeItem($id);
                DB::commit();
                return $flag;
            } catch(\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }
}
