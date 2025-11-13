@php
    $titlePage      = 'Thêm Category mới';
    $submit         = 'admin.order.create';
    $checkImage     = 'required';
    if(!empty($type)&&$type=='edit'){
        $titlePage  = 'Chỉnh sửa Category';
        $submit     = 'admin.order.update';
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
                            <h4 class="card-title">Thông tin trang</h4>
                        </div>
                        <div class="card-body">

                            @include('admin.order.formPage')

                        </div>
                    </div>
                </div>
                <div class="pageAdminWithRightSidebar_main_content_item">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Thông tin SEO</h4>
                        </div>
                        <div class="card-body">

                            @include('admin.form.formSeo')
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- END:: Main content -->

            <!-- START:: Sidebar content -->
            <div class="pageAdminWithRightSidebar_main_rightSidebar">
                <!-- Button Save -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
                    <a href="{{ route('admin.order.list') }}" type="button" class="btn btn-secondary waves-effect waves-float waves-light">Quay lại</a>
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light" onClick="javascript:submitForm('formAction');" style="width:100px;" aria-label="Lưu">Lưu</button>
                </div>
                <div class="customScrollBar-y" style="height: calc(100% - 90px);">
                    <!-- Form Upload -->
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                        @include('admin.form.formImage')
                    </div>
                    <!-- Form Slider -->
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                        @include('admin.form.formSlider')
                    </div>
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

@push('scriptCustom')
    <script type="text/javascript">
        $('.repeater').repeater();

    </script>
@endpush