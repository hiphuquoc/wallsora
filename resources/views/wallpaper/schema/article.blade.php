@php
    $title              = $itemSeo->seo_title ?? $item->seo->seo_title ?? null;
    $description        = $itemSeo->seo_description ?? $item->seo->seo_description ?? null;
    $url                = env('APP_URL').'/'.$itemSeo->slug_full ?? $item->slug_full;
    /* author */
    $author             = config('main_'.env('APP_NAME').'.author_name');
    /* image */
    $imagePage          = config('image.default');
    if(!empty($item->seo->image)) $imagePage = \App\Helpers\Image::getUrlImageCloud($item->seo->image);
    $imageAuthor        = env('APP_URL').Storage::url(config('main_'.env('APP_NAME').'.logo_main'));
    $widthImageAuthor   = 500;
    $heightImageAuthor  = 500;
    if(file_exists($imageAuthor)){
        $infoImageAuthor    = getimagesize($imageAuthor);
        $widthImageAuthor   = $infoImageAuthor[0];
        $heightImageAuthor  = $infoImageAuthor[1];
    }
@endphp

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "@id": "{{ $url }}#website",
        "inLanguage": "{{ $language }}",
        "headline": "{{ $author }} Article",
        "datePublished": "{{ !empty($item->seo->created_at) ? date('c', strtotime($item->seo->created_at)) : null }}",
        "dateModified": "{{ !empty($item->seo->updated_at) ? date('c', strtotime($item->seo->updated_at)) : null }}",
        "name": "{{ $title }}",
        "description": "{{ $description }}",
        "url": "{{ $url }}",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ $url }}"
        },
        "author":{
            "@type": "Organization",
            "name": "{{ $author }}",
            "url": "{{ env('APP_URL') }}"
        },
        "image":{
            "@type": "ImageObject",
            "url": "{{ $imagePage }}",
            "width": "800",
            "height": "500"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ $author }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ $imageAuthor }}",
                "width": "{{ $widthImageAuthor }}",
                "height": "{{ $heightImageAuthor }}"
            }
        },
        "potentialAction": {
            "@type": "ReadAction",
            "target": [
                {
                    "@type": "EntryPoint",
                    "urlTemplate": "{{ $url }}"
                }
            ]
        }
    }
</script>