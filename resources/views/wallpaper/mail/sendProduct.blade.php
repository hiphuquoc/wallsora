<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('data_language_1.'.$language.'.order_confirmation') }}</title> <!-- Xác nhân đơn hàng -->
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif;">
    <div dir="auto" style="max-width: 600px; margin: 15px auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <div style="background-color: #006d57; color: #ffffff; text-align: center; padding: 15px;">
            <h2 style="margin: 10px 0; font-size: 20px;">{{ config('data_language_1.'.$language.'.order_confirmation') }}</h2>
        </div>
        <div style="padding: 15px 20px; line-height: 1.6; color: #333333; background: #EDF2F7;">
            <p style="margin: 0 0 10px; font-size: 15px;">{{ config('data_language_1.'.$language.'.hello') }} <strong>{{ $order->customer->email ?? '---' }}</strong>,</p> <!-- Xin chào -->
            <p style="margin: 10px 0; font-size: 15px;">{{ config('data_language_1.'.$language.'.thank_you_message_email') }}</p> <!-- Cảm ơn bạn đã hoàn tất đơn hàng của mình! Hình nền của bạn đã sẵn sàng để sử dụng. -->

            <table style="width: 100%; background-color: #fff; padding: 5px 15px; border-radius: 12px; border: 1px solid #e4e4e4; margin: 15px 0; font-size: 15px;">
                <tbody>
                    <tr>
                        <td style="width: 150px; padding: 5px 0;"><strong>{{ config('data_language_1.'.$language.'.order_id') }}:</strong></td> <!-- Mã đơn -->
                        <td style="padding: 5px 0;">{{ $order->code ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>{{ config('data_language_1.'.$language.'.purchase_date') }}:</strong></td> <!-- Ngày mua -->
                        <td style="padding: 5px 0;">{{ !empty($order->created_at) ? date('d/m/Y', strtotime($order->created_at)) : '--- ' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>{{ config('data_language_1.'.$language.'.total_price') }}:</strong></td> <!-- Tổng tiền -->
                        <td style="padding: 5px 0;">{{ !empty($order->total) ? \App\Helpers\Number::getFormatPriceByLanguage($order->total, 'vi') : '---' }}</td>
                    </tr>
                </tbody>
            </table>

            <h3 style="font-size: 18px; margin: 10px 0;">{{ config('data_language_1.'.$language.'.download_guide') }}:</h3> <!-- Hướng dẫn tải -->
            <p style="margin: 10px 0; font-size: 15px;">{{ config('data_language_1.'.$language.'.download_instructions') }}:</p> <!-- Bạn có thể tải bộ hình nền bằng các cách sau -->
            <ol style="margin: 10px 0; padding-left: 20px; font-size: 15px;">
                <li>{{ config('data_language_1.'.$language.'.direct_download') }}: <!-- Tải trực tiếp qua các liên kết bên dưới -->
                    <ul style="margin: 5px 0; padding-left: 20px;">
                        @foreach($order->wallpapers as $wallpaper)
                            @if(!empty($wallpaper->infoWallpaper->file_cloud_source))
                                @php
                                    $urlSource = \App\Helpers\Image::getUrlImageCloud($wallpaper->infoWallpaper->file_cloud_source);
                                @endphp
                                <li><a href="{{ $urlSource }}" style="color: #006d57; text-decoration: none; font-weight: 700;">{{ config('data_language_1.'.$language.'.image') }} {{ $loop->index + 1 }}</a></li> <!-- Ảnh -->
                            @endif
                        @endforeach
                    </ul>
                </li>
                <li>{!! config('data_language_1.'.$language.'.login_instruction_message_email') !!}</li>
            </ol>

            <p style="margin: 10px 0; font-size: 15px;">{!! config('data_language_1.'.$language.'.support_contact_message_email') !!}</p>

            <p style="margin: 10px 0; font-size: 15px;">{!! config('data_language_1.'.$language.'.wish_message_email') !!}</p>
        </div>
        <div style="background-color: #333333; color: #ffffff; text-align: center; padding: 10px; font-size: 14px;">
            <p style="margin: 10px 0; font-size: 14px;">{{ config('data_language_1.'.$language.'.sincerely') }}<br><a href="{{ env('APP_URL') }}" style="color: #ffffff; text-decoration: none;">{{ env('DOMAIN_NAME') }}</a></p>
        </div>
    </div>
</body>
</html>
