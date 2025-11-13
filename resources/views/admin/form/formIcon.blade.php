<!-- One Row -->
<div class="formBox_full_item">
    <span data-toggle="tooltip" data-placement="top" title="
        Đây là icon dùng hiển thị bên cạnh tên Danh mục
    ">
        <i class="explainInput" data-feather='alert-circle'></i>
        <label class="form-label inputRequired" for="icon">Icon đại diện <span style="font-weight:700;">100*100 px</span></label>
    </span>
    <input class="form-control" type="file" id="icon" name="icon" onchange="readURL(this, 'imageIcon');" />
    <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
    <div class="imageUpload">
        @if(!empty($item->icon)&&file_exists(Storage::path($item->icon))&&$type!='copy')
            @php
                $imagePath  = Storage::path($item->icon);
                $infoPixel  = getimagesize($imagePath);
                $extension  = pathinfo($imagePath)['extension'];
                $infoSize   = round(filesize($imagePath)/1024, 2);
            @endphp
            <img id="imageIcon" src="{{ Storage::url($item->icon) }}?{{ time() }}" />
            <div style="margin-top:0.25rem;color:#789;display:flex;justify-content:space-between;">
                <span>.{{ $extension }}</span>
                <span>{{ $infoPixel[0] }}*{{ $infoPixel[1] }} px</span>
                <span>{{ $infoSize }} MB</span>
            </div>
        @else
            <img id="imageIcon" src="{{ config('image.default') }}" />
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