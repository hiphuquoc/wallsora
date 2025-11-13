@if(!empty($dataFaq))
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                @foreach($dataFaq as $index => $faq)
                    {
                        "@type": "Question",
                        "name": {!! json_encode($faq['question'], JSON_UNESCAPED_UNICODE) !!},
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": {!! json_encode($faq['answer'], JSON_UNESCAPED_UNICODE) !!}
                        }
                    }@if(!$loop->last),@endif
                @endforeach
            ]
        }
    </script>
@endif