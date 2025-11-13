<div id="messageModal" class="modalBox">
    <div class="modalBox_bg js_toggleMessageModal"></div>
    <div class="modalBox_box">
        @include('main.modal.contentMessageModal', [
            'title' => $title ?? null,
            'content'   => $content ?? null
        ])
    </div>
</div>
@pushonce('scriptCustom')
    <script type="text/javascript">
        $('.js_toggleMessageModal').on('click', function(){
            openCloseModal('messageModal');
        })
        /* set content messgae Modal ajax */
        function setMessageModal(title = null, content = null) {
            const queryParams = new URLSearchParams({
                title: title,
                content: content
            }).toString();

            fetch("{{ route('ajax.setMessageModal') }}?" + queryParams, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                $('#messageModal .modalBox_box').html(data);
                openCloseModal('messageModal');
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
    </script>
@endpushonce