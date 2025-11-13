@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Bài Viết</div>
<!-- ===== START: SEARCH FORM ===== -->
<form id="formSearch" method="get" action="{{ route('admin.blog.list') }}">
    <div class="searchBox">
        <div class="searchBox_item">
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}">
                <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button>
            </div>
        </div>
        @if(!empty($categories))
            <div class="searchBox_item">
                <select class="form-select select2" name="search_category" onChange="submitForm('formSearch');">
                    <option value="0">- Tìm theo Chuyên mục -</option>
                    @foreach($categories as $category)
                        @php
                            $selected   = null;
                            if(!empty($params['search_category'])&&$params['search_category']==$category->id) $selected = 'selected';
                        @endphp
                        <option value="{{ $category->id }}" {{ $selected }}>{{ $category->seo->title }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="searchBox_item" style="margin-left:auto;text-align:right;">
            @php
                $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewBlogInfo', config('setting.admin_array_number_view'), $viewPerPage, $list->total());
                echo $xhtmlSettingView;
            @endphp
        </div>
    </div>
</form>
<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th style="width:320px;">Ảnh</th>
                    <th class="text-center">Thông tin</th>
                    <th class="text-center" style="width:225px;">Khác</th>
                    <th class="text-center" width="200px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        @include('admin.category.row', [
                            'item'          => $item,
                            'no'            => $loop->index+1,
                            'typeRoute'     => 'blog',
                        ])
                    @endforeach
                @else
                    <tr><td colspan="5">Không có dữ liệu phù hợp!</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    {{-- {{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }} --}}
</div>

{{-- <!-- Nút thêm -->
<a href="{{ route('admin.category.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a> --}}
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.blog.delete') }}",
                    type        : "get",
                    dataType    : "html",
                    data        : { id : id }
                }).done(function(data){
                    if(data==true) {
                        $('#oneItem-'+id).remove();
                        $('#oneItemSub-'+id).remove();
                    }
                });
            }
        }
    </script>
@endpush