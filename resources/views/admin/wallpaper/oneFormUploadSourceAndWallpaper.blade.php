<div> <!-- thẻ div để không bị display:block đè -->
    <div id="{{ $idBox }}" class="formWallpaperBox_gallery_item">
        @php
            /* background source */
            $backgroundSource       = null;
            if(!empty($wallpaper->file_name)){
                $url                = config('main_'.env('APP_NAME').'.google_cloud_storage.sources').$wallpaper->file_name;
                $size               = 400;
                $response           = config('admin.images.default_750x460');
                $contentImage       = Storage::disk('gcs')->get($url);
                if(!empty($contentImage)){
                    $thumbnail      = \Intervention\Image\ImageManagerStatic::make($contentImage)->resize($size, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode();
                }
                $base64Image        = 'data:image/jpeg;base64,'.base64_encode($thumbnail);
                $backgroundSource   = "background:url('".$base64Image."') no-repeat;background-size:100% 100%;";
            }
            /* background wallpaper */
            $backgroundWallpaper    = null;
            if(!empty($wallpaper->file_cloud_wallpaper)){
                $backgroundWallpaper   = "background:url('".\App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->file_cloud_wallpaper)."') no-repeat;background-size:100% 100%;";
            }
        @endphp
        <div class="formWallpaperBox_gallery_item_source" style="{{ $backgroundSource }}">
            @php
                $requiredSource = 'required';
                if(!empty($wallpaper)) $requiredSource = null;
            @endphp
            <input class="form-control" type="file" name="sources[{{ $idBox }}]" onChange="readImageWhenChoose(this);" {{ $requiredSource }} />
        </div>
        <div id="js_addFormUploadSource_wallpaper_{{ $idBox }}" class="formWallpaperBox_gallery_item_wallpaper" style="{{ $backgroundWallpaper }}">
            @if(!empty($wallpaper))
                <input class="form-control" type="file" name="wallpapers[]" onChange="readImageWhenChoose(this);" />
            @endif
        </div>
    </div>
</div>