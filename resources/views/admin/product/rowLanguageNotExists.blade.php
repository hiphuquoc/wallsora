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
            <div class="oneLine">
                <strong>Mô tả SEO</strong>: {{ $item->seo->seo_description ?? null }}
            </div>
            <div class="oneLine">
                <strong>Đường dẫn</strong>: {{ $item->seo->slug_full ?? null }}
            </div>
        </td>
        <td>
            <div class="oneLine">
                Đánh giá: {{ $item->seo->rating_aggregate_star }} sao / {{ $item->seo->rating_aggregate_count }}
            </div>
            <div class="oneLine">
                <i class="fa-solid fa-plus"></i>{{ date('H:i \n\g\à\y d-m-Y', strtotime($item->seo->created_at)) }}
            </div>
            <div class="oneLine">
                <i class="fa-solid fa-pencil"></i>{{ date('H:i \n\g\à\y d-m-Y', strtotime($item->seo->updated_at)) }}
            </div>
        </td>
        <td style="vertical-align:top;display:flex;font-size:0.95rem;">
            <div class="icon-wrapper iconAction">
                <a href="/{{ $item->seo->slug_full ?? null }}" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <div>Xem</div>
                </a>
            </div>
            {{-- <div class="icon-wrapper iconAction">
                <a href="{{ route('admin.'.$typeRoute.'.view', ['language' => 'vi', 'id' => $item->id]) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    <div>Sửa</div>
                </a>
            </div> --}}
            <div class="icon-wrapper iconAction">
                <a href="#" onclick="createJobTranslateAndCreatePageAjax('{{ $item->seo->slug }}', {{ $item->id }}, false);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-plus"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                    <div>Tạo</div>
                </a>
            </div>
            {{-- <div class="icon-wrapper iconAction">
                <a href="{{ route('admin.'.$typeRoute.'.view', ['id' => $item->id, 'language' => 'vi', 'type' => 'copy']) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <div>Chép</div>
                </a>
            </div>
            <div class="icon-wrapper iconAction">
                <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-square">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                    </svg>
                    <div>Xóa</div>
                </div>
            </div> --}}
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
            @include('admin.product.row', [
                'item'  => $child,
                'no'    => $no.'.'.($loop->index+1),
                'typeRoute' => $typeRoute,
            ])
        @endforeach
    @endif
@endif