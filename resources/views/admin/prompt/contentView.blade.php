@php
    $titlePage      = 'Thêm Prompt mới';
    $submit         = 'admin.prompt.createAndUpdate';
    $checkImage     = 'required';
    if(!empty($type)&&$type=='edit'){
        $titlePage  = 'Chỉnh sửa Prompt';
        $checkImage = null;
    }
@endphp

<form id="formAction" class="needs-validation invalid" action="{{ route($submit) }}" method="POST" novalidate enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="prompt_info_id" name="prompt_info_id" value="{{ !empty($item->id)&&$type!='copy' ? $item->id : null }}" />
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
                            <h4 class="card-title">Thông tin Prompt</h4>
                        </div>
                        <div class="card-body">

                            @include('admin.prompt.formPage')

                        </div>
                    </div>
                </div>
                
            </div>
            <!-- END:: Main content -->

            <!-- START:: Sidebar content -->
            <div class="pageAdminWithRightSidebar_main_rightSidebar">
                <!-- Button Save -->
                <div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
                    <a href="{{ route('admin.prompt.list') }}" type="button" class="btn btn-secondary waves-effect waves-float waves-light">Quay lại</a>
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light" onClick="javascript:submitForm('formAction');" style="width:100px;" aria-label="Lưu">Lưu</button>
                </div>
                <div class="customScrollBar-y" style="height: calc(100% - 90px);">
                    {{-- <!-- Form Upload -->
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                        @include('admin.form.formImage')
                    </div>
                    <!-- Form Slider -->
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                        @include('admin.form.formSlider')
                    </div> --}}
                    {{-- <!-- Form Gallery -->
                    <div class="pageAdminWithRightSidebar_main_rightSidebar_item">
                        @include('admin.form.formIcon')
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