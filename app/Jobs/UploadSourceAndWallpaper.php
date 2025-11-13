<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Http;
use App\Helpers\Charactor;
use App\Models\Wallpaper;

class UploadSourceAndWallpaper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $infoWallpaper;
    

    public function __construct($infoWallpaper){
        $this->infoWallpaper    = $infoWallpaper;
    }

    public function handle(){
        // $urlS                           = config('main_'.env('APP_NAME').'.google_cloud_storage.sources').$this->infoWallpaper->file_name;
        // $fullUrlS                       = config('main_'.env('APP_NAME').'.google_cloud_storage.cdn_domain').$urlS;

        // /* Lấy thông tin ảnh wallpaper */
        // $widthW                         = 800;
        // $heightW                        = 1746;
        // $miniTypeW                      = 'image/webp';
        // $extensionW                     = config('image.extension');
        // $fileNameNonHaveExtensionW      = \App\Helpers\Charactor::convertStrToUrl($this->infoWallpaper->name).'-'.Charactor::randomString(10);
        // $newUrlW                        = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers').$fileNameNonHaveExtensionW.'.'.$extensionW;
        // // /* lấy thông tin ảnh source */
        // // $widthS                         = 3072;
        // // $heightS                        = 6144;
        // // $miniTypeS                      = 'image/png';
        // // $response                       = Http::get($fullUrlS);
        // // if ($response->successful()) {
        // //     $imageData = $response->body();
        // //     $filesize = strlen($imageData);
        // // }
        // // $fileSizeS                      = $filesize ?? '0';
        // // $extensionS                     = pathinfo($urlS)['extension'];
        // // $fileNameNonHaveExtensionS      = \App\Helpers\Charactor::convertStrToUrl($this->infoWallpaper->name).'-'.Charactor::randomString(20);
        // // $newUrlS                        = config('main_'.env('APP_NAME').'.google_cloud_storage.sources').$fileNameNonHaveExtensionS.'.'.$extensionS;
        // // /* đổi tên source */
        // // Storage::disk('gcs')->move($urlS, $newUrlS);
        // $infoUpdate     = [
        //     'file_name_wallpaper'   => $fileNameNonHaveExtensionW,
        //     'extension_wallpaper'   => $extensionW,
        //     'file_cloud_wallpaper'  => $newUrlW,
        //     'width_wallpaper'       => $widthW,
        //     'height_wallpaper'      => $heightW,
        //     'file_size_wallpaper'   => $this->infoWallpaper->file_size,
        //     'mime_type_wallpaper'   => $miniTypeW,

        //     // 'file_name_source'      => $fileNameNonHaveExtensionS,
        //     // 'extension_source'      => $extensionS,
        //     // 'file_cloud_source'     => $newUrlS,
        //     // 'width_source'          => $widthS,
        //     // 'height_source'         => $heightS,
        //     // 'file_size_source'      => $fileSizeS,
        //     // 'mime_type_source'      => $miniTypeS
        // ];
        // Wallpaper::updateItem($this->infoWallpaper->id, $infoUpdate);

        // $urlW                   = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers').$this->infoWallpaper->file_name;
        // $fullUrlW               = config('main_'.env('APP_NAME').'.google_cloud_storage.cdn_domain').$urlW;
        
        // $folderTmp              = 'testwallpapers/';
        // $imageTmp               = ImageManagerStatic::make($fullUrlW);
        // $percentPixel           = $imageTmp->width() / $imageTmp->height();
        // $widthImage             = 800;
        // $heightImage            = $widthImage / $percentPixel;
    
        // /* upload ảnh wallpaper gốc */
        // $extensionDefault       = config('image.extension');
        // $fileUrl                = $folderTmp.$fileNameNonHaveExtensionW.'.'.$extensionDefault;
        // Storage::disk('gcs')->put($fileUrl, $imageTmp->encode($extensionDefault, config('image.quality'))->resize($widthImage, $heightImage)->stream());
        // /* upload ảnh wallpaper Small */
        // $widthImage             = 500;
        // $heightImage            = $widthImage / $percentPixel;
        // $fileNameSmall          = $fileNameNonHaveExtensionW.'-small.'.$extensionDefault;
        // $fileUrlSmall           = $folderTmp.$fileNameSmall;
        // Storage::disk('gcs')->put($fileUrlSmall, $imageTmp->encode($extensionDefault, config('image.quality'))->resize($widthImage, $heightImage)->stream());
        // /* upload ảnh wallpaper Mini */
        // $widthImage             = 60;
        // $heightImage            = $widthImage / $percentPixel;
        // $fileNameMini           = $fileNameNonHaveExtensionW.'-mini.'.$extensionDefault;
        // $fileUrlMini            = $folderTmp.$fileNameMini;
        // Storage::disk('gcs')->put($fileUrlMini, $imageTmp->encode($extensionDefault, config('image.quality'))->resize($widthImage, $heightImage)->stream());

    }
}
