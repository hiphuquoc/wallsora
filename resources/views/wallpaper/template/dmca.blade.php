<div class="socialBox_dmca">
    <a href="https://www.dmca.com/Protection/Status.aspx?ID={{ env('DMCA_ID') }}" 
       title="DMCA.com Protection Status" class="dmca-badge"> 
        <img src="https://cdn.wallsora.com/storage/images/dmca-badge-w100-2x1-02.webp?ID={{ env('DMCA_ID') }}" alt="DMCA.com Protection Status" loading="lazy" />
    </a>
    {{-- <script async src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"></script> --}}
    <script async src="{{ asset('js/DMCABadgeHelper.min.js') }}"></script>
</div>