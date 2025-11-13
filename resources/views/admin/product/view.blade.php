@extends('layouts.admin')
@section('content')

    @php
        $titlePage      = 'Thêm Sản phẩm mới';
        $submit         = 'admin.product.createAndUpdate';
        if(!empty($type)&&$type=='edit'){
            $titlePage  = 'Chỉnh sửa Sản phẩm';
        }
    @endphp
    <!-- Start: backgroun để chặn thao tác khi đang dịch content ngầm -->
    @include('admin.category.lock')
    <!-- End: backgroun để chặn thao tác khi đang dịch content ngầm -->
    <form id="formAction" class="needs-validation invalid" action="{{ route($submit) }}" method="POST" novalidate enctype="multipart/form-data">
    @csrf
        <input type="hidden" id="seo_id" name="seo_id" value="{{ $itemSeo->id ?? 0 }}" />
        <input type="hidden" id="seo_id_vi" name="seo_id_vi" value="{{ !empty($item->seo->id)&&$type!='copy' ? $item->seo->id : 0 }}" />
        <input type="hidden" id="product_info_id" name="product_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : 0 }}" />
        <input type="hidden" id="language" name="language" value="{{ $language ?? 'vi' }}" />
        <input type="hidden" id="type" name="type" value="{{ $type }}" />
        <div class="pageAdminWithRightSidebar withRightSidebar">
            <div class="pageAdminWithRightSidebar_header" style="z-index:1000;position:relative;">
                <div style="width:100%;margin-bottom:10px;">{{ $titlePage }}</div>
                @include('admin.template.languageBox', [
                    'item' => $item,
                    'language' => $language,
                    'routeName' => 'admin.product.view',
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
                <div class="pageAdminWithRightSidebar_main_content" data-repeater-list="prices">

                    <div class="pageAdminWithRightSidebar_main_content_item">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Thông tin trang</h4>
                            </div>
                            <div class="card-body">

                                @include('admin.product.formPage', [
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
                    <!-- tùy biến giá -->
                    @if($language=='vi')
                        @if(!empty($item->prices)&&$item->prices->isNotEmpty())
                            @foreach($item->prices as $price)
                                <div class="pageAdminWithRightSidebar_main_content_item" data-repeater-item>
                                    @include('admin.product.formPrice', compact('item', 'price'))
                                </div>
                            @endforeach
                        @else 
                            <div class="pageAdminWithRightSidebar_main_content_item" data-repeater-item>
                                @include('admin.product.formPrice')
                            </div>
                        @endif
                    @endif

                </div>
                
                <!-- END:: Main content -->

                <!-- START:: Sidebar content -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar">
                    <!-- action -->
                    @include('admin.form.buttonAction', [
                        'routeBack' => 'admin.product.list',
                    ])
                    <!-- action support -->
                    <div class="customScrollBar-y">
                        {{-- @if($language=='vi') --}}
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            <button class="btn btn-icon btn-primary waves-effect waves-float waves-light" type="button" aria-label="Thêm" style="width:100%;" data-repeater-create>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-25"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Thêm phiên bản SP</span>
                            </button>
                        </div>
                        {{-- @endif --}}
                        <!-- Form Upload -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formImage')
                        </div>
                        {{-- <!-- Form Slider -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formSlider')
                        </div> --}}
                        {{-- <!-- Form Gallery -->
                        <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                            @include('admin.form.formGallery')
                        </div> --}}
                        
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
    <!-- modal xem danh sách trang đã copy -->
    @include('admin.product.modalViewProductCopied')
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        $('.pageAdminWithRightSidebar_main').repeater();

        document.addEventListener('DOMContentLoaded', function() {
            @if(!empty($item->prices)&&$item->prices->isNotEmpty())
                @foreach($item->prices as $price)
                    loadWallpaperByProductPrice('{{ $price->id }}');
                @endforeach
            @endif
        });

        function loadWallpaperByProductPrice(idProductPrice){
            $.ajax({
                url         : "{{ route('admin.productPrice.loadWallpaperByProductPrice') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'    : '{{ csrf_token() }}',
                    product_price_id : idProductPrice 
                }
            }).done(function(response){
                $('#js_loadWallpaperByProductPrice_'+idProductPrice).html(response);
            });
        }

        function deleteWallpaperToProductPrice(idBox, idProductPrice, idWallpaper){
            $.ajax({
                url: "{{ route('admin.productPrice.deleteWallpaperToProductPrice') }}",
                type: 'post',
                dataType: 'json',
                data: {
                    wallpaper_id : idWallpaper,
                    product_price_id : idProductPrice
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                if(response) $('#'+idBox).remove();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }
    </script>
@endpush