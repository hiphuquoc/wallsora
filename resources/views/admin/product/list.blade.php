@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Sản Phẩm</div>

@include('admin.product.search', compact('list', 'categories'))

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th class="text-center">Thông tin</th>
                    <th class="text-center" width="800px">Wallpapers</th>
                    <th class="text-center" width="200px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        @php
                            $no = $loop->index + 1;
                        @endphp
                        @include('admin.product.row', [
                            'item'  => $item,
                            'no'    => $no,
                        ])
                    @endforeach
                @else
                    <tr><td colspan="5">Không có dữ liệu phù hợp!</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    {{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }}
</div>

<!-- Nút thêm -->
<a href="{{ route('admin.product.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.product.delete') }}",
                    type        : "GET",
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