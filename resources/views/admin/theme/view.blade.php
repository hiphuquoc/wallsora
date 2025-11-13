@extends('layouts.admin')
@section('content')

@php
    $titlePage      = 'Thêm Giao diện mới';
    $submit         = 'admin.theme.create';
    $checkImage     = 'required';
    if(!empty($type)&&$type=='edit'){
        $titlePage  = 'Chỉnh sửa Giao diện';
        $submit     = 'admin.theme.update';
        $checkImage = null;
    }
@endphp

<form id="formAction" class="needs-validation invalid" action="{{ route($submit) }}" method="POST" novalidate enctype="multipart/form-data">
@csrf
<div class="pageAdminWithRightSidebar withRightSidebar">
    <div class="pageAdminWithRightSidebar_header">
        {{ $titlePage }}
    </div>
    <!-- Error -->
    @if ($errors->any())
        <ul class="errorList">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <!-- MESSAGE -->
    @include('admin.template.messageAction')
    
    <div class="pageAdminWithRightSidebar_main">
        <!-- START:: Main content -->
        <div class="pageAdminWithRightSidebar_main_content">
            <div class="pageAdminWithRightSidebar_main_content_item">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Thông tin</h4>
                    </div>
                    <div class="card-body">

                        <input type="hidden" id="theme_info_id" name="theme_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : null }}" />
                        <div class="formBox">
                            <div class="formBox_full">
                                <!-- One Row -->
                                <div class="formBox_column2_item_row">
                                    <label class="form-label inputRequired" for="name">Tiêu đề Theme</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $item->name ?? null }}" required>
                                    <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
                                </div>
                                <!-- One Row -->
                                <div class="formBox_full_item">
                                    <label class="form-label inputRequired" for="type">Loại Theme</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="white">Bản sáng</option>
                                    </select>
                                </div>
                                <!-- One Row -->
                                <div class="formBox_column2_item_row">
                                    <div class="form-check form-check-success">
                                        <input type="checkbox" class="form-check-input" id="status" name="status" {{ !empty($item->status) ? 'checked' : null }} onClick="return false;">
                                        <label class="form-check-label" for="status">Kích hoạt</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="pageAdminWithRightSidebar_main_content_item">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Bảng màu</h4>
                    </div>
                    <div class="card-body">

                        <div class="viewColorBox">
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu chính 1:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorLv1" name="colors[colorLv1]" value="{{ $item->colors->colorLv1 }}" />
                                    <label for="colorLv1">{{ $item->colors->colorLv1 }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu chính 2:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorLv2" name="colors[colorLv2]" value="{{ $item->colors->colorLv2 }}" />
                                    <label for="colorLv2">{{ $item->colors->colorLv2 }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu phụ 1:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorSLv1" name="colors[colorSLv1]" value="{{ $item->colors->colorSLv1 }}" />
                                    <label for="colorSLv1">{{ $item->colors->colorSLv1 }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu phụ 2:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorSLv2" name="colors[colorSLv2]" value="{{ $item->colors->colorSLv2 }}" />
                                    <label for="colorSLv2">{{ $item->colors->colorSLv2 }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Nút nhấn chính 1:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorButtonLv1" name="colors[colorButtonLv1]" value="{{ $item->colors->colorButtonLv1 }}" />
                                    <label for="colorButtonLv1">{{ $item->colors->colorButtonLv1 }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Nút nhấn chính 2:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorButtonLv2" name="colors[colorButtonLv2]" value="{{ $item->colors->colorButtonLv2 }}" />
                                    <label for="colorButtonLv2">{{ $item->colors->colorButtonLv2 }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu chữ tối:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorText" name="colors[colorText]" value="{{ $item->colors->colorText }}" />
                                    <label for="colorText">{{ $item->colors->colorText }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu chữ sáng:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorTextLight" name="colors[colorTextLight]" value="{{ $item->colors->colorTextLight }}" />
                                    <label for="colorTextLight">{{ $item->colors->colorTextLight }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu giá:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorPrice" name="colors[colorPrice]" value="{{ $item->colors->colorPrice }}" />
                                    <label for="colorPrice">{{ $item->colors->colorPrice }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu nhãn:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorLabel" name="colors[colorLabel]" value="{{ $item->colors->colorLabel }}" />
                                    <label for="colorLabel">{{ $item->colors->colorLabel }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu sao:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorStar" name="colors[colorStar]" value="{{ $item->colors->colorStar }}" />
                                    <label for="colorStar">{{ $item->colors->colorStar }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu success:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorSuccess" name="colors[colorSuccess]" value="{{ $item->colors->colorSuccess }}" />
                                    <label for="colorSuccess">{{ $item->colors->colorSuccess }}</label>
                                </div>
                            </div>
                            <div class="viewColorBox_item">
                                <div class="viewColorBox_item_title">
                                    Màu đỏ:
                                </div>
                                <div class="viewColorBox_item_colorBox">
                                    <input type="color" id="colorRed" name="colors[colorRed]" value="{{ $item->colors->colorRed }}" />
                                    <label for="colorRed">{{ $item->colors->colorRed }}</label>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- END:: Main content -->

        <!-- START:: Sidebar content -->
        <div class="pageAdminWithRightSidebar_main_rightSidebar">
            <!-- Button Save -->
            <div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
                <a href="{{ route('admin.theme.list') }}" type="button" class="btn btn-secondary waves-effect waves-float waves-light">Quay lại</a>
                <button type="submit" class="btn btn-success waves-effect waves-float waves-light" onClick="javascript:submitForm('formAction');" style="width:100px;" aria-label="Lưu">Lưu</button>
            </div>
            {{-- <div class="customScrollBar-y" style="height: calc(100% - 70px);border-top: 1px dashed #adb5bd;">
                <!-- Form Upload -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                    @include('admin.form.formImage')
                </div>
                <!-- Form Slider -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                    @include('admin.form.formSlider')
                </div>
            </div> --}}
        </div>
        <!-- END:: Sidebar content -->
    </div>
</div>
</form>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        

    </script>
@endpush