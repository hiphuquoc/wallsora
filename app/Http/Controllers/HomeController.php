<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use App\Models\Page;
use App\Models\Category;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\HelperController;
use App\Http\Controllers\Admin\TranslateController;
use App\Http\Controllers\Admin\ChatGptController;
use App\Models\ISO3166;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Product;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Session;
use App\Models\RelationSeoProductInfo;
use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\RelationSeoPageInfo;
use App\Models\Timezone;
use App\Jobs\Tmp;
use App\Jobs\AutoTranslateContent;
use App\Jobs\AutoImproveContent;
use App\Jobs\TranslateConfigLanguage;
use App\Jobs\CopyBoxContentToAllTagAndCategory;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendProductMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

use App\Services\HeaderMainService;
use App\Services\HtmlCacheService;
use GuzzleHttp\Client;


class HomeController extends Controller {

    public static function home(HeaderMainService $headerMainService, HtmlCacheService $htmlCacheService, Request $request, $language = 'vi')
    {
        SettingController::settingLanguage($language);

        $appName = env('APP_NAME');
        $cacheKey = RoutingController::buildNameCache("{$language}home");

        $htmlContent = $htmlCacheService->getOrRender($cacheKey, function () use ($language, $headerMainService, $appName) {
            // lấy thông tin trang chủ
            $item = Page::select('*')
                ->whereHas('seos.infoSeo', function ($query) use ($language) {
                    $query->where('slug', $language);
                })
                ->with('seo', 'seos.infoSeo', 'type')
                ->first();
            $itemSeo = self::extractSeoForLanguage($item, $language);

            // lấy html header main menu
            $menuHtml = $headerMainService->getMenuHtml($language);

            // lấy danh sách danh mục
            $categories = Category::select('*')
                ->where('flag_show', 1)
                ->get();

            // render giao diện
            return View::make('wallpaper.home.index', compact(
                'item',
                'itemSeo',
                'language',
                'menuHtml',
                'categories',
            ))->render();
        });

        echo $htmlContent;
    }

    /**
        * Trích xuất infoSeo đúng ngôn ngữ
    */
    public static function extractSeoForLanguage($item, $language) {
        if (empty($item->seos)) {
            return [];
        }

        foreach ($item->seos as $seo) {
            if (!empty($seo->infoSeo->language) && $seo->infoSeo->language === $language) {
                return $seo->infoSeo;
            }
        }

        return [];
    }

    public static function test(Request $request){

        // // Dữ liệu mẫu để kiểm thử hàm
        // $promptText = '';
        // $testMessages = [
        //     ['role' => 'system', 'content' => 'Bạn là một chuyên gia sáng tạo nội dung với phong cách hấp dẫn và sáng tạo. Hãy giúp tôi viết những nội dung độc đáo và thu hút người đọc, với giọng văn thân thiện, dễ hiểu và sáng tạo. Sử dụng ngôn ngữ tự nhiên và tránh lặp từ.'],
        //     ['role' => 'user', 'content' => $promptText]
        // ];
        // $options        = [
        //     // 'max_tokens'    => 100000,
        //     // 'stream'        => false,
        //     // 'temperature' => 0.7, // Cân bằng giữa sáng tạo và tập trung (0-1)
        //     // 'top_p' => 0.9, // Lấy mẫu từ phần trăm xác suất cao nhất 
        //     // 'frequency_penalty' => 0.5, // Giảm lặp từ (0-1)
        //     // 'presence_penalty' => 0.3, // Khuyến khích đề cập chủ đề mới (0-1)
        //     // 'stop' => ['</html>', '<!--END-->'], // Dừng generate khi gặp các sequence này
        //     // 'best_of' => 3, // Sinh 3 response và chọn cái tốt nhất (tăng chi phí)
        //     // 'n' => 1, // Số lượng response trả về
        // ];
        // // $model  = 'deepseek-reasoner';
        // $model      = 'qwen-max';
        // $response = self::chatWithAI($testMessages, $model, $options);
        // print_r($response);
        // dd($response);


        // $tags = Tag::select('*')
        //             ->where('id', '>', 748)
        //             ->orderBy('id', 'DESC')
        //             ->get();
        
        // foreach($tags as $tag){

        //     $idSeo = 0;
        //     foreach($tag->seos as $seo){
        //         if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language=='vi'){
        //             $idSeo = $seo->infoSeo->id;
        //             break;
        //         }
        //     }
        //     foreach($tag->seo->contents as $content){
        //         if($content->ordering!=6||$content->ordering!=7) AutoImproveContent::dispatch($content->ordering, $idSeo);
        //     }
            
        // }

        // dd(123);

        // $languageList       = config('language');

        // foreach($languageList as $language){

        //     // TranslateConfigLanguage::dispatch($language);
        //     echo '<div>'.$language['key'].' - '.$language['name'].'</div>';
            
        // }

        // dd('success');

        // $array = ["Tê Giác", "Hà Mã", "Lạc Đà", "Cá Nemo", "Sao Biển", "Ngựa", "Cá Ngựa",
        //             "Hồng Hạc", "Vẹt", "Tắc Kè Hoa", "Cá Sấu", "Ếch", "Cá Rồng", "Báo Tuyết", 
        //             "Báo Đen", "Báo Đốm", "Cá Mập", "Cá Heo", "Rùa Biển", "Chim Ưng", "Chim Công", "Đom Đóm", 
        //             "Cá Chép", "Cá Koi", "Cá Thần Tiên", "Cá La Hán", "Cá Bảy Màu", "Cá Betta", "Bươm Bướm",
        //             "Hoa Dâm Bụt", "Hoa Giấy", "Hoa Sứ", "Hoa Lưu Ly", "Hoa Cát Tường", "Hoa Thược Dược", 
        //             "Hoa Mộc Lan", "Hoa Ngọc Lan", "Hoa Sim", "Hoa Súng", "Hoa Lài",
        //             "Trà", "Cà phê", "Trái Bơ", "Trái Thanh Long", "Sữa chua", "Trái xoài", "Trái ổi", "Hải sản",
        //             "Thư viện", "Hồ sen", "Bảo tàng", "Nhà sàn",
        //             "Rạng san hô", "Rừng tre", "Rừng lá phong", "Rừng thông", "Mặt trăng",
        //             "Baby Tree", "Capybara", "Gấu Lobby", "Labubu",
        //             "Back Myth: Wukong", "Genshin Impact", "Call of Duty",
        //             "Tom and Jerry", "Chú Báo Hồng", "Mèo Oggy và những chú gián tinh nghịch", "Dr Stone", 
        //             "Spy x Family", "Solo Leveling", "Bleach", "Fate Series", "Gintama", 
        //             "Tokyo Revengers", "Code Geass", "Black Butler", "Toradora!", "Vua Sư Tử", "Mộ Đom Đóm", "Tây Du Ký", "Câu Bé Bút Chì", "Scooby Doo", "Khuyển dạ xoa", "Vùng đất linh hồn", "Ben 10", "Kẻ trộm mặt trăng", "Natra", "Chu Tước", "Bạch Hổ", "Huyền Vũ", "Thanh Long", "Cóc Ngậm Tiền", "Tỳ Hưu", "Đồng Tiền Cổ", "Cây Tài Lộc", "Tài Lộc", "Thần Tài", "Phúc Lộc Thọ", "Ông Địa", "Bát Quái", "Vòng Tay", "Cây Tùng", "Cây Trúc", "Tứ Quý", "Cầu Nguyện", "Hào Quang"];
        // $count      = 0;
        // foreach($array as $nameTag){
        //     $flag   = \App\Http\Controllers\Admin\FreeWallpaperController::createSeoTmp($nameTag);
        //     if($flag) $count += $count;
        // }

        // dd($count);


        /* thông tin bản tiếng việt & tiếng anh 
        
            "vi" => [
                "title" => "",
                "seo_title" => "",
                "seo_description" => "",
            ],
        
        */
        
        // $tags   = Tag::select('*')
        //                 ->where('id', '>=', 10)
        //                 ->where('id', '<=', 20)
        //                 ->get();
        // $arrayNotTranslate = ['vi', 'en'];
        // $count  = 0;
        // foreach($tags as $tag){
        //     $type = $tag->seo->type;
        //     $infoPrompt = \App\Models\Prompt::select('*')
        //                     ->where('reference_table', $type)
        //                     ->where('reference_name', 'content')
        //                     ->where('type', 'translate_content')
        //                     ->first();
        //     foreach($tag->seos as $seo){
        //         if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $arrayNotTranslate)){
        //             foreach($tag->seo->contents as $content){
        //                 \App\Jobs\AutoTranslateContent::dispatch($content->ordering, $seo->infoSeo->language, $seo->infoSeo->id, $infoPrompt->id); 
        //                 ++$count;           
        //             }
        //         }
        //     } 
        // }

        // dd($count);


        // $categories = Category::all();

        // foreach($categories as $category){
        //     echo ', <span>'.$category->seo->title.'</span>';
        // }


        $languages = config('language');
        foreach($languages as $language){
            echo '<div>'.$language['key'].' - '.$language['name_by_language'].'</div>';
        }

        $categories = Category::all();
        $i  = 1;
        foreach($categories as $category){
            echo $category->seo->title . ', ';
            ++$i;
        }


        dd(123);
    }

    public static function chatWithAI(array $messages, string $model = 'deepseek-reasoner', array $options = []) {
        // $apiUrl = "https://api.deepseek.com/chat/completions";
        $apiUrl = "https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions";
        $apiKey = env('QWEN_API_KEY'); // Đặt API key trong file .env
    
        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
        ], $options);
    
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $apiKey",
        ])->timeout(3060)->post($apiUrl, $payload);
    
        if ($response->successful()) {
            return $response->json();
        }
        
        return ['error' => 'Failed to connect to DeepInfra API', 'status' => $response->status(), 'body' => $response->body()];
    }

    private static function normalizeUnicode($string) {
        return \Normalizer::normalize($string, \Normalizer::FORM_C);
    }

    public static function callAPIClaudeAI(Request $request){

        // Cấu hình Guzzle client
        $client = new Client();

        // Lấy API key từ .env
        $apiKey = env('CLAUDE_AI_API_KEY');

        // Dữ liệu bạn muốn gửi đến Claude AI API
        $data = [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 1024,
            'messages' => [
                ['role' => 'user', 'content' => '1 + 1 bằng mấy'], 
            ],
        ];

        // Gửi yêu cầu POST đến Claude AI API
        $response = $client->post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => $data,
        ]);

        // Trả về kết quả từ API dưới dạng JSON
        $result = response()->json(json_decode($response->getBody()->getContents(), true));

        dd($result);
        
    }

    private static function findUniqueElements($arr1, $arr2) {
        // Lọc các phần tử có trong arr1 nhưng không có trong arr2 và ngược lại
        $uniqueInArr1 = array_diff($arr1, $arr2);
        $uniqueInArr2 = array_diff($arr2, $arr1);
        
        // Kết hợp các phần tử không trùng
        return array_merge($uniqueInArr1, $uniqueInArr2);
    }
}