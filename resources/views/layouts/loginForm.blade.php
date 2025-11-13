<!DOCTYPE html>
<html lang="vi">

<!-- === START:: Head === -->
<head>
    @include('wallpaper.snippets.head', ['index' => 'no'])
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body>
    <div class="blurBackground" style="background:url('https://previews.123rf.com/images/monsitj/monsitj1610/monsitj161000047/65641646-financial-stock-market-graph-on-technology-abstract-background.jpg') no-repeat;background-size:100% 100%;width:100%;height:100%;position:fixed;z-index:-1;"></div>
    
    <!-- Modal -->
    @include('admin.login.form')
    <!-- === START:: Scripts Default === -->
    @include('wallpaper.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
</body>
<!-- === END:: Body === -->

</html>