<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use App\Services\BuildInsertUpdateModel;
use App\Models\Wallpaper;
use App\Models\RelationProductPriceWallpaperInfo;
use App\Helpers\Charactor;

use App\Http\Controllers\SettingController;

// use App\Jobs\UploadSourceAndWallpaper;

class WallpaperController extends Controller {

    public function list(Request $request){
        $params             = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewWallpaperInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        /* Search theo tên */
        if(!empty($request->get('search_name'))) {
            $params['search_name'] = $request->get('search_name');
            $list           = Wallpaper::getList($params);
            $total          = $list->total();
        } else {
            $list           = new \Illuminate\Database\Eloquent\Collection;
            $total          = Wallpaper::count();
        }
        return view('admin.wallpaper.list', compact('list', 'total', 'params', 'viewPerPage'));
    }

    public function loadOneRow(Request $request){
        $response           = '';
        if(!empty($request->get('id'))){
            $item           = Wallpaper::select('*')
                                ->where('id', $request->get('id'))
                                ->first();
            $response       = view('admin.wallpaper.oneRow', compact('item'))->render();
        }
        echo $response;
    }

    public function loadFormUploadSourceAndWallpaper(Request $request){
        set_time_limit(0);

        $xhtml = '';
        if(!empty($request->get('data_id'))){
            foreach($request->get('data_id') as $idBox){
                $xhtml .= view('admin.wallpaper.oneFormUploadSourceAndWallpaper', compact('idBox'))->render();
            }
        }
        echo $xhtml;
    }

    public function uploadWallpaperWithSource(Request $request){
        // Thiết lập thời gian thực thi không giới hạn
        set_time_limit(0);
        try {
            DB::beginTransaction();
            if (!empty($request->file('files.wallpaper')) && !empty($request->file('files.source'))){
                $wallpaper          = $request->file('files.wallpaper');
                $source             = $request->file('files.source');
                $i                  = $request->get('count');
                /* Lấy thông tin ảnh wallpaper */
                $imageInfoW                     = getimagesize($wallpaper);
                $widthW                         = $imageInfoW[0];
                $heightW                        = $imageInfoW[1];
                $miniTypeW                      = $imageInfoW['mime'];
                $fileSizeW                      = filesize($wallpaper);
                $extensionW                     = config('image.extension');
                $fileNameNonHaveExtensionW      = \App\Helpers\Charactor::convertStrToUrl($request->get('name')).'-'.time().'-'.$i;
                $folderW                        = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $fileUrlW                       = $folderW.$fileNameNonHaveExtensionW.'.'.$extensionW;
                /* upload wallpaper lên google_cloud_storage với 3 bản Full Small Mini (thông qua function Upload) */
                \App\Helpers\Upload::uploadWallpaper($wallpaper, $fileNameNonHaveExtensionW.'.'.$extensionW, $folderW);
                /* lấy thông tin ảnh source */
                $imageInfoS                     = getimagesize($source);
                $widthS                         = $imageInfoS[0];
                $heightS                        = $imageInfoS[1];
                $miniTypeS                      = $imageInfoS['mime'];
                $fileSizeS                      = filesize($source);
                $extensionS                     = $source->getClientOriginalExtension();
                $fileNameNonHaveExtensionS      = \App\Helpers\Charactor::convertStrToUrl($request->get('name')).'-'.Charactor::randomString(20);
                $fileUrlS                       = config('main_'.env('APP_NAME').'.google_cloud_storage.sources').$fileNameNonHaveExtensionS.'.'.$extensionS;
                /* upload source trực tiếp lên google_cloud_storage */
                Storage::disk('gcs')->put($fileUrlS, file_get_contents($source));
                /* Lưu thông tin vào CSDL */
                $idWallpaper = Wallpaper::insertItem([
                    'user_id'           => Auth::user()->id,
                    'name'              => $request->get('name'),
                    'description'       => $request->get('description') ?? null,

                    'file_name_wallpaper'   => $fileNameNonHaveExtensionW,
                    'extension_wallpaper'   => $extensionW,
                    'file_cloud_wallpaper'  => $fileUrlW,
                    'width_wallpaper'       => $widthW,
                    'height_wallpaper'      => $heightW,
                    'file_size_wallpaper'   => $fileSizeW,
                    'mine_type_wallpaper'   => $miniTypeW,
                    
                    'file_name_source'      => $fileNameNonHaveExtensionS,
                    'extension_source'      => $extensionS,
                    'file_cloud_source'     => $fileUrlS,
                    'width_source'          => $widthS,
                    'height_source'         => $heightS,
                    'file_size_source'      => $fileSizeS,
                    'mine_type_source'      => $miniTypeS

                ]);
                DB::commit();
                if(!empty($idWallpaper)){
                    $response = [];
                    $infoWallpaper = Wallpaper::select('*')
                                        ->where('id', $idWallpaper)
                                        ->first();
                    $response['id'] = $idWallpaper;
                    $response['content'] = view('admin.wallpaper.oneRow', ['item' => $infoWallpaper])->render();
                    return json_encode($response);
                }
                return true;
            }
        } catch (\Exception $exception){
            DB::rollBack();
            return '';
        }
    }

    public function changeWallpaperWithSource(Request $request){
        // try {
        //     DB::beginTransaction();
            $extensionDefault   = config('image.extension');
            $wallpaper          = $request->file('files.wallpaper');
            $source             = $request->file('files.source');
            $idWallpaper        = $request->get('wallpaper_id');
            $infoWallpaper      = Wallpaper::select('*')
                                    ->where('id', $idWallpaper)
                                    ->first();
            $fileName           = pathinfo($infoWallpaper->file_name)['filename'];
            /* trường hợp có thay đổi wallpaper */
            if(!empty($wallpaper)){
                $extensionWallpaper     = $wallpaper->getClientOriginalExtension();
                $fileNameFull           = $fileName.'.'.$extensionWallpaper;
                /* xóa wallpaper trong storage */
                $extensionDefault       = config('image.extension');
                $wallpaperPathInStorage = Storage::path(config('image.folder_upload').$fileName.'.'.$extensionDefault);
                if(file_exists($wallpaperPathInStorage)) unlink($wallpaperPathInStorage);
                /* xóa ảnh wallpaper mini trong storage */
                $filenameNotExtension   = pathinfo($infoWallpaper->file_name)['filename'];
                $wallpaperMiniPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-mini.'.$extensionDefault);
                if(file_exists($wallpaperMiniPathInStorage)) unlink($wallpaperMiniPathInStorage);
                /* xóa ảnh wallpaper small trong storage */
                $wallpaperSmallPathInStorage = Storage::path(config('image.folder_upload').$filenameNotExtension.'-small.'.$extensionDefault);
                if(file_exists($wallpaperSmallPathInStorage)) unlink($wallpaperSmallPathInStorage);
                /* xóa wallpaper trong google_cloud_storage */
                $folderW                    = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $fileUrlW                   = $folderW.$fileNameFull;
                Storage::disk('gcs')->delete($fileUrlW);
                /* upload lại wallpaper mới */
                \App\Helpers\Upload::uploadWallpaper($wallpaper, $fileName.'.'.$extensionDefault, config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers'));
                Storage::disk('gcs')->put($fileUrlW, file_get_contents($wallpaper));
            }
            /* trường hợp có thay đổi source */
            if(!empty($source)){
                $extensionSource    = $source->getClientOriginalExtension();
                $fileNameS          = $fileName.'.'.$extensionSource;
                /* xóa wallpaper trong google_cloud_storage */
                $fileUrlS           = config('main_'.env('APP_NAME').'.google_cloud_storage.sources').$fileNameFull;
                Storage::disk('gcs')->delete($fileUrlS);
                /* upload lại source mới */
                Storage::disk('gcs')->put($fileUrlS, file_get_contents($source));
            }
            /* cập nhật cơ sở dữ liệu */
            $imageInfo  = getimagesize($wallpaper);
            $width      = $imageInfo[0];
            $height     = $imageInfo[1];
            $fileSize   = filesize($wallpaper);
            Wallpaper::updateItem($idWallpaper, [
                'name'              => $request->get('name'),
                'description'       => $request->get('description') ?? null,
                'width'             => $width,
                'height'            => $height,
                'file_size'         => $fileSize,
                'extension'         => $extensionWallpaper,
                'mime_type'         => $imageInfo['mime']
            ]);
        //     DB::commit();
            return true;
        // } catch (\Exception $exception){
        //     DB::rollBack();
        //     return false;
        // }
    }

    public function deleteWallpaperAndSource(Request $request){
        $flag                       = false;
        if(!empty($request->get('id'))){
            $idWallpaper            = $request->get('id');
            $infoWallpaper          = Wallpaper::select('*')
                                        ->where('id', $idWallpaper)
                                        ->first();
            $flag                   = self::delete($infoWallpaper);
            
            /* xóa hết tát cả relation của wallpaper này => tránh lỗi hệ thống ==== chỉ xóa khi xóa hẳn ảnh trong riêng function delete này */
            RelationProductPriceWallpaperInfo::select('*')
                ->where('wallpaper_info_id', $idWallpaper)
                ->delete();
            /* xóa trong cơ sở dữ liệu */
            $infoWallpaper->delete();
        }
        return $flag;
    }

    private static function delete($infoWallpaper){
        $flag   = false;
        if(!empty($infoWallpaper)){
            /* xóa wallpaper trong google_cloud_storage */
            Storage::disk('gcs')->delete($infoWallpaper->file_cloud_wallpaper);
            /* xóa wallpaper Large trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers').$infoWallpaper->file_name_wallpaper.'-large.'.$infoWallpaper->extension_wallpaper);
            /* xóa wallpaper Small trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers').$infoWallpaper->file_name_wallpaper.'-small.'.$infoWallpaper->extension_wallpaper);
            /* xóa wallpaper Mini trong google_cloud_storage */
            Storage::disk('gcs')->delete(config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers').$infoWallpaper->file_name_wallpaper.'-mini.'.$infoWallpaper->extension_wallpaper);
            /* xóa source trong google_cloud_storage */
            Storage::disk('gcs')->delete($infoWallpaper->file_cloud_source);
            $flag = true;
        }
        return $flag;
    }

    public function loadModalUploadAndEdit(Request $request){
        $wallpaper      = null;
        if(!empty($request->get('wallpaper_id'))){
            $wallpaper  = Wallpaper::select('*')
                            ->where('id', $request->get('wallpaper_id'))
                            ->first();
        }
        $result         = view('admin.wallpaper.formModalUploadAndEdit', compact('wallpaper'))->render();
        echo $result;
    }

    public function searchWallpapers(Request $request){
        $response           = '';
        if(!empty($request->get('key_search'))&&!empty($request->get('product_price_id'))){
            $language       = $request->get('language');
            $wallpapers     = Wallpaper::select('*')
                                ->where('name', 'like', '%'.$request->get('key_search').'%')
                                ->orderBy('price_uses_count', 'ASC')
                                ->orderBy('id', 'DESC')
                                ->withCount('priceUses') // Số lượng phần tử trong priceUses trả ra tên biến trong collection price_uses_count
                                ->get();
            $relations      = RelationProductPriceWallpaperInfo::select('*')
                                ->where('product_price_id', $request->get('product_price_id'))
                                ->get();
            foreach($wallpapers as $wallpaper){
                /* check có tồn tại chưa */
                $selected   = false;
                foreach($relations as $relation){
                    if($wallpaper->id==$relation->wallpaper_info_id){
                        $selected = true;
                        break;
                    }
                }   
                /* trả kết quả */
                $response   .= view('admin.product.oneRowSearchWallpaper', [
                    'wallpaper'         => $wallpaper,
                    'idProductPrice'    => $request->get('product_price_id'),
                    'selected'          => $selected
                ])->render();
            }
        }
        if(empty($response)) $response = '<div class="searchViewBefore_selectbox_item">'.config('data_language_1.'.$language.'.no_suitable_results_found').'</div>';
        echo $response;
    }
}
