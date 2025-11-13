@if(!empty($item))
<tr id="oneItem-{{ $item->id }}">
    <td class="text-center">{{ ($loop->index + 1) }}</td>
    <td>
        <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($item->file_cloud) }}" style="width:100%;height:100%;object-fit:cover;" />
    </td>
    <td>
        <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;">
            {{ $item->name ?? $item->seo->title ?? null }}</span>
        </div>
        @if(!empty($item->seo))
            <div class="oneLine">
                <strong>Tiêu đề Seo:</strong> {{ $item->seo->seo_title }}
            </div>
        @endif
        @if(!empty($item->seo))
            <div class="oneLine">
                <strong>Mô tả Seo:</strong> {{ $item->seo->seo_description }}
            </div>
        @endif
        @if(!empty($item->seo))
            <div class="oneLine">
                <strong>Dường dẫn tĩnh:</strong> {{ $item->seo->slug_full }}
            </div>
        @endif
    </td>
    <td>
        @if(!empty($item->categories)&&$item->categories->isNotEmpty())
            <div class="onLine" style="margin-top:0.25rem;">
                @php
                    $xhtmlCategory      = null;
                    foreach($item->categories as $category){
                        if(!empty($category->infoCategory->seo->type)&&$category->infoCategory->seo->type=='category_info'){
                            $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;margin-top:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                        }
                    }
                @endphp 
                <strong>Chủ đề:</strong> {!! $xhtmlCategory ?? '...' !!}
            </div>
        @endif
        @if(!empty($item->categories)&&$item->categories->isNotEmpty())
            <div class="onLine" style="margin-top:0.25rem;">
                @php
                    $xhtmlCategory      = null;
                    foreach($item->categories as $category){
                        if(!empty($category->infoCategory->seo->type)&&$category->infoCategory->seo->type=='style_info'){
                            $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;margin-top:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                        }
                    }
                @endphp 
                <strong>Phong cách:</strong> {!! $xhtmlCategory ?? '...' !!}
            </div>
        @endif
        @if(!empty($item->categories)&&$item->categories->isNotEmpty())
            <div class="onLine" style="margin-top:0.25rem;">
                @php
                    $xhtmlCategory      = null;
                    foreach($item->categories as $category){
                        if(!empty($category->infoCategory->seo->type)&&$category->infoCategory->seo->type=='event_info'){
                            $xhtmlCategory  .= '<div class="badge bg-primary" style="margin-left:0.25rem;margin-top:0.25rem;">'.$category->infoCategory->seo->title.'</div>';
                        }
                    }
                @endphp 
                <strong>Sự kiện:</strong> {!! $xhtmlCategory ?? '...' !!}
            </div>
        @endif
        @if(!empty($item->tags)&&$item->tags->isNotEmpty())
            <div class="onLine" style="margin-top:0.25rem;">
                @php
                    $xhtmlTag      = null;
                    foreach($item->tags as $tag){
                        if(!empty($tag->infoTag)){
                            $xhtmlTag  .= '<div class="badge bg-secondary" style="margin-left:0.25rem;margin-top:0.25rem;">'.$tag->infoTag->seo->title.'</div>';
                        }
                    }
                @endphp 
                <strong>Tags:</strong> {!! $xhtmlTag ?? '...' !!}
            </div>
        @endif
    </td>
    <td>
        <div class="actionBoxOfList">
            <a href="/{{ $item->seo->slug_full ?? null }}" target="_blank">
                <i class="fa-solid fa-eye"></i>
                <div>Xem</div>
            </a>
            <a href="{{ route('admin.seoFreeWallpaper.view', ['language' => 'vi', 'id' => $item->id]) }}">
                <i class="fa-solid fa-pen-to-square"></i>
                <div>Sửa</div>
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
</tr><tr>
    <td colspan="5"> 
        @include('admin.template.languageBox', [
            'item'      => $item,
            'routeName' => 'admin.seoFreeWallpaper.view',
        ])
    </td>
</tr>
@endif