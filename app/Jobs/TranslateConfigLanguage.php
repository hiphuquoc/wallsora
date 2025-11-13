<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\Prompt;
use App\Models\SeoContent;
use App\Http\Controllers\HomeController;

class TranslateConfigLanguage implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $infoLanguage;
    public  $tries = 5; // Số lần thử lại

    public function __construct($infoLanguage){
        $this->infoLanguage     = $infoLanguage;
    }

    public function handle(){
        try {
            $model              = 'qwen-max';
            $options            = [];
            $configFilePath     = base_path('config/data_language_2.php');
            $promptText     = 'dịch value của array này ra ngôn ngữ '.$this->infoLanguage['key'].' - '.$this->infoLanguage['name'].' giúp tôi, yêu cầu:
                                    - dịch hay, chuẩn ngôn ngữ và văn phong địa phương
                                    - "PhamVanPhu" là tên riêng, giữ nguyên giúp tôi
                                    - trình bày trong array như vậy 
                                    - chỉ cần trả về array tôi cần, đừng giải thích gì thêm và cũng đừng thêm những ký tự đặc biệt
            
                                "vi" => [
                                    "question_and_answer"   => "Câu hỏi thường gặp",
                                    "wallpaper_theme"   => "Chủ đề Hình Nền",
                                    "wallpaper_style"   => "Phong cách Hình Nền",
                                    "free_wallpaper"    => "Hình nền Miễn Phí",
                                    "phone_wallpaper"   => "Hình nền điện thoại ",
                                    "product_description"   => "<p>Hô biến màn hình điện thoại của bạn thành một kiệt tác nghệ thuật với bộ sưu tập hình nền cực chất!</p>
                                        <ul>
                                            <li>Độ phân giải cao, hình ảnh sắc nét đến từng chi tiết.</li>
                                            <li>Tương thích hoàn hảo với mọi loại màn hình điện thoại.</li>
                                            <li>Tải xuống dễ dàng và nhanh chóng chỉ với vài bước đơn giản.</li>
                                        </ul>
                                        <div>Hãy tải ngay và cảm nhận sự khác biệt đầy thú vị trên màn hình điện thoại của bạn nhé!</div>
                                    ",
                                    "wallpaper_by_themes" => "Hình Nền Điện Thoại theo Chủ Đề",
                                    "wallpaper_by_styles" => "Hình Nền Điện Thoại theo Phong Cách",
                                    "wallpaper_by_events" => "Hình Nền Điện Thoại theo Sự Kiện",
                                    "list_type_search" => [
                                        "category_info" => "Danh mục",
                                        "paid_wallpaper" => "Hình nền trả phí",
                                        "free_wallpaper" => "Hình nền miễn phí",
                                        "article" => "Bài viết",
                                    ],
                                    "copyright" => "Bản quyền ® ".date(\'Y\')." <a href=\"{{ env(\'APP_URL\') }}/{{ \$language }}\" aria-label=\"{{ env(\'APP_NAME\') }}\">{{ env(\'APP_NAME\') }}</a>. Thiết kế và phát triển bởi PhamVanPhu. Mọi quyền được bảo lưu.",
                                ]';
            $testMessages = [
                ['role' => 'system', 'content' => 'Bạn là một chuyên gia sáng tạo nội dung và dịch thuật với hơn 10 năm kinh nghiệm. Hãy giúp người dùng dịch những nội dung chính xác và phù hợp văn phong, văn hóa địa phương, với giọng văn thân thiện và dễ hiểu.'],
                ['role' => 'user', 'content' => $promptText]
            ];

            $response           = HomeController::chatWithAI($testMessages, $model, $options);

            $content            = $response['choices'][0]['message']['content'] ?? '';

            if (!empty($content)) {
                // Đọc file hiện tại
                $currentContent = file_exists($configFilePath) ? file_get_contents($configFilePath) : "<?php\n\nreturn [\n];";

                // Chèn nội dung mới vào giữa array return
                $currentContent = preg_replace('/return\s*\[\s*/', "return [\n" . $content . ",\n", $currentContent);

                // Ghi lại vào file
                file_put_contents($configFilePath, $currentContent);
            }

        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
