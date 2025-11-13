@if(!empty($infoImageCloud))
    @php
        $urlImageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($infoImageCloud->file_cloud);
    @endphp
    <div class="formBox">
        <div class="formBox_full">
            <!-- One Row -->
            <div class="formBox_full_item">
                <input type="hidden" id="image_cloud_id" name="image_cloud_id" value="{{ $infoImageCloud->id }}" />
                <input class="form-control" type="file" id="image_new" name="image_new" onChange="readURL(this,'js_readURL_idShow_modal');">
            </div>
            <div class="formBox_full_item">

                <div class="columnBox">
                    <div class="columnBox_item" style="width:100%;aspect-ratio:16/9;background:url('{{ Storage::url('public/images/background-of-image.jpg') }}') no-repeat;background-size:100% 100%;border-radius:0.75rem;overflow:hidden;">
                        <img src="{{ $urlImageSmall.'?'.time() }}" style="width:100%;object-fit:contain;height:100%;" />
                    </div>
                    <div class="columnBox_item" style="flex:0 0 40px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    </div>
                    <div class="columnBox_item" style="width:100%;aspect-ratio:16/9;background:url('{{ Storage::url('public/images/background-of-image.jpg') }}') no-repeat;background-size:100% 100%;border-radius:0.75rem;overflow:hidden;">
                        <img id="js_readURL_idShow_modal" src="{{ config('image.default') }}" style="width:100%;object-fit:contain;height:100%;" />
                    </div>
                </div>

            </div>
        </div>
    </div>

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
@endif