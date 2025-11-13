@php
    $nameHome   = config('data_language_1.'.$language.'.home');
    $xhtml      = '{
                    "@type": "ListItem",
                    "position": 1,
                    "name": "'.$nameHome.'",
                    "item": "'.env('APP_URL').'/'.$language.'"
                }';
    $i          = 2;
    foreach($breadcrumb as $b){
        $xhtml .= ', ';
        $title  = $b->title ?? $b->seo_title;
        $slug   = $b->slug_full ?? null;
        $xhtml .= '{
                        "@type": "ListItem",
                        "position": '.$i.',
                        "name": "'.$title.'",
                        "item": "'.env('APP_URL').'/'.$slug.'"
                    }';
        ++$i;
    }
@endphp
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {!! $xhtml !!}
        ]
    }
</script>
