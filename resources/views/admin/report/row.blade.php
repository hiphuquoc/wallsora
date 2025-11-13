@if(!empty($item))
    <tr id="oneItem-{{ $item->id }}">
        <td>{{ $no }}</td>
        @php
            $urlImage = config('image.default');
            if(!empty($item->image)) $urlImage = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->image);
        @endphp
        {{-- <td class="text-center"><img src="{!! $urlImage.'?v='.time() !!}" style="width:100%;aspect-ratio:800/533;object-fit:cover;font-family:'SVN-Gilroy Bold',sans-serif;" /></td> --}}
        <td>
            @php
                $countBox = !empty($item->jobAutoTranslate) ? $item->jobAutoTranslate->count() : 0;
            @endphp
            {{-- <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;">
                <img src="" style="width:35px;margin-right:5px;" /> {{ $item->title ?? null }} <span style="font-weight:normal;font-size:16px;color:#28c76f;">({{ $countBox }} box)</span>
            </div> --}}
            <div class="oneLine">
                <strong>Tiêu đề SEO</strong>: {{ $item->seo_title ?? null }} (<strong>{{ !empty($item->seo_title) ? mb_strlen($item->seo_title) : 0 }}</strong>)
            </div>
            <div class="oneLine">
                <strong>Mô tả SEO</strong>: {{ $item->seo_description ?? null }} (<strong>{{ !empty($item->seo_description) ? mb_strlen($item->seo_description) : 0 }}</strong>)
            </div>
            <div class="oneLine">
                <strong>Đường dẫn</strong>: {{ $item->slug_full ?? null }}
            </div>
        </td>
        <td class="text-center">
            @foreach($item->jobAutoTranslate as $l)
                <div class="oneLine" {{ $loop->index>0 ? 'style="border-top:1px dashed #333;padding-top:5px;"' : '' }}>
                    {!! $l->status==1 ? '<span style="color:#28c76f;">Thành công</span>' : '<span>Đang chờ...</span>' !!}
                </div>
            @endforeach
        </td>
        <td>
            @foreach($item->jobAutoTranslateLinks as $l)
                <div class="oneLine" {{ $loop->index>0 ? 'style="border-top:1px dashed #333;padding-top:5px;"' : '' }}>{{ ($loop->index+1) }}. <a target="_blank" href="{{ $l->link_translate }}">{{ $l->link_translate }}</a></div>
            @endforeach
        </td>
        <td>
            <div class="actionBoxOfList">
                <a href="/{{ $item->slug_full ?? null }}" target="_blank">
                    <i class="fa-solid fa-eye"></i>
                    <div>Xem</div>
                </a>
                <div onclick="redirectEdit({{ $item->id }}, '{{ $item->language }}');">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <div>Sửa</div>
                </div>
                <div class="actionCheck" onClick="reRequestTranslate({{ $item->id }}, '{{ $item->language }}');">
                    <i class="fa-solid fa-repeat"></i>
                    <div>Lại</div>
                </div>
                <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                    <i class="fa-solid fa-trash"></i>
                    <div>Xóa</div>
                </div>
                
            </div>
        </td>
    </tr>
    <!-- nếu có phần tử con => viết tiếp -->
    @if(!empty($item->childs)&&$item->childs->isNotEmpty())
        @foreach($item->childs as $child)
            @include('admin.category.row', [
                'item'  => $child,
                'no'    => $no.'.'.($loop->index+1)
            ])
        @endforeach
    @endif
@endif

@pushonce('scriptCustom')
    <script type="text/javascript">
        function reRequestTranslate(idSeo, language){
            $.ajax({
                url         : "{{ route('admin.translate.reRequestTranslate') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'        : '{{ csrf_token() }}', 
                    id_seo : idSeo,
                    language
                }
            }).done(function(data){
                $('#oneItem-'+idSeo).remove();
            });
        }
        function redirectEdit(idSeoByLanguage, language){
            $.ajax({
                url         : "{{ route('admin.translate.redirectEdit') }}",
                type        : "get",
                dataType    : "json", // Đảm bảo dataType là json
                data        : {
                    id_seo_by_language : idSeoByLanguage,
                    language,
                }
            }).done(function(response){
                // Mở tab mới với URL trả về từ server
                window.open(response.url, '_blank');
            });
        }
    </script>
@endpushonce