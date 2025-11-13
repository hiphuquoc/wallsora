@if(!empty($item))
    @php
        $color = config('main_'.env('APP_NAME').'.status_check_translate_of_page.'.$item->status.'.color');
        $style = !empty($color) ? 'style="background:'.$color.';"' : ''; 
    @endphp
    <tr id="js_updatePageCheckTranslateOfPage_{{ $item->seo_id }}" {!! $style !!}>
        <td>{{ $no }}</td>
        <td>
            <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#009e69;">
                {{ $item->title_vi ?? null }} 
            </div>
            <div class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#009e69;">
                {{ $item->title_en ?? null }} 
            </div>
            <div class="oneLine">
                <strong>Ngôn ngữ</strong>: {{ $item->language ?? null }}
            </div>
            <div class="oneLine">
                <strong>Loại</strong>: {{ $item->type ?? null }}
            </div>
        </td>
        <td>
            <label for="title_{{ $item->seo_id }}">
                @php
                    $keySearch  = '';
                    $tmp        = explode(' ', $item->title);
                    $keySearch  = implode('+', $tmp);
                    $translate  = '('.$item->title_google_translate_vi.' - '.$item->title_google_translate_en.')';
                @endphp 
                <a href="https://www.google.com/search?q={{ $keySearch }}&hl={{ $item->language }}" target="_blank" class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#e67112;">
                    {{ $item->title ?? null }} - {{ $translate }}
                </a>
                <div class="oneLine">
                    {{ $item->seo_title ?? null }}
                </div>
                <div class="oneLine">
                    {{ $item->seo_description ?? null }}
                </div>
                <!-- input -->
                <input type="radio" id="title_{{ $item->seo_id }}" name="update[{{ $item->seo_id }}]" value="old" />
            </label>
        </td>
        <td>
            <label for="new_title_{{ $item->seo_id }}">
                @php
                    $keySearch  = '';
                    $tmp        = explode(' ', $item->new_title);
                    $keySearch  = implode('+', $tmp);
                    $translate  = '('.$item->new_title_google_translate_vi.' - '.$item->new_title_google_translate_en.')';
                @endphp 
                <a href="https://www.google.com/search?q={{ $keySearch }}&hl={{ $item->language }}" target="_blank" class="oneLine" style="font-size:1.2rem;font-weight:bold;margin-bottom:1rem;color:#009e69;">
                    {{ $item->new_title ?? null }} - {{ $translate }}
                </a>
                <div class="oneLine">
                    {{ $item->new_seo_title ?? null }}
                </div>
                <div class="oneLine">
                    {{ $item->new_seo_description ?? null }}
                </div>
                <!-- input -->
                <input type="radio" id="new_title_{{ $item->seo_id }}" name="update[{{ $item->seo_id }}]" value="new" />
            </label>
        </td>
        <td>
            <div class="actionBoxOfList">
                {{-- <div onclick="">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <div>Sửa</div>
                </div> --}}
                <div class="actionCheck" onclick="reCheckTranslateOfPage({{ $item->seo_id }}, '{{ $item->language }}');">
                    <i class="fa-solid fa-repeat"></i>
                    <div>Lại</div>
                </div>
                {{-- <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                    <i class="fa-solid fa-trash"></i>
                    <div>Xóa</div>
                </div> --}}
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