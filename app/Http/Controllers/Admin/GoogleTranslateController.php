<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class GoogleTranslateController extends Controller {
    /**
     * Hàm dịch từ khóa sang nhiều ngôn ngữ.
     *
     * @param  string  $text  Văn bản cần dịch
     * @param  array  $languages  Mảng ngôn ngữ đích
     * @return array  Kết quả dịch
     */
    public static function translate($text, $language = 'vi') {
        $response = '';

        $response = Http::withHeaders([
            'Referer' => env('APP_URL')
        ])->get('https://translation.googleapis.com/language/translate/v2', [
            'key' => env('GOOGLE_TRANSLATE_API_KEY'),
            'q' => $text,
            'target' => $language,
        ]);

        if ($response->successful()) {
            $translatedText = $response->json('data.translations.0.translatedText');
            $response       = $translatedText;
        } else {
            $response = "Lỗi khi gọi API Google Translate.";
        }

        return $response;
    }
}
