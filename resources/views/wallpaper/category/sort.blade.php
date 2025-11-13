<!-- filter box -->
<form id="formViewBy" action="{{ url()->current().'?'.http_build_query(request()->query()) }}" method="GET">
    @include('wallpaper.category.sortContent', [
        'language'          => $language ?? 'vi',
        'total'             => $total,
        'categories'        => $categories ?? null,
        'categoryChoose'    => $categoryChoose ?? null,
        'searchFeeling'     => $searchFeeling ?? null
    ])
</form>

@pushonce('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            showSortBoxFreeWallpaper();
        });
    </script>
@endpushonce