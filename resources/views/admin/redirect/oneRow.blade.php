@if(!empty($item))
@php
    $no = $no ?? 0;
@endphp
<tr id="oneItem-{{ $item->id }}">
    <td style="font-weight:700;text-align:center;">
        {{ $no }}
    </td>
    <td style="vertical-align:top;">
        <a href="{{ $item->old_url }}" target="_blank">{{ $item->old_url }}</a>
    </td>
    <td style="vertical-align:top;">
        <a href="{{ $item->new_url }}" target="_blank">{{ $item->new_url }}</a>
    </td>
    <td style="vertical-align:top;display:flex;">
        <div class="icon-wrapper iconAction">
            <a href="#" class="actionDelete" onClick="deleteItem('{{ $item->id }}');">
                <i data-feather='x-square'></i>
                <div>Xóa</div>
            </a>
        </div>
    </td>
</tr>
@endif