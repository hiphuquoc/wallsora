@php
    $title  = $itemSeo->seo_title ?? $item->seo->seo_title ?? null;
@endphp
<script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "CreativeWorkSeries",
        "name": "{{ $title }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $itemSeo->rating_aggregate_star ?? '5' }}",
            "bestRating": "5",
            "ratingCount": "{{ $itemSeo->rating_aggregate_count ?? '120' }}"
        }
    }
</script>