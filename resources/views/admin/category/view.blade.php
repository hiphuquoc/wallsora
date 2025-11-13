@extends('layouts.admin')
@section('content')

    @php
        $titlePage      = 'Thêm Category mới';
        $submit         = 'admin.category.createAndUpdate';
        if(!empty($type)&&$type=='edit'){
            $titlePage  = 'Chỉnh sửa Category';
        }
    @endphp
    <!-- Start: backgroun để chặn thao tác khi đang dịch content ngầm -->
    @include('admin.category.lock')
    <!-- End: backgroun để chặn thao tác khi đang dịch content ngầm -->
    <form id="formAction" class="needs-validation invalid" action="{{ route($submit) }}" method="POST" novalidate enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="seo_id" name="seo_id" value="{{ $itemSeo->id ?? 0 }}" />
    <input type="hidden" id="seo_id_vi" name="seo_id_vi" value="{{ !empty($item->seo->id)&&$type!='copy' ? $item->seo->id : 0 }}" />
    <input type="hidden" id="category_info_id" name="category_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : 0 }}" />
    <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
    <input type="hidden" id="type" name="type" value="{{ $type }}" />
        <div class="pageAdminWithRightSidebar withRightSidebar">
            <div class="pageAdminWithRightSidebar_header" style="z-index:1000;position:relative;">
                <div style="width:100%;margin-bottom:10px;">{{ $titlePage }}</div>
                @include('admin.template.languageBox', [
                    'item' => $item,
                    'language' => $language,
                    'routeName' => 'admin.category.view',
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

                                @include('admin.category.formPage', [
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
                    <!-- action -->
                    @include('admin.form.buttonAction', [
                        'routeBack' => 'admin.category.list',
                    ])
                    <!-- action support -->
                    <div class="customScrollBar-y">
                        <!-- Form Upload -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formImage')
                        </div>
                        {{-- <!-- Form Slider -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formSlider')
                        </div> --}}
                        <!-- Form Gallery -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.category.formGallery')
                        </div>
                    </div>
                </div>
                <!-- END:: Sidebar content -->
            </div>
        </div>
    </form>    
@endsection
@push('modal')
    <!-- modal chọn thumnail -->
    @include('admin.category.formSearchThumnail')
    <!-- modal chọn thumnail -->
    @include('admin.form.formModalChooseLanguageBeforeDeletePage')
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            loadFreeWallpaperOfCategory('{{ $item->id ?? 0 }}');
            $('.repeater').repeater();
        });

        function loadFreeWallpaperOfCategory(idCategory){
            $.ajax({
                url         : "{{ route('admin.category.loadFreeWallpaperOfCategory') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'    : '{{ csrf_token() }}',
                    category_info_id : idCategory 
                }
            }).done(function(response){
                $('#js_loadFreeWallpaperOfCategory_idWrite').html(response);
            });
        }

        function seachFreeWallpaperOfCategory(idCategory){
            $.ajax({
                url         : "{{ route('admin.category.seachFreeWallpaperOfCategory') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'    : '{{ csrf_token() }}',
                    category_info_id : idCategory 
                }
            }).done(function(response){
                $('#js_seachFreeWallpaperOfCategory_idWrite').html(response);
            });
        }

    </script>
@endpush