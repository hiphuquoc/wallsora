<!-- One Row -->
<div class="formBox_full_item">
    <span data-toggle="tooltip" data-placement="top" title="
        Đây là Ảnh đại diện dùng làm Ảnh đại diện trên website, Ảnh đại diện ngoài Google, Ảnh đại diện khi Share link
    ">
        <i class="explainInput" data-feather='alert-circle'></i>
        <label class="form-label inputRequired" for="image">Ảnh đại diện 800*533px</label>
    </span>
    <input class="form-control" type="file" id="image" name="image" onchange="readURL(this, 'imageUpload');" />
    <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
    <div class="imageUpload">
        @php
            $imageUrl       = !empty($item->seo->image) ? \App\Helpers\Image::getUrlImageCloud($item->seo->image) : null;
            $imageUrlSmall  = !empty($item->seo->image) ? \App\Helpers\Image::getUrlImageSmallByUrlImage($item->seo->image) : null;
            $response       = !empty($imageUrl) ? Http::get($imageUrl) : null;
        @endphp
        @if(!empty($imageUrl)&&$response->ok()&&$type!='copy')
            @php
                $size = getimagesize($imageUrl);
                $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
                $width = $size[0];
                $height = $size[1];
                $mime = $size['mime'];
                $fileSize = $response->header('content-length');
                $fileSizeKB = round($fileSize / 1024, 0);
            @endphp
            <img id="imageUpload" src="{{ $imageUrlSmall }}?{{ time() }}" />
            <div style="margin-top:0.25rem;color:#789;display:flex;justify-content:space-between;">
                <span>.{{ $extension }}</span>
                <span>{{ $width }}*{{ $height }} px</span>
                <span>{{ $fileSizeKB }} KB</span>
            </div>
        @else
            <img id="imageUpload" src="{{ config('image.default') }}" style="aspect-ratio:800/533;" />
        @endif
    </div>
</div>

@pushonce('scriptCustom')
    <script type="text/javascript">
        function readURL(input, idShow) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#'+idShow).attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

    </script>
@endpushonce