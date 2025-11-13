@if(!empty($item))
    <tr id="oneItem-{{ $item->id }}">
        <td>{{ $no }}</td>
        @php
            $urlImage = config('image.default');
            if(!empty($item->seo->image)) $urlImage = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->seo->image);
        @endphp
        <td class="text-center"><img src="{!! $urlImage.'?v='.time() !!}" style="width:100%;aspect-ratio:800/533;object-fit:cover;font-family:'SVN-Gilroy Bold',sans-serif;" /></td>
        <td>
            <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;">
                {{ $item->seo->title ?? null }}
            </div>
            <div class="oneLine">
                <strong>Tiêu đề SEO</strong>: {{ $item->seo->seo_title ?? null }}
            </div>
            <div class="oneLine maxLine_3">
                <strong>Mô tả SEO</strong>: {{ $item->seo->seo_description ?? null }}
            </div>
            <div class="oneLine">
                <strong>Đường dẫn</strong>: {{ $item->seo->slug_full ?? null }}
            </div>
        </td>
        <td>
            <div class="oneLine">
                Đánh giá: <span class="highLight_700">{{ $item->seo->rating_aggregate_star }}</span> sao / <span class="highLight_700">{{ $item->seo->rating_aggregate_count }}</span>
            </div>
            <div id="js_updateNotes_notes_{{ $item->seo->id }}" class="oneLine maxLine_7">
                @if(!empty($item->notes))
                    Ghi chú: {{ $item->notes }}
                @endif
            </div>
        </td>
        <td>
            <div class="actionBoxOfList">
                <a href="/{{ $item->seo->slug_full ?? null }}" target="_blank">
                    <i class="fa-solid fa-eye"></i>
                    <div>Xem</div>
                </a>
                <a href="{{ route('admin.'.$typeRoute.'.view', ['language' => 'vi', 'id' => $item->id]) }}">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <div>Sửa</div>
                </a>
                <a href="{{ route('admin.'.$typeRoute.'.view', ['id' => $item->id, 'language' => 'vi', 'type' => 'copy']) }}">
                    <i class="fa-solid fa-copy"></i>
                    <div>Chép</div>
                </a>
                <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                    <i class="fa-solid fa-trash"></i>
                    <div>Xóa</div>
                </div>
                @php
                    $keySearch  = '';
                    $tmp        = explode(' ', $item->seo->title);
                    $tmp[]      = 'wiki';
                    $keySearch  = implode('+', $tmp);
                @endphp 
                <a href="https://www.google.com/search?q={{ $keySearch }}" target="_blank" class="actionSearch">
                    <i class="fa-brands fa-google"></i>
                    <div>Search</div>
                </a>
                <div class="actionWiki" onclick="updateNotes({{ $item->seo->id }});">
                    <i class="fa-solid fa-book"></i>
                    <div>Wiki</div>
                </div>
                <div class="actionCheck" onclick="checkTranslateOfPage({{ $item->seo->id }});">
                    <i class="fa-solid fa-check"></i>
                    <div>Check</div>
                </div>
            </div>
        </td>
    </tr>
    <tr id="oneItemSub-{{ $item->id }}">
        <td colspan="5"> 
            @include('admin.template.languageBox', [
                'item' => $item,
                'routeName' => 'admin.'.$typeRoute.'.view',
            ])
        </td>
    </tr>
    <!-- nếu có phần tử con => viết tiếp -->
    @if(!empty($item->childs)&&$item->childs->isNotEmpty())
        @foreach($item->childs as $child)
            @include('admin.category.row', [
                'item'  => $child,
                'no'    => $no.'.'.($loop->index+1),
                'typeRoute' => $typeRoute,
            ])
        @endforeach
    @endif
@endif