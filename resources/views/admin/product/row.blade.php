<!-- in ra item cha -->
@php
    $flagCopy = !empty($item->seo->link_canonical) ? 'style="background: repeating-linear-gradient(
            45deg,
            rgba(255, 165, 0, 0.15),
            rgba(255, 165, 0, 0.15) 5px,
            transparent 10px,
            transparent 30px);"' : '';
@endphp
<tr id="oneItem-{{ $item->id }}" {!! $flagCopy !!}>
    <td class="text-center">{{ $no }}</td>
    <td style="display:flex;flex-direction:column;">
        
        <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:0.5rem;">
            {{ $item->name ?? $item->seo->title ?? null }} (<span style="font-weight:bold;color:#E74C3C;">{{ $item->code }}</span>)
        </div>
        <div class="oneLine">
            <strong>Tiêu đề Seo:</strong> {{ $item->seo->seo_title }}
        </div>
        <div class="oneLine">
            <strong>Mô tả Seo:</strong> {{ $item->seo->seo_description }}
        </div>
        <div class="oneLine">
            <strong>Dường dẫn tĩnh:</strong> {{ $item->seo->slug_full }}
        </div>
        <div class="oneLine">
            <strong>Giá trọn bộ:</strong> <span style="color:red;font-weight:bold;font-size:1.3rem;">{{ $item->price }}</span>
        </div>
        
        @if(!empty($item->categories)&&$item->categories->isNotEmpty())
            <div class="oneLine">
                @php
                    $xhtmlCategory      = null;
                    foreach($item->categories as $category){
                        if($category->infoCategory->seo->type=='category_info'){
                            $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                        }
                    }
                @endphp 
                <strong>Chủ đề:</strong> {!! $xhtmlCategory !!}
            </div>
            <div class="oneLine">
                @php
                    $xhtmlCategory      = null;
                    foreach($item->categories as $category){
                        if($category->infoCategory->seo->type=='style_info'){
                            $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                        }
                    }
                @endphp 
                <strong>Phong cách:</strong> {!! $xhtmlCategory !!}
            </div>
            <div class="oneLine">
                @php
                    $xhtmlCategory      = null;
                    foreach($item->categories as $category){
                        if($category->infoCategory->seo->type=='event_info'){
                            $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                        }
                    }
                @endphp 
                <strong>Sự kiện:</strong> {!! $xhtmlCategory !!}
            </div>
        @endif
    </td>
    <td>
        <div class="priceProductBox">
            @foreach($item->prices as $price)
                <div class="priceProductBox_item">
                    <div class="priceProductBox_item_image">
                        @php
                            /* lấy ảnh Small */
                            $imageSmall = config('image.default');
                            if(!empty($price->wallpapers[0]->infoWallpaper->file_cloud_wallpaper)) $imageSmall  = \App\Helpers\Image::getUrlImageSmallByUrlImage($price->wallpapers[0]->infoWallpaper->file_cloud_wallpaper);
                        @endphp     
                        <img src="{{ $imageSmall }}" />
                        @if($price->wallpapers->count()>1)
                            <div class="priceProductBox_item_image_count">
                                {{ $price->wallpapers->count() }}<i class="fa-regular fa-image"></i>
                            </div>
                        @endif
                        <div class="priceProductBox_item_image_price">
                            {{ number_format($price->price) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </td>
    <td>
        <div class="actionBoxOfList">
            <a href="/{{ $item->seo->slug_full ?? null }}" target="_blank">
                <i class="fa-solid fa-eye"></i>
                <div>Xem</div>
            </a>
            <a href="{{ route('admin.product.view', ['language' => 'vi', 'id' => $item->id]) }}">
                <i class="fa-solid fa-pen-to-square"></i>
                <div>Sửa</div>
            </a>
            <a href="{{ route('admin.product.view', ['id' => $item->id, 'language' => 'vi', 'type' => 'copy']) }}">
                <i class="fa-solid fa-copy"></i>
                <div>Chép</div>
            </a>
            {{-- <div class="actionWiki" onclick="">
                <i class="fa-solid fa-book"></i>
                <div>Wiki</div>
            </div> --}}
            <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                <i class="fa-solid fa-trash"></i>
                <div>Xóa</div>
            </div>
        </div>
    </td>
</tr>
<tr id="oneItemSub-{{ $item->id }}">
    <td colspan="4">
        @include('admin.template.languageBox', [
            'item' => $item,
            'routeName' => 'admin.product.view',
        ])
    </td>
</tr>