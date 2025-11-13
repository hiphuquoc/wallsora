<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\Category;
use App\Models\RelationSeoCategoryInfo;
use App\Models\Tag;
use App\Models\RelationSeoTagInfo;
use App\Models\Product;
use App\Models\RelationSeoProductInfo;
use App\Models\Page;
use App\Models\RelationSeoPageInfo;
use App\Models\SeoContent;
use App\Models\JobAutoTranslate;
use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\HelperController;
use App\Models\JobAutoTranslateLinks;
use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;

class reUploadWallpaper implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $urlImage;
    private $filename;
    private $folderUpload;
    public  $tries = 5; // Số lần thử lại

    public function __construct($urlImage, $filename, $folderUpload){
        $this->urlImage     = $urlImage;
        $this->filename     = $filename;
        $this->folderUpload = $folderUpload;

    }

    public function handle(){
        try {
            // Lấy nội dung ảnh từ URL
            $imageContent = file_get_contents($this->urlImage);

            if ($imageContent === false) {
                return null; // Nếu không lấy được ảnh, trả về null
            }

            // Tạo đối tượng ảnh từ nội dung lấy được
            $imageTmp = ImageManagerStatic::make($imageContent);

            // ===== set filename & checkexists
            $filenameNotExtension = pathinfo($this->filename)['filename'];
            $extension = pathinfo($this->filename)['extension'];
            $fileUrl = $this->folderUpload . $this->filename;
            $gcsDisk = Storage::disk('gcs');

            // Tính toán kích thước ảnh
            $percentPixel = $imageTmp->width() / $imageTmp->height();
            $widthImage = $imageTmp->width();
            $heightImage = $imageTmp->height();

            // Lưu ảnh chính
            $gcsDisk->put($fileUrl, $imageTmp->encode($extension, config('image.quality'))->resize($widthImage, $heightImage)->stream());
            $result = $fileUrl;

            // Resize và lưu các kích thước khác
            // Large image
            $fileUrlLarge = $this->folderUpload . $filenameNotExtension . '-large.' . $extension;
            $widthImageLarge = config('image.resize_large_width');
            $heightImageLarge = $widthImageLarge / $percentPixel;
            $gcsDisk->put($fileUrlLarge, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageLarge, $heightImageLarge)->stream());

            // Small image
            $fileUrlSmall = $this->folderUpload . $filenameNotExtension . '-small.' . $extension;
            $widthImageSmall = config('image.resize_small_width');
            $heightImageSmall = $widthImageSmall / $percentPixel;
            $gcsDisk->put($fileUrlSmall, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageSmall, $heightImageSmall)->stream());

            // Mini image
            $fileUrlMini = $this->folderUpload . $filenameNotExtension . '-mini.' . $extension;
            $widthImageMini = config('image.resize_mini_width');
            $heightImageMini = $widthImageMini / $percentPixel;
            $gcsDisk->put($fileUrlMini, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageMini, $heightImageMini)->stream());
        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }
}
