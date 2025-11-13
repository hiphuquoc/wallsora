<!-- form modal chọn wallpaper -->
{{-- <form id="formSearchThumnails" method="POST" action="#">
    @csrf --}}
    <div class="modal fade" id="modalViewProductCopied" tabindex="-1" aria-labelledby="addNewCardTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin:0 auto;max-width:90%;">
            <div class="modal-content">
                <!-- Modal header với nút thoát -->
                <div type="button" class="btnCloseCustom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <div class="modal-body" style="margin-bottom:70px;">

                    <table class="table table-bordered" style="min-width:900px;">
                        <tbody id="js_searchProductCopied_idWrite">
                            <!-- ajax -->
                        </tbody>
                    </table>

                    <div class="modalAction">
                        <div class="btn btn-primary waves-effect waves-float waves-light" onclick="updateProductCopied();">
                            <i class="fa-solid fa-print"></i>Cập nhật
                        </div>
                    </div>

                </div>
                

                
            </div>
            
        </div>
    </div>
{{-- </form> --}}
@pushonce('scriptCustom')
    <script type="text/javascript">

        function searchProductCopied(){
            const idSeo        = $('#seo_id').val();
            $.ajax({
                url: "{{ route('admin.product.searchProductCopied') }}",
                type: "post",
                dataType: 'html',
                data: {
                    id_seo : idSeo,
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                $('#js_searchProductCopied_idWrite').html(response);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }

        function updateProductCopied(){
            const idSeo        = $('#seo_id').val();
            /* đóng modal + bật loading */
            $('#modalViewProductCopied').modal('hide');
            openCloseFullLoading();
            $.ajax({
                url: "{{ route('admin.product.updateProductCopied') }}",
                type: "post",
                dataType: 'html',
                data: {
                    id_seo : idSeo,
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                location.reload();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
            
        }

    </script>
@endpushonce