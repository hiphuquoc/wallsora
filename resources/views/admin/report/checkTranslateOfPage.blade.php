@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Gợi Ý Sửa Lỗi</div>
@include('admin.report.searchCheckTranslateOfPage', compact('list'))
<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered tableCheckTranslateOfPage" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th class="text-center" style="width:180px;">Thông tin</th>
                    <th class="text-center">Bản dịch cũ</th>
                    <th class="text-center">Gợi ý sửa</th>
                    <th class="text-center" width="200px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        @include('admin.report.rowCheckTranslateOfPage', [
                            'item'  => $item,
                            'no'    => $loop->index+1
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

<!-- action box sidebar -->
<div class="actionFixedSidebarBox">
    <div class="actionFixedSidebarBox_item" onclick="updatePageCheckTranslateOfPage();">
        <i class="fa-solid fa-file-pen"></i>
        <div>Lưu bản đã chọn</div>
    </div>
</div>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.translate.delete') }}",
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

        function updatePageCheckTranslateOfPage() {
            Swal.fire({
                title: 'Xác nhận hành động',
                html: '<div>Hành động này sẽ tiến hành update lại tất cả những giá trị được chọn.</div>',
                preConfirm: () => {
                    const checkedValues = {};
                    document.querySelectorAll('input[type="radio"][name^="update["]:checked').forEach((radio) => {
                        const seoId = radio.name.match(/\[(\d+)\]/)[1]; // Lấy seo_id từ name="update[seo_id]"
                        checkedValues[seoId] = radio.value;
                    });

                    if (Object.keys(checkedValues).length === 0) {
                        Swal.showValidationMessage("Vui lòng chọn ít nhất một tùy chọn.");
                        return false;
                    }

                    return checkedValues;
                },
                showLoaderOnConfirm: true,
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    console.log("Dữ liệu gửi đi:", result.value); // Kiểm tra dữ liệu trước khi gửi

                    // Gọi Fetch API
                    fetch("{{ route('admin.checkTranslateOfPage.updatePageCheckTranslateOfPage') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ data: result.value })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Có lỗi xảy ra khi gửi yêu cầu!");
                        }
                        return response.json();
                    })
                    .then(data => {
                        createToast(data.toast_type, data.toast_title, data.toast_message);

                        const successArray = Array.isArray(data.array_success) ? data.array_success : [];
                        const notUpdateArray = Array.isArray(data.array_not_update) ? data.array_not_update : [];

                        // Xóa các hàng trong array_success và array_not_update nếu chúng là mảng
                        [...successArray, ...notUpdateArray].forEach(id => {
                            const row = document.querySelector(`#js_updatePageCheckTranslateOfPage_${id}`);
                            if (row) row.remove();
                        });
                    })
                    .catch(error => {
                        console.error("Lỗi:", error);
                        createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
                    });
                }
            });
        }

        function reCheckTranslateOfPage(idSeo, language) {
            // Gọi Fetch API
            fetch("{{ route('admin.checkTranslateOfPage.reCheckTranslateOfPage') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    seo_id : idSeo,
                    language
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Có lỗi xảy ra khi gửi yêu cầu!");
                }
                return response.json();
            })
            .then(data => {
                createToast(data.toast_type, data.toast_title, data.toast_message);
                if(data.flag){
                    const row = document.querySelector(`#js_updatePageCheckTranslateOfPage_${idSeo}`);
                    if (row) row.remove();
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
            });
        }
        
    </script>
@endpush