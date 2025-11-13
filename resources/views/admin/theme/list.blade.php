@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh sách Theme</div>

@foreach($themes as $theme)
<div class="card">
    <div class="themeListBox">
        @php
            $active         = null;
            $actionActive   = '<div class="icon-wrapper iconAction">
                                    <a href="'.route('admin.theme.setColor', ['id' => $theme->id]).'">
                                        <i class="fa-solid fa-check"></i>
                                        <div>Active</div>
                                    </a>
                                </div>';
            if($theme->status==1) {
                $active         = '<span class="themeListBox_title_status">Đang hoạt động</span>';
                $actionActive   = null;
            }
        @endphp
        <div class="themeListBox_title">
            <div>{{ $theme->name ?? null }} {!! $active !!}</div> 
            <div class="themeListBox_title_action">
                {!! $actionActive !!}
                <div class="icon-wrapper iconAction">
                    <a href="{{ route('admin.theme.view', ['id' => $theme->id, 'type' => 'edit'])}}">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <div>Sửa</div>
                    </a>
                </div>
                <div class="icon-wrapper iconAction">
                    <a href="{{ route('admin.theme.view', ['id' => $theme->id, 'type' => 'copy'])}}">
                        <i class="fa-regular fa-paste"></i>
                        <div>Chép</div>
                    </a>
                </div>
            </div>
        </div>
        <div class="themeListBox_color">
            <!-- cột 1: màu chính - phụ -->
            <div class="themeListBox_color_column">
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu chính 1:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorLv1 }};">
                        {{ $theme->colors->colorLv1 }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu chính 2:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorLv2 }};">
                        {{ $theme->colors->colorLv2 }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu phụ 1:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorSLv1 }};">
                        {{ $theme->colors->colorSLv1 }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu phụ 2:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorSLv2 }};">
                        {{ $theme->colors->colorSLv2 }}
                    </div>
                </div>
            </div>
            <!-- cột 2: màu button -->
            <div class="themeListBox_color_column">
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Nút nhấn chính 1:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorButtonLv1 }};">
                        {{ $theme->colors->colorButtonLv1 }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Nút nhấn chính 2:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorButtonLv2 }};">
                        {{ $theme->colors->colorButtonLv2 }}
                    </div>
                </div>
            </div>
            <!-- cột 3: màu chữ -->
            <div class="themeListBox_color_column">
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu chữ tối:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorText }};">
                        {{ $theme->colors->colorText }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu chữ sáng:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorTextLight }};">
                        {{ $theme->colors->colorTextLight }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu giá:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorPrice }};">
                        {{ $theme->colors->colorPrice }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu nhãn:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorLabel }};">
                        {{ $theme->colors->colorLabel }}
                    </div>
                </div>
            </div>
            <!-- cột 4: màu khác -->
            <div class="themeListBox_color_column">
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu sao:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorStar }};">
                        {{ $theme->colors->colorStar }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu success:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorSuccess }};">
                        {{ $theme->colors->colorSuccess }}
                    </div>
                </div>
                <div class="themeListBox_color_column_item">
                    <div class="themeListBox_color_column_item_title">
                        Màu đỏ:
                    </div>
                    <div class="themeListBox_color_column_item_colorBox" style="background:{{ $theme->colors->colorRed }};">
                        {{ $theme->colors->colorRed }}
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endforeach
    
@endsection
@push('scripts-custom')
    <script type="text/javascript">
    
    </script>
@endpush