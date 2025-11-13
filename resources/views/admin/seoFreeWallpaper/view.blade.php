@extends('layouts.admin')
@section('content')

    @php
        $titlePage      = 'Thêm Category mới';
        $submit         = 'admin.seoFreeWallpaper.createAndUpdate';
        if(!empty($type)&&$type=='edit'){
            $titlePage  = 'Chỉnh sửa Category';
        }
    @endphp

    <form id="formAction" class="needs-validation invalid" action="{{ route($submit) }}" method="POST" novalidate enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="seo_id" name="seo_id" value="{{ $itemSeo->id ?? 0 }}" />
    <input type="hidden" id="seo_id_vi" name="seo_id_vi" value="{{ !empty($item->seo->id)&&$type!='copy' ? $item->seo->id : 0 }}" />
    <input type="hidden" id="free_wallpaper_info_id" name="free_wallpaper_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : 0 }}" />
    <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />
        <div class="pageAdminWithRightSidebar withRightSidebar">

            <div class="pageAdminWithRightSidebar_header" style="z-index:1000;position:relative;">
                <div style="width:100%;margin-bottom:10px;">{{ $titlePage }}</div>
                @include('admin.template.languageBox', [
                    'item' => $item,
                    'language' => $language,
                    'routeName' => 'admin.seoFreeWallpaper.view',
                ])
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
                                <h4 class="card-title">Thông tin trang</h4>
                            </div>
                            <div class="card-body">

                                @include('admin.seoFreeWallpaper.formPage', [
                                    'item'              => !empty($itemSourceToCopy) ? $itemSourceToCopy : $item,
                                    'itemSeo'           => !empty($itemSeoSourceToCopy) ? $itemSeoSourceToCopy : $itemSeo,
                                    'flagCopySource'    => !empty($itemSeoSourceToCopy) ? true : false,
                                ])

                            </div>
                        </div>
                    </div>
                    <div class="pageAdminWithRightSidebar_main_content_item">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Thông tin SEO</h4>
                            </div>
                            <div class="card-body">

                                @include('admin.form.formSeo', [
                                    'item'              => !empty($itemSourceToCopy) ? $itemSourceToCopy : $item,
                                    'itemSeo'           => !empty($itemSeoSourceToCopy) ? $itemSeoSourceToCopy : $itemSeo,
                                    'flagCopySource'    => !empty($itemSeoSourceToCopy) ? true : false,
                                    'idSeoSource'       => $itemSeoSourceToCopy->id ?? 0
                                ])
                                
                            </div>
                        </div>
                    </div>
                    <!-- nội dung -->
                    @include('admin.form.formFilterContent')
                </div>
                <!-- END:: Main content -->

                <!-- START:: Sidebar content -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar">
                    {{-- <!-- Button Save -->
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
                        @if(!empty($itemSeo->slug_full))
                            <a href="/{{ $itemSeo->slug_full }}" target="_blank" style="font-size:1.4rem;"><i class="fa-regular fa-eye"></i></a>
                        @endif
                        <a href="{{ route('admin.seoFreeWallpaper.list') }}" type="button" class="btn btn-secondary waves-effect waves-float waves-light">Quay lại</a>
                        <button type="submit" class="btn btn-success waves-effect waves-float waves-light" aria-label="Lưu">Lưu</button>
                    </div>
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
                        <div class="btn btn-success waves-effect waves-float waves-light" aria-label="Lưu" style="width:100%;" onclick="submitForm('formAction', { 'index_google': true });">Lưu & Index</div>
                    </div>
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                        <div class="actionBox">
                            @if($language=='vi')
                                <div class="actionBox_item maxLine_1" onClick="callAI('auto_content_for_image')">
                                    <i class="fa-solid fa-robot"></i>Auto Content
                                </div>
                            @else   
                                <div class="actionBox_item maxLine_1" onClick="callAI('translate_content')">
                                    <i class="fa-solid fa-language"></i>Dịch Content
                                </div>
                            @endif
                            @if(!empty($itemSeo->link_canonical))
                                <a href="{{ URL::current().'?id='.$item->id.'&language='.$language.'&id_seo_source='.$itemSeo->link_canonical }}" class="actionBox_item maxLine_1">
                                    <i class="fa-solid fa-file-import"></i>Copy từ trang gốc
                                </a>
                            @endif
                        </div>
                    </div> --}}
                    @include('admin.form.buttonAction', [
                        'routeBack' => 'admin.seoFreeWallpaper.list',
                    ])
                    <div class="customScrollBar-y">
                        <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($item->file_cloud) }}" />
                    </div>
                </div>
                <!-- END:: Sidebar content -->
            </div>
        </div>
    </form>    
@endsection
@push('modal')
    <!-- modal chọn thumnail -->
    @include('admin.form.formModalChooseLanguageBeforeDeletePage')
@endpush
@push('scriptCustom')
    <script type="text/javascript">

        $('.repeater').repeater();

    </script>
@endpush