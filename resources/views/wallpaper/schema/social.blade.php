@php
    $title              = $itemSeo->seo_title ?? $item->seo->seo_title ?? null;
    $description        = $itemSeo->seo_description ?? $item->seo->seo_description ?? null;
    /* đoạn này xử lý trương hợp lỗi do $itemSeo->source->slug_full rỗng */
    $url = !empty($itemSeo->link_canonical) 
    ? ($itemSeo->source ? $itemSeo->source->slug_full : $itemSeo->slug_full) 
    : $itemSeo->slug_full;
    $urlFull            = !empty($url) ? env('APP_URL').'/'.$url : env('APP_URL');
    /* image */
    $imagePage          = config('image.default');
    if(!empty($item->seo->image)) $imagePage = \App\Helpers\Image::getUrlImageCloud($item->seo->image);
    /* author */
    $author             = $itemSeo->rating_author_name ?? $item->seo->rating_author_name ?? config('main_'.env('APP_NAME').'.author_name');
    /* lấy giá theo ngôn ngữ */
    $tmp            = \App\Helpers\Number::getPriceByLanguage($lowPrice, $language);
    $lowPrice       = $tmp['number'];
    $currency       = $tmp['currency'];
    $tmp            = \App\Helpers\Number::getPriceByLanguage($highPrice, $language);
    $highPrice      = $tmp['number'];
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}" />
<link rel="canonical" href="{{ $urlFull==env('APP_URL').'/vi' ? env('APP_URL') : $urlFull }}" />
<meta property="og:locale" content="vi_VN" />
<meta property="article:published_time" content="{{ date('c', strtotime($itemSeo->created_at)) }}" />
<meta property="article:modified_time" content="{{ date('c', strtotime($itemSeo->updated_at)) }}" />
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:image" content="{{ $imagePage }}" />
<meta property="og:image:secure_url" content="{{ $imagePage }}" />
<meta property="og:image:type" content="image/webp" />
<meta property="og:image:width" content="800" />
<meta property="og:image:height" content="533" />
<meta property="og:image:alt" content="{{ $title }}" />
<meta property="og:url" content="{{ $urlFull }}" />
<meta property="og:site_name" content="{{ ucfirst(env('APP_NAME')) }}" />
<meta property="og:type" content="website" />
@if(!empty($lowPrice)&&!empty($highPrice))
    @if($lowPrice>=$highPrice)
        <meta property="og:price:amount" content="{{ $lowPrice }}" />
    @else 
        <meta property="og:price:amount:minimum" content="{{ $lowPrice }}" />
        <meta property="og:price:amount:maximum" content="{{ $highPrice }}" />
    @endif
    <meta property="og:price:currency" content="{{ $currency }}" />
@endif
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $title }}" />
<meta name="twitter:description" content="{{ $description }}" />
<meta name="twitter:creator" content="{{ $author }}" />
<meta name="twitter:image" content="{{ $imagePage }}" />