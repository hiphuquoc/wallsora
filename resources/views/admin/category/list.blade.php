@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Chủ Đề</div>
<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th style="width:320px;">Ảnh</th>
                    <th class="text-center">Thông tin</th>
                    <th class="text-center" style="width:30%;">Khác</th>
                    <th class="text-center" width="200px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        @include('admin.category.row', [
                            'item'  => $item,
                            'no'    => $loop->index+1,
                            'typeRoute' => 'category',
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
                    url         : "{{ route('admin.category.delete') }}",
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