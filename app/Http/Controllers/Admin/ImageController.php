<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemFile;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Models\ImageCloud;
use Illuminate\Support\Facades\Auth;

use App\Services\BuildInsertUpdateModel;

class ImageController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function list(Request $request){
        $searchName             = $request->get('search_name') ?? null;
        $list                   = ImageCloud::select('*')
                                    ->when(!empty($searchName), function($query) use($searchName){
                                        $query->where('file_name', 'LIKE', '%'.$searchName.'%');
                                    })
                                    ->get();
        $params['search_name']  = $searchName;
        return view('admin.image.list', compact('list', 'params'));
    }

    public function loadImage(Request $request){
        $idImageCloud           = $request->get('image_cloud_id');
        $infoImageCloud         = ImageCloud::find($idImageCloud);
        return view('admin.image.oneRow', compact('infoImageCloud'));
    }

    public function loadModal(Request $request){
        $result             = '';
        $idImageCloud       = $request->get('image_cloud_id');
        if(!empty($idImageCloud)){
            $infoImageCloud = ImageCloud::find($idImageCloud);
            $result         = view('admin.image.formModalChangeImage', compact('infoImageCloud'))->render();
        }
        echo $result;
    }

    public function removeImage(Request $request){
        $flagDelete               = false;
        if(!empty($request->get('image_cloud_id'))){
            /* lấy thông tin ảnh */
            $infoImageCloud = ImageCloud::find($request->get('image_cloud_id'));
            /* xóa trên cloud */
            $flagDelete     = Upload::deleteWallpaper($infoImageCloud->file_cloud);
            /* xóa trong CSDL */
            if($flagDelete) $infoImageCloud->delete();
        }
        return $flagDelete;
    }

    public function changeImage(Request $request){
        $requestImage               = $request->file('image_new');
        $idImageCloud               = $request->get('image_cloud_id');
        $infoImageCloud             = ImageCloud::find($idImageCloud);
        /* upload đè ảnh cũ */
        $fileNameUpload = $infoImageCloud->file_name.'.'.$infoImageCloud->extension;
        $fileCloud      = Upload::uploadWallpaper($requestImage, $fileNameUpload, $infoImageCloud->folder_name);
        /* cập nhật lại kích thước & dung lượng ảnh */
        $imageInfo      = getimagesize($requestImage);
        $width          = $imageInfo[0];
        $height         = $imageInfo[1];
        $fileSize       = filesize($requestImage);
        $flag           = ImageCloud::updateItem($idImageCloud, [
            'width'     => $width,
            'height'    => $height,
            'file_size' => $fileSize,
        ]);
        $result         = [];
        $result['flag'] = $flag;
        return json_encode($result);
    }

    // public function checkImageExists($basenameOld, $basenameNew){
    //     $result                     = [];
    //     if(!empty($basenameOld)&&!empty($basenameNew)){
    //         /* kiểm tra trường hợp cả 2 trùng nhau */
    //         if($basenameOld==$basenameNew) {
    //             $result['flag']     = false;
    //             $result['message']  = 'Tên ảnh mới trùng với Tên ảnh cũ!';
    //             return $result;
    //         }
    //         /* kiểm tra trường hợp trùng trong thư mục */
    //         if(file_exists(public_path($basenameNew))){
    //             $result['flag']     = false;
    //             $result['message']  = 'Ảnh mới trùng với một ảnh khác trong thư mục!';
    //             return $result;
    //         }
    //         /* kiểm tra trường hợp trùng trong database */
    //         $tmp                    = SystemFile::select('*')
    //                                     ->where('file_name', $basenameNew)
    //                                     ->first();
    //         if(!empty($tmp)){
    //             $result['flag']     = false;
    //             $result['message']  = 'Ảnh mới trùng với một ảnh khác trong CSDL!';
    //             return $result;
    //         }
    //         /* hợp lệ */
    //         $result['flag']         = true;
    //         $result['message']      = null;
    //     }
    //     return $result;
    // }

    public function uploadImages(Request $request){
        $count                  = 0;
        $content                = '';
        if(!empty($request->file('image_upload'))){
            foreach($request->file('image_upload') as $image){
                $imageName      = $image->getClientOriginalName();
                $imageFileName  = \App\Helpers\Charactor::convertStrToUrl(pathinfo($imageName)['filename']);
                $extension      = config('image.extension');
                $filePathUpload = Storage::path(config('image.folder_upload').$imageFileName.'.'.$extension);
                $infoImageCloud = self::uploadImage($image, $filePathUpload, 'copy');
                $content        .= view('admin.image.oneRow', [
                    'infoImageCloud'    => $infoImageCloud,
                    'style'             => 'box-shadow: 0 0 5px rgb(0, 123, 255)',
                ]);
                ++$count;
            }
        }
        $result['count']    = $count;
        $result['content']  = $content;
        return json_encode($result);
    }

    public static function uploadImage($requestImage, $filePathUpload, $action = 'rewrite'){
        $infoImage          = new \Illuminate\Database\Eloquent\Collection;
        if(!empty($requestImage)){
            /* thêm type cho filePath */
            $imageFileName  = pathinfo($filePathUpload)['filename'];
            $extension      = config('image.extension');
            $miniType       = config('image.mine_type');
            $imageInfo      = getimagesize($requestImage);
            $width          = $imageInfo[0];
            $height         = $imageInfo[1];
            $fileSize       = filesize($requestImage);
            $fileNameUpload = $imageFileName.'.'.$extension;
            if($action=='copy') if(file_exists($filePathUpload)) $fileNameUpload = $imageFileName.'-'.time().'.'.$extension;
            /* thêm ảnh */
            $folderUpload   = config('main_'.env('APP_NAME').'.google_cloud_storage.images');
            $fileCloud      = Upload::uploadWallpaper($requestImage, $fileNameUpload, $folderUpload);
            /* Lưu thông tin vào CSDL */
            $idImageCloud   = ImageCloud::insertItem([
                'user_id'       => Auth::user()->id,
                'folder_name'   => $folderUpload,
                'file_name'     => $imageFileName,
                'extension'     => $extension,
                'file_cloud'    => $fileCloud,
                'width'         => $width,
                'height'        => $height,
                'file_size'     => $fileSize,
                'mine_type'     => $miniType,
            ]);
            /* lấy ngược lại thông tin */
            $infoImage      = ImageCloud::find($idImageCloud);
        }
        return $infoImage;
    }

    // public static function replaceImageInContentWithLoading($content){
    //     if(!empty($content)){
    //         preg_match_all('#(<img.*>)#imsU', $content, $match);
    //         $dataAtrrImage  = $match[1];
    //         $dataImage      = [];
    //         $i              = 0;
    //         foreach($dataAtrrImage as $attrImage){
    //             $dataImage[$i]['source']   = $attrImage;
    //             /* src */
    //             preg_match('#src="(.*)"#imsU', $attrImage, $match);
    //             $dataImage[$i]['src']      = $match[1];
    //             /* data-src */
    //             preg_match('#data-src="(.*)"#imsU', $attrImage, $match);
    //             $dataImage[$i]['data-src'] = $match[1] ?? null;
    //             /* alt và title */
    //             preg_match('#alt="(.*)"#imsU', $attrImage, $match);
    //             $dataImage[$i]['alt']      = $match[1] ?? null;
    //             $dataImage[$i]['title']    = $match[1] ?? null;
    
    //             /* Lấy class */
    //             preg_match('#class="(.*)"#imsU', $attrImage, $match);
    //             $dataImage[$i]['class']    = $match[1] ?? null;
    
    //             ++$i;
    //         }
    //         /* duyệt mảng => thay thế */
    //         $tmp            = [];
    //         foreach($dataImage as $image){
    //             $dataSrc    = $image['data-src'] ?? $image['src'];
    //             $class      = $image['class'] ?? ''; // Lấy class từ mảng $dataImage
    //             $tmp        = '<img src="'.Storage::url(config('image.loading_main_gif')).'" data-src="'.$dataSrc.'" alt="'.$image['alt'].'" title="'.$image['title'].'" style="width:100%;" class="lazyload '.$class.'" />'; // Bổ sung class vào thẻ img
    //             $content    = str_replace($image['source'], $tmp, $content);
    //         }
    //     }
    //     return $content;
    // }

}
