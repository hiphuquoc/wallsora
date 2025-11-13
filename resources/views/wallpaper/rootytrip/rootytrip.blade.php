<div style="font-family: arial;">
    @if(!empty($arrayData))
        @php
            $null = "không xác định";
            $ok     = '<span style="color:#00bd7d;">Ok</span>';
            $error  = '<span style="color:red;">Error</span>';
        @endphp
        <h2>{{ $arrayData['url'] ?? $null }}</h2>
        <table border="1" style="display:table;box-sizing:border-box;border-collapse: collapse;font-size:14px;">
            <tbody>
                <tr>
                    <td style="padding:5px;" width="100px;">Url</td>
                    <td style="padding:5px;">{{ $arrayData['url'] ?? $null }}</td>
                </tr>
                <tr>
                    <td style="padding:5px;" width="140px;">Meta Title</td>
                    <td style="padding:5px;">{{ $arrayData['title'] ?? $null }}</td>
                </tr>
                <tr>
                    <td style="padding:5px;" width="140px;">Meta Description</td>
                    <td style="padding:5px;">{{ $arrayData['description'] ?? $null }}</td>
                </tr>
                <tr>
                    <td style="padding:5px;" width="140px;">Thẻ Heading</td>
                    <td style="padding:5px;">
                        
                        <!-- table -->
                        <table style="display:table;box-sizing:border-box;border-collapse: collapse;font-size:14px;">
                            <thead>
                                <tr>
                                    <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Loại</td>
                                    <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Nội dung</td>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($arrayData['headings'] as $heading)
                                    @if(!empty($heading['level']))
                                        <tr>
                                            <td style="padding:5px 10px;width:200px;">{{ $heading['level'] }}</td>
                                            <td style="padding:5px 10px;">{{ $heading['text'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            <tbody>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td style="padding:5px;" width="140px;">Ảnh trong bài</td>
                    <td style="padding:5px;">
                        
                        <!-- table -->
                        <table style="display:table;box-sizing:border-box;border-collapse: collapse;font-size:14px;">
                            <thead>
                                <tr>
                                    <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Src</td>
                                    <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Status</td>
                                    <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Alt</td>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($arrayData['images_incontent'] as $img)
                                    <tr>
                                        <td style="padding:5px 10px;">{{ $img['src'] }}</td>
                                        <td style="padding:5px 10px;">{!! $img['check']==true ? $ok : $error !!}</td>
                                        <td style="padding:5px 10px;width:500px;">{{ $img['alt'] }}</td>
                                    </tr>
                                @endforeach
                            <tbody>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td style="padding:5px;" width="140px;">Link trong bài</td>
                    <td style="padding:5px;">
                        <!-- table -->
                        <table style="display:table;box-sizing:border-box;border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Href</td>
                                        <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Status</td>
                                    <th style="text-align:center;padding:5px 10px;background:#e1e1e1;">Anchor text</td>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($arrayData['links'] as $link)
                                    <tr>
                                        <td style="padding:5px 10px;">{{ $link['href'] }}</td>
                                        <td style="padding:5px 10px;">{!! $link['error']==false ? $ok : $error !!}</td>
                                        <td style="padding:5px 10px;width:500px;">{{ $link['anchor_text'] }}</td>
                                    </tr>
                                @endforeach
                            <tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div>=====================================</div>

    @endif
</div>