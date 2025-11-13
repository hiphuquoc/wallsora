@if(!empty($thumnail->infoFreewallpaper))
    <div id="js_removeThumnailsOfCategory_{{ $thumnail->infoFreewallpaper->id }}">
        <img id="galleryUpload" src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($thumnail->infoFreewallpaper->file_cloud) }}" />
        <i class="fa-solid fa-circle-xmark" onclick="removeThumnailsOfCategory({{ $thumnail->infoFreewallpaper->id }}, {{ $thumnail->category_info_id }});"></i>
    </div>
@endif