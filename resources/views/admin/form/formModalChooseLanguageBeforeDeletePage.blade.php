<!-- form modal chọn wallpaper -->
<form id="formChooseLanguageBeforeDeletePage" method="POST" action="#">
    @csrf
    <div class="modal fade" id="modalChooseLanguageBeforeDeletePage" tabindex="-1" aria-labelledby="addNewCardTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin:0 auto;">
            <div class="modal-content">
                <div class="modal-body">
                    <!-- check all -->
                    <div style="display:flex;margin-bottom:0.75rem;border-bottom:1px dashed #333;padding-bottom:0.75rem;">
                        <input type="checkbox" class="form-check-input" id="language_check_all" name="language_check_all" checked />
                        <label class="form-check-label maxLine_1" for="language_check_all" style="margin-left:0.5rem;">Chọn tất cả</label>
                    </div>
                    <!-- danh sách ngôn ngữ -->
                    <div class="languageBox">
                        @foreach($item->seos as $s)
                            @if(!empty($s->infoSeo)&&$s->infoSeo->language!='vi')
                                @php
                                    $keyName = 'language_check_'.$s->infoSeo->language;
                                @endphp
                                <div class="languageBox_item" style="border:none;">
                                    <input type="checkbox" class="form-check-input" id="{{ $keyName }}" name="language_check_{{ $s->infoSeo->language }}" value="{{ $s->infoSeo->language }}" checked />
                                    <label class="form-check-label maxLine_1" for="{{ $keyName }}" style="margin-left:0.5rem;">{{ $s->infoSeo->language }}</label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <!-- nút nhấn -->
                    <div class="swal2-actions">
                        <div class="swal2-loader"></div>
                        <button type="button" class="swal2-confirm swal2-styled" onclick="deleteLanguage();">Xác nhận</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

@pushonce('scriptCustom')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            $('#language_check_all').on('change', function() {
                // Lấy trạng thái checked của checkbox "all"
                var isChecked = $(this).is(':checked');
                
                // Duyệt qua tất cả các checkbox có name bắt đầu bằng "language_check_", ngoại trừ checkbox "all"
                $('input[type="checkbox"][name^="language_check_"]').not('[name="language_check_all"]').each(function() {
                    $(this).prop('checked', isChecked);
                });
            });
        });

        function deleteLanguage(){
            /* đóng modal + bật loading */
            $('#modalChooseLanguageBeforeDeletePage').modal('hide');
            openCloseFullLoading();
            /* lấy dữ liệu */
            const idSeoVi = $('#seo_id_vi').val();
            // Tạo một mảng để chứa các giá trị đã được chọn
            let selectedLanguages = [];
            // Duyệt qua tất cả các checkbox có name bắt đầu bằng "language_check_" và đã được checked
            $('input[type="checkbox"][name^="language_check_"]:checked').each(function() {
                selectedLanguages.push($(this).val());
            });
            $.ajax({
                url         : "{{ route('admin.helper.deleteLanguage') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'    : '{{ csrf_token() }}',
                    id_seo_vi   : idSeoVi,
                    languages   : selectedLanguages, 
                }
            }).done(function(response){
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
        }

    </script>
@endpushonce