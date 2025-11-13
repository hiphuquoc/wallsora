@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh sách yêu cầu</div>

<!-- MESSAGE -->
@include('admin.template.messageAction')

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th></th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>API</th>
                    <th>Loại</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        <tr id="item_{{ $item->id }}">
                            <td class="text-center">{{ ($loop->index + 1) }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->password }}</td>
                            <td>{{ $item->api }}</td>
                            <td>{{ $item->type }}</td>
                            <td style="display:flex;align-item:center;justify-content:center;">
                                <div class="form-check form-check-primary form-switch">
                                    @php
                                        $disabled = $item->type=='gpt-3.5-turbo-1106' ? 'disabled' : '';
                                    @endphp
                                    <input type="checkbox" class="form-check-input" value="{{ $item->id }}" {{ $item->status==1 ? 'checked' : '' }} onclick="changeApiActive(this);" style="cursor:pointer;" {{ $disabled }}>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="5">Không có dữ liệu phù hợp!</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    {{-- <!-- Pagination -->
    {{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }} --}}
</div>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">

        function changeApiActive(input){
            var params              = {};
            params['id']            = $(input).val();
            params['is_checked']    = $(input).prop('checked');
            const queryParams = new URLSearchParams(params).toString();
            fetch('{{ route("admin.apiai.changeApiActive") }}?' + queryParams, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if(data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }

    </script>
@endpush