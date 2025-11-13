<!-- filter box -->
<form id="formViewBy" action="{{ url()->current().'?'.http_build_query(request()->query()) }}" method="GET">
   @include('wallpaper.categoryBlog.sortContent', [
      'language'          => $language ?? 'vi',
      'total'             => $total,
      'categories'        => $categories ?? null
   ])
</form>

@pushonce('scriptCustom')
    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function() {
         showSortBoxInCategoryTag();
      });

      /* tải box bộ lọc trang trả phí */
      function showSortBoxInCategoryTag() {
         const id = "{{ $item->id ?? 0 }}";
         const language = "{{ $language ?? '' }}";
         // Lấy chuỗi query parameters từ URL
         var queryString = window.location.search;
         
         // Tạo một đối tượng URLSearchParams từ chuỗi query parameters
         var urlParams = new URLSearchParams(queryString);

         // Lấy tất cả các tham số truyền qua URL
         var params = {};
         for (const [key, value] of urlParams) {
               params[key] = value;
         }

         // Thêm các giá trị id và total vào params
         params['id'] = id;
         params['language'] = language;
         const queryParams = new URLSearchParams(params).toString();

         fetch("/showSortBoxInCategoryTag?" + queryParams, {
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
               $('#formViewBy').html(data);
         })
         .catch(error => {
               console.error("Fetch request failed:", error);
         });
      }
    </script>
@endpushonce