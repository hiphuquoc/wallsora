@if(!empty($infoImageCloud))
    @php
        $urlIamgeSmall  = \App\Helpers\Image::getUrlImageSmallByUrlImage($infoImageCloud->file_cloud);
        $urlImageLarge  = \App\Helpers\Image::getUrlImageLargeByUrlImage($infoImageCloud->file_cloud);
        /* chức năng copy to clipboard */
        $idContent      = 'js_copyToClipboard_content_'.rand(0, 1000000);
    @endphp
    <div id="js_removeImage_{{ $infoImageCloud->id }}" class="imageBox_item">
        <div class="imageBox_item_image">
            <img src="{{ $urlIamgeSmall }}?{{ time() }}" />
        </div>
        <div class="imageBox_item_content">
            <div class="imageBox_item_content_text">
                <textarea id="{{ $idContent }}" cols="2" style="margin-bottom:0.5rem;background:transparent;border:none;width:100%;resize:none;" disabled>{{ $urlImageLarge }}</textarea>
                <div>width: {{ $infoImageCloud->width ?? '-' }}</div>
                <div>height: {{ $infoImageCloud->height ?? '-' }}</div>
                <div>size: {{ round($infoImageCloud->file_size/1024) }}kb</div>
            </div>
            <div class="imageBox_item_content_action">
                <!-- copy đường dẫn -->
                <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Copy ảnh" onClick="copyToClipboard('{{ $idContent }}')"><i class="fa-regular fa-copy"></i></div>
                <!-- thay ảnh -->
                <div data-bs-toggle="modal" data-bs-target="#modalImage" onClick="loadModal({{ $infoImageCloud->id }});"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>
                {{-- <!-- thay tên ảnh -->
                <div data-bs-toggle="modal" data-bs-target="#modalImage" onClick="loadModal('changeName');"><i class="fa-solid fa-pen-to-square"></i></div> --}}
                <!-- xóa ảnh -->
                <div class="remove" onClick="removeImage('{{ $infoImageCloud->id }}');"><i class="fa-solid fa-trash-can"></i></div>
            </div>
        </div>
    </div>
@endif
