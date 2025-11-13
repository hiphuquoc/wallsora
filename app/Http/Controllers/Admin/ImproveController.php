<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Http\Requests\TagRequest;
use App\Models\Seo;
use App\Models\LanguageTagInfo;
use App\Models\Prompt;
use App\Models\Tag;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Http\Controllers\Admin\HelperController;
use App\Http\Controllers\Admin\GalleryController;
use App\Jobs\AutoTranslateAndCreatePage;
use App\Models\RelationEnCategoryInfoEnCategoryBlogInfo;
use App\Models\RelationCategoryInfoTagInfo;
use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\RelationSeoPageInfo;
use App\Models\RelationSeoProductInfo;
use App\Models\SeoContent;
use App\Jobs\AutoTranslateContent;
use App\Jobs\AutoWriteContent;
use App\Models\JobAutoTranslate;

class ImproveController extends Controller {


    public static function improveContent(Request $request) {
        $ordering = $request->get('ordering');
        $idSeoVi  = $request->get('seo_id'); // Chỉ cải thiện content tiếng Việt
    
        // Kiểm tra nếu thiếu dữ liệu đầu vào
        if (empty($ordering) || empty($idSeoVi)) {
            return json_encode([
                'error'   => 'Thiếu thông tin đầu vào!',
                'content' => '',
            ], JSON_UNESCAPED_UNICODE);
        }
    
        try {
            $content = self::handleImproveContent($ordering, $idSeoVi);
    
            if (!empty($content)) {
                return json_encode([
                    'error'   => '',
                    'content' => $content,
                ], JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode([
                    'error'   => 'Không tìm thấy nội dung phù hợp!',
                    'content' => '',
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            return json_encode([
                'error'   => 'Lỗi hệ thống: ' . $e->getMessage(),
                'content' => '',
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function handleImproveContent($ordering, $idSeo){
        /* xử lý theo từng ordering */
        $promptText     = self::getPromptImproveContent($ordering, $idSeo);
        /* thực thi & trả kết quả */
        $content        = '';
        if(!empty($promptText)){
            $infoPrompt = [
                'version'   => 'qwen-max',
            ];
            $response   = ChatGptController::callApi($promptText, $infoPrompt);
            $content    = $response['content'] ?? '';
        }
        return $content;
    }

    private static function getPromptImproveContent($ordering, $idSeo){
        $infoPage   = HelperController::getFullInfoPageByIdSeo($idSeo);
        $title      = $infoPage->seo->title;
        $slug       = $infoPage->seo->slug;
        $type       = $infoPage->seo->type;
        $contentSource    = '';
        foreach($infoPage->seo->contents as $content){
            if($content->ordering==$ordering) {
                $contentSource = $content->content;
                break;
            }
        }
        if($type=='category_info'||$type=='tag_info'){
            switch ($ordering) {
                case 1: /* giới thiệu */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 sửa lại theo mẫu và hướng dẫn:
                                        <đoạn mẫu>
                                            <h2>Hình nền điện thoại '.$title.': Khám phá vẻ đẹp Ma Mị và Kỳ Bí của búp bê huyền thoại '.$title.' ngay trên màn hình điện thoại của bạn</h2>
                                        </đoạn mẫu>
                                        + "Ma Mị" và "Kỳ Bí" thay bằng 2 tính chất để làm nổi bật vẻ đẹp của chủ đề, "búp bê huyền hoại '.$title.'" thay bằng đoạn chung chung nói lên được sự bao quát của chủ đề.
                                    - phần mở đầu - cải thiện như sau: tách ra 3 đoạn cho dễ đọc, rõ nghĩa từng đoạn, lời dẫn thật hay, cuốn hút và hợp lí. quan trọng dẫn dắt liên quan đến vẻ đẹp của chủ đề. rõ nghĩa từng câu bao gồm: 1 đoạn đặt câu hỏi để dẫn, 1 đoạn nói nếu khách hàng là người như thế nào, thì vẻ đẹp của hình nền điện thoại này sẽ phù hợp với họ như thế nào, 1 đoạn mời họ bước vào khám phá chủ đề (thêm "nhé!" cuối câu mời này cho thân thiện). mãu gợi ý bên dưới - bạn hãy dựa vào đó mà sáng tạo, sửa lại đoạn mở đầu cho phù hợp với chủ đề và thật cuốn hút:
                                        <gợi ý>
                                            <p>Bạn có biết, mỗi lần mở điện thoại cũng giống như mở ra một cánh cửa nhỏ dẫn đến thế giới riêng của chính mình?</p>
                                            <p>Và nếu bạn là người yêu thích sự sáng tạo, đam mê cái đẹp và trân trọng những giá trị nghệ thuật độc đáo, thì các bộ sưu tập <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> mà chúng tôi mang đến chắc chắn sẽ khiến bạn cảm thấy vô cùng hứng thú - đây không chỉ đơn thuần là những bức ảnh đẹp mắt, mà còn là cả một câu chuyện về tinh thần tự do, cá tính mạnh mẽ và nguồn cảm hứng bất tận được gửi gắm qua từng chi tiết đấy!</p>
                                            <p>Hãy để chúng tôi đồng hành cùng bạn trong hành trình khám phá những giá trị thẩm mỹ đỉnh cao, nơi mà mỗi bức ảnh đều kể câu chuyện riêng về sự đẳng cấp và phong cách đỉnh cao nhé!</p>
                                        </gợi ý>
                                        + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.' cao cấp</a></strong>. đa dạng từ "cao cáp" trong anchor text với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                    - phần nội dung định nghĩa, phần nội dung bên dưới thẻ h3 - cải thiện như sau: 2 đoạn 2 ý riêng biệt (mỗi đoạn trong thẻ <p></p>), mở rộng cho hay hơn, 1 đoạn định nghĩa và 1 đoạn chú trọng nói về vể đẹp của chủ đề, không cần nói về sản phẩm của tôi chỗ này.
                                    - phần nội dung nói về cách nghệ sĩ ứng dụng vẻ đẹp của chủ đề vào hình nền - cải thiện như sau: 2 đoạn 2 ý riêng biệt (mỗi đoạn trong thẻ <p></p>), 1 đoạn nói về sự sáng tạo của nghệ sĩ trong việc ứng dụng vẻ đẹp của chủ đề vào thiết kế hình nền điện thoại, 1 đoạn hãy nói nhiều về sự đầu tư, nghiên cứu tâm lí học, ứng dụng và gian nan như thế nào để có những tác phẩm nghệ thuật ấn tượng.
                                    - phần nội dung nói về tầm quan trọng của việc sử dụng hình nền đẹp và phù hợp - cải thiện như sau:
                                        + 1 đoạn dẫn chứng số liệu, tôi cần bạn dựa vào content cũ viết bổ sung thêm dẫn chứng cụ thể hơn, số liệu và thông tin đầy đủ, đáng tin cậy hơn.
                                        + 1 đoạn nói về những bộ sưu tập chất lượng của tôi (số nhiều), tôi cần bạn dựa vào content cũ viết lại để kết nối với câu trên, nhấn mạnh và nói nhiều hơn nữa về vẻ đẹp, lợi ích và chất lượng của các bộ hình nền cao cấp và đặt 1 link  <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>. đa dạng anchor text với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu). lưu ý đa dạng đừng trùng với anchor text phần mở đầu.
                                        + 1 đoạn cuối, tôi cần bạn dựa vào content cũ viết lại cho thật hay và cuốn hút (chỗ vẽ viễn cảnh để khách hàng tưởng tượng), thêm cảm thán phù hợp ở cuối đoạn để cho thân thiện và cảm xúc (nhưng cảm xúc nhẹ nhàng, đừng quá kích thích).
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.
    
                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa
    
                                    đoạn content cần sửa:
                                    '.$contentSource;
                    break;
                case 2: /* phân loại */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - đoạn mở đầu viết lại theo mẫu bên dưới:
                                        <gợi ý>
                                            <p>Bạn đã từng băn khoăn không biết nên chọn hình nền nào để vừa thể hiện cá tính, vừa mang đến cảm giác mới lạ cho chiếc điện thoại của mình?</p>
                                            <p>Đừng lo! Chúng tôi sẽ giúp bạn khám phá những phân loại độc đáo xoay quanh chủ đề <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.'</a></strong>. Để thông qua nội dung này, bạn sẽ dễ dàng tìm thấy những phong cách hình nền lý tưởng và phù hợp với mình nhất nhé!</p>
                                        </gợi ý>
                                        + đặt 1 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>.
                                    - phần nội dung thân - nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Tại <strong><a href="../../">'.env('DOMAIN_NAME').'</a></strong>, chúng tôi tự hào sở hữu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.' đỉnh cao</a></strong> với đa dạng các thể loại, phong cách và chủ đề - mỗi bộ sưu tập đều được đầu tư kỹ lưỡng về chất lượng hình ảnh và giá trị nghệ thuật, đảm bảo mang đến trải nghiệm tuyệt vời nhất cho người dùng. Hãy để chúng tôi đồng hành cùng bạn trong việc tạo nên diện mạo độc đáo và hấp dẫn cho chiếc điện thoại ngay hôm nay nhé!</p>
                                        </đoạn mẫu>
                                        + đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">kho hình nền điện thoại '.$title.' cao cáp</a></strong>. đa dạng từ "cao cáp" trong anchor text với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.
    
                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa
    
                                    đoạn content cần sửa:
                                    '.$contentSource;
                    break;
                case 3: /* lợi ích */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - phần nội dung thân - nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p><strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">Kho hình nền '.$title.' cao cấp</a></strong> tại <strong><a href="../../">'.env('DOMAIN_NAME').'</a></strong> được xây dựng với tất cả tâm huyết và sự chuyên nghiệp - mỗi bộ sưu tập đều là thành quả của quá trình nghiên cứu kỹ lưỡng, từ khâu lựa chọn chủ đề đến việc hoàn thiện từng chi tiết nhỏ nhất. Chúng tôi tự hào mang đến cho bạn những sản phẩm không chỉ đẹp về hình thức mà còn giàu giá trị tinh thần, vượt xa mong đợi của một bộ hình nền thông thường.</p>
                                        </đoạn mẫu>
                                        + đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">Kho hình nền '.$title.' cao cấp</a></strong>. đa dạng từ "cao cáp" trong anchor text với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.
    
                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa
    
                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                case 5: /* cách chọn */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - phần nội dung mở đầu - viết lại theo mẫu bên dưới:
                                        <gợi ý>
                                            <p>Bạn đang băn khoăn không biết làm thế nào để chọn được những bộ <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> vừa đẹp, vừa phù hợp với phong cách và cá tính của mình?</p>
                                            <p>Đừng lo lắng! Chúng tôi hiểu rằng mỗi người đều có những tiêu chí lựa chọn hình nền riêng. Vậy nên, nội dung dưới đây sẽ giúp bạn khám phá những tiêu chí quan trọng để lựa chọn <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền '.$title.' đẹp</a></strong>, từ đó dễ dàng tìm thấy những bộ sưu tập hoàn hảo cho chiếc điện thoại của mình!</p>
                                        </gợi ý>
                                        + đặt 2 link ở phần mở đầu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong>. 1 link cố định anchor text từ khóa ngắn (như mẫu), 1 link thì đa dạng từ "đẹp" trong anchor text với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                    - phần nội dung thân - nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Kết thúc hành trình khám phá <strong>cách chọn hình nền điện thoại '.$title.'</strong>, chúng tôi tin rằng bạn đã có cái nhìn tổng quan và sâu sắc hơn về chủ đề này. Tại <strong><a href="../../">'.env('DOMAIN_NAME').'</a></strong>, chúng tôi tự hào có nền tảng hệ thống chuyên nghiệp, công nghệ vượt trội và tích hợp AI thông minh để hỗ trợ bạn dễ dàng tìm được những sản phẩm phù hợp theo tất cả các tiêu chí kể trên. Hãy khám phá và trải nghiệm sự khác biệt ngay hôm nay nhé!</p>
                                        </đoạn mẫu>
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.
    
                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa
    
                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                case 4: /* gợi ý */
                    $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                    - tiêu đề lớn h2 giữ nguyên nội dung
                                    - phần nội dung thân - viết lại theo mẫu bên dưới:
                                        + những tiêu đề nằm trong thẻ <h3> (nội dung của các gợi ý) viết lại cho hấp dẫn hơn, phù hợp với chủ đề và thêm "4k" vào sau tên chủ đề để nhấn mạnh chất lượng của bộ sưu tập.
                                        + nội dung ý chính giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                    - đoạn kết - viết lại theo mẫu bên dưới:
                                        <đoạn mẫu>
                                            <p>Tại <strong><a href="../../">'.env('DOMAIN_NAME').'</a></strong>, chúng tôi mang đến <strong><a href="../../hinh-nen-dien-thoai">kho hình nền điện thoại</a></strong> đa sắc màu và đầy đủ các chủ đề – nơi mỗi bức ảnh là một câu chuyện, mỗi thiết kế là một mảnh ghép cảm xúc. Từ những gam màu rực rỡ dành cho tâm hồn nghệ sĩ yêu cái đẹp, đến những hình ảnh tinh tế, sâu lắng phù hợp làm quà tặng ý nghĩa, tất cả đều đang chờ bạn khám phá đấy!</p>
                                        </đoạn mẫu>
                                    - Tôi cần icon trước các thẻ h3 (nếu chưa có thì chọn bổ sung vào - nếu có rồi thì chọn lại) cho thật đẹp, phù hợp với chủ đề và nội dung của thẻ. ví dụ chỗ định nghĩa thì cần icon của chủ đề, chỗ cách ứng dụng thì thêm icon nào thể hiện sự sáng tạo - nghiên cứu, chỗ tầm quan trọng thì icon nhấn mạnh thật đẹp.
    
                                    Yêu cầu về kết quả:
                                    - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                    - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa
    
                                    đoạn content cần sửa:
                                    '.$contentSource;
                        break;
                    case 8: /* kết luận chung */
                        $promptText = 'tôi có đoạn content cần được cải thiện lại cho hay hơn, mạch lạc và cảm xúc hơn dựa theo các theo yêu cầu cụ thể:
                                        - tiêu đề lớn h2 giữ nguyên nội dung
                                        - phần nội dung mở đầu - viết lại theo mẫu bên dưới:
                                            <gợi ý>
                                                <p>Tiếp theo, chúng tôi sẽ cùng bạn khám phá một số bí quyết để giúp bạn quản lí và tối ưu hóa trải nghiệm cá nhân với những bộ <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.'</a></strong> mà bạn đã sưu tầm - đầu tư nhé!</p>
                                                <p>Đây không chỉ là những hướng dẫn kỹ thuật mà còn là hành trình giúp bạn kết nối sâu sắc hơn với niềm đam mê nghệ thuật và tận hưởng được tối đa giá trị tinh thần mà các bộ sưu tập này mang lại. Bắt đầu ngay thôi!</p>
                                            </gợi ý>
                                        - phần nội dung thân - icon và nội dung chính của các mẹo giữ nguyên, chau chuốt lại lời văn cho hay, mạch lạc, cảm xúc và phù hợp với chủ đề hơn. không cần đặt thêm link phần này.
                                        - phần nội dung kết luận chung - cải thiện như sau: tách ra 5 đoạn cho dễ đọc, rõ nghĩa từng đoạn, lời dẫn thật hay, cuốn hút và hợp lí. rõ nghĩa từng đoạn theo các hướng dẫn cụ thể cho từng đoạn bên dưới: 
                                            + 1 đoạn kết luận chung chung về vai trò, lợi ích và vẻ đẹp của hình nền điện thoại '.$title.', đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề:
                                                <đoạn mẫu>
                                                    <p>Trong thế giới hiện đại ngày nay, nơi công nghệ thường lấn át cảm xúc, <strong><a title="Hình Nền Điện Thoại '.$title.'" href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền '.$title.'</a></strong> đóng vai trò như một cây cầu nối giữa nghệ thuật và cuộc sống hàng ngày. Chúng không chỉ đơn thuần là những hình ảnh trang trí mà còn là phương tiện thể hiện bản thân, nuôi dưỡng tâm hồn và thậm chí trở thành "<strong>liệu pháp tinh thần</strong>" mỗi khi bạn cần nguồn cảm hứng vô tận. Mỗi đường nét, mỗi gam màu đều kể câu chuyện riêng về truyền thống và sự sáng tạo, mang đến cho bạn nguồn cảm hứng bất tận trong cuộc sống hằng ngày..</p>
                                                </đoạn mẫu>
                                            + 1 đoạn nhấn mạnh về sự đầu tư thiết kế, kì công của chung tôi để tạo ra các sản phẩm chất lượng, với mong muốn truyền tải nhiều giá trị tích cực và ý nghĩa nhất có thể cho người dùng, đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề. đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.' cao cấp</a></strong>. đa dạng từ "cao cáp" trong anchor text với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                                <đoạn mẫu>
                                                    <p>Tại <strong><a title="'.env('DOMAIN_NAME').'" href="../../">'.env('DOMAIN_NAME').'</a></strong>, mỗi <strong><a href="../../hinh-nen-dien-thoai/'.$slug.'">hình nền điện thoại '.$title.' cao cấp</a></strong> đại diện cho đỉnh cao của một quá trình sáng tạo nghiêm túc: từ nghiên cứu tâm lý học màu sắc, hiểu biết về xu hướng thẩm mỹ đương đại đến việc cân bằng hoàn hảo giữa vẻ đẹp truyền thống và phong cách hiện đại. Chúng tôi tin rằng việc cá nhân hóa thiết bị công nghệ là cách để tôn trọng chính mình – một tuyên ngôn tự hào giữa nhịp sống bận rộn.</p>
                                                </đoạn mẫu>
                                            + 1 đoạn vẽ ra viễn cảnh tươi đẹp cho khách hàng tưởng tượng, đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề. đặt 1 link như mẫu <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại cao cấp</a></strong>. đa dạng từ "cao cáp" trong anchor text với các biến thể như "độc đáo", "đỉnh cao", "chất lượng cao", "cao cấp", "đẹp", "4K" (như mẫu).
                                                <đoạn mẫu>
                                                    <p>Hãy tưởng tượng mỗi sáng thức dậy, mở điện thoại và nhìn thấy hình ảnh yêu thích rực rỡ trên màn hình – có thể là một khoảnh khắc đáng nhớ, một nguồn cảm hứng mới cho ngày làm việc, hoặc đơn giản là một niềm vui nhỏ bạn tự dành tặng bản thân. Tất cả những cảm xúc ấy đang chờ bạn khám phá trong từng bộ sưu tập <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại cao cấp</a></strong> của chúng tôi – nơi cái đẹp không chỉ được chiêm ngưỡng mà còn trở thành một phần trong cuộc sống hằng ngày của bạn!</p>
                                                </đoạn mẫu>
                                            + 1 đoạn kêu gọi nhẹ nhàng, không cần link - đoạn mẫu bên dưới - dựa vào đoạn mãu bạn hãy sáng tạo và viết lại cho unique, đa dạng và phù hợp với chủ đề:
                                                <đoạn mẫu>
                                                    <p>Đừng ngần ngại thử nghiệm những sự kết hợp mới, thay đổi gu thẩm mỹ hoặc thậm chí "<strong>phá vỡ quy tắc</strong>" để tìm ra phiên bản hình nền phản ánh chân thực nhất con người bạn. Sau cùng, điện thoại không chỉ là một công cụ – nó là tấm gương phản chiếu cá tính của bạn, một không gian riêng tư nơi bạn có thể tự do thể hiện mọi khía cạnh của tâm hồn. Và chúng tôi luôn ở đây, đồng hành cùng bạn trên hành trình khám phá ấy!</p>
                                                </đoạn mẫu>
                                                + đa dạng từ <strong>phá vỡ quy tắc</strong> cho unique
                                            + 1 đoạn chúc khách hàng - viết nguyên và giữ theo đoạn mẫu bên dưới:
                                                <đoạn mẫu>
                                                    <p>Chúc bạn có những trải nghiệm tuyệt vời và tràn đầy cảm hứng cùng những <strong><a href="../../hinh-nen-dien-thoai">hình nền điện thoại đẹp</a></strong> mà bạn yêu thích!</p>
                                                </đoạn mẫu>
    
                                        Yêu cầu về kết quả:
                                        - Trả về HTML text hoàn chỉnh và đầy đủ nội dung với thẻ đúng chuẩn  (chỉ cần text, không cần định dạng trong khung html)
                                        - Chỉ trả kết quả bài viết, không giải thích thêm, không thêm các ký tự định dạng thừa
    
                                        đoạn content cần sửa:
                                        '.$contentSource;
                            break;
                default:
                    $promptText = '';
                    break;
            }
        }else { /* product_info */
            $promptText = 'mô tả về sản phẩm của tôi trên website:
                            - '.$title.'
                            - gồm 6-8 ảnh đẹp chất lượng 4k

                            tôi có đoạn nội dung về sản phẩm này trên website cần cải thiện và viết lại theo yêu cầu:
                            '.$contentSource.'

                            dựa theo yêu cầu của tôi:
                            - Phân tích đối tượng khách hàng:
                                + Xác định 5 nhóm khách hàng tiềm năng chính - phần này bạn chỉ cần phân tích để viết cho phù hợp và thu hút những khách hàng này, không cần trinh bày rõ ràng vào nội dung.
                                + Phân tích nhu cầu và động lực mua hàng của từng nhóm
                                + Đề xuất cách sản phẩm đáp ứng nhu cầu của từng nhóm
                            - Tối ưu cho bán hàng:
                                + Giải quyết các băn khoăn thường gặp của khách hàng
                                + Tạo động lực mua hàng ngay - đầu tư đời sông tinh thần - trải nghiệm số trên điện thoại thân thuộc.
                                + Kết hợp yếu tố cảm xúc và lý trí
                            - Tối ưu giọng văn:
                                + Sử dụng những câu và đoạn văn ngắn nhưng vẫn truyền tải hết ý nghĩa.
                                + Diễn đạt cho thật hay, cảm xúc, gần gũi và cuốn hút. Hãy dẫn dắt hợp lí giữa câu với nhau, từ đoạn này qua qua kia cũng dẫn dắt liền mạch, nhẹ nhàng, chân thành, hơi bay bổng, lãng mạn một chút, cảm xúc và sáng tạo.
                                + Cân bằng giữa ngôn ngữ chuyên môn và dễ hiểu. Tránh sử dụng quá nhiều thuật ngữ kỹ thuật.
                                + Sử dụng giọng văn gần gũi, truyền cảm hứng tích cực và phấn khởi
                                + Xưng hô thân thiện với cách xưng hô "chúng tôi - bạn".
                                + Chuyển đoạn mạch lạc, nhẹ nhàng và cuốn hút giữa các câu với nhau, giữa các đoạn với nhau và giữa các phần với nhau.
                                + Thêm các emoji phù hợp với nội dung phía trước các thẻ li để làm nổi bật nội dung
                                + Trả về kết quả và đừng giải thích hay ghi chú gì thêm.

                            layout mẫu hướng dẫn bên dưới:
                            - Phần h2 đầu tiên và nội dung giới thiệu: tôi cần bạn đầu tư thật nhiều ở phần này viết lại cho hay, nội dung 2 đoạn nên rõ ràng, 1 đoạn nêu lên vấn đề, 1 đoạn trình bày giải pháp là sản phẩm. trong nội dung cũ có link sản phẩm hãy giữ lại và đặt vào nội dung mới.

                            - Phần h2 tiếp theo trong nội dung cũ "Tại sao bạn nên chọn bộ hình nền này" thay bằng layout và hướng dẫn bên dưới:
                            <h2>Giá Trị Cảm Xúc & Ý Nghĩa Tinh Thần Của Bộ Hình Nền Pháo Hoa</h2> [viết lại phần tiêu đề này và nội dung con trong ul li bên dưới cho phù hợp với sản phẩm]
                            <ul>
                            <li><strong>Lưu Giữ Khoảnh Khắc Hạnh Phúc:</strong> Mỗi hình ảnh pháo hoa không chỉ là bức ảnh đẹp - mà còn là câu chuyện cảm xúc được "<strong>đóng khung</strong>" tinh tế, từng tia sáng rực rỡ mang theo hơi ấm của niềm vui, kết nối bạn với những giây phút đáng nhớ bên gia đình và người thân..</li>
                            <li><strong>Thể Hiện Cá Tính Riêng Biệt:</strong> Pháo hoa tượng trưng cho những khởi đầu tốt đẹp và khát vọng tương lại. Lựa chọn những hình nền này, bạn không chỉ trang trí màn hình, mà còn khẳng định mình là người đặc biệt – tự tin, năng động và có khát vọng hướng đến những điều tốt đẹp.</li>
                            <li><strong>Nguồn Năng Lượng Tích Cực:</strong> Màu sắc và hình ảnh có sức mạnh kỳ diệu trong việc làm mới tâm hồn. Những hình nền pháo hoa rực rỡ sẽ như "liều thuốc bổ" tinh thần, giúp bạn xua tan mệt mỏi mỗi khi nhìn vào.</li>
                            <li><strong>Nghệ Thuật Trong Công Nghệ:</strong> Đây không chỉ là hình nền, mà còn là tác phẩm nghệ thuật số được thiết kế tỉ mỉ - mỗi chi tiết đều mang đến trải nghiệm thẩm mỹ hoàn hảo, biến chiếc điện thoại thành phương tiện thể hiện phong cách cá nhân.</li>
                            <li><strong>Kết Nối Cảm Xúc Số:</strong> Trong thời đại số hóa, không gian sống số ngày càng quan trọng với mỗi người chúng ta - bộ hình nền này sẽ biến màn hình lạnh lẽo thành không gian ấm áp, chứa đựng kỷ niệm và cảm xúc riêng tư của bạn.</li>
                            </ul>
                            <h2>Chất Lượng Cao - Độc Quyền Chỉ Có Tại '.env('DOMAIN_NAME').'</h2> [viết lại nội dung con trong ul li bên dưới cho phù hợp với sản phẩm]
                            <ul>
                            <li><strong>Độ phân giải chuẩn 4K Ultra HD:</strong> Mỗi hình nền được thiết kế với độ sắc nét tối đa, hiển thị hoàn hảo trên mọi kích thước màn hình, mang đến trải nghiệm sống động như thật.</li>
                            <li><strong>Sở hữu nhiều lựa chọn trong một bộ sưu tập giúp bạn dễ dàng làm mới màn hình thường xuyên, phù hợp với tâm trạng hoặc các dịp đặc biệt mà không gây nhàm chán.</li>
                            <li><strong>Định dạng PNG:</strong> Màu sắc hiển thị chính xác, không viền, không mất chi tiết, giữ trọn chất lượng gốc trên mọi loại màn hình.</li>
                            </ul>
                            <h2>Món Quà Số Độc Đáo Cho Người Thân Yêu</h2> [viết lại nội dung con trong ul li bên dưới cho phù hợp với sản phẩm]
                            <ul>
                            <li><strong>Quà tặng ý nghĩa không đụng hàng:</strong> Thay vì những món quà vật chất thông thường, bộ hình nền độc đáo này sẽ là món quà tinh thần đặc biệt, thể hiện sự quan tâm tinh tế đến không gian cá nhân của người nhận.</li>
                            <li><strong>Làm mới trải nghiệm số:</strong> Tặng người thân cơ hội làm mới thiết bị yêu thích mà không tốn chi phí mua sắm mới, mang đến niềm vui và sự hứng khởi mỗi ngày.</li>
                            <li><strong>Kết nối cảm xúc đặc biệt:</strong> Mỗi lần nhìn vào màn hình, người nhận sẽ nhớ đến tình cảm và sự quan tâm của bạn, tạo nên kết nối bền lâu.</li>
                            </ul>
                            <h2>Tương Thích Với Mọi Thiết Bị Điện Thoại</h2> [phần tiêu đề này và nội dung con bên dưới giữ nguyên]
                            <ul>
                            <li><strong>iPhone:</strong> Được tối ưu hóa cho tất cả các dòng từ iPhone 6 trở lên, hiển thị hoàn hảo trên cả màn hình khóa và màn hình chính.</li>
                            <li><strong>Dòng Samsung Galaxy:</strong> Phù hợp với tỷ lệ màn hình của Galaxy S, Note, A series và các dòng máy gập, tận dụng tối đa công nghệ Dynamic AMOLED.</li>
                            <li><strong>Hỗ trợ đa nền tảng Android:</strong> Tương thích hoàn hảo với Xiaomi, Oppo, Vivo, Realme, Huawei và mọi thiết bị Android khác, đảm bảo hiển thị đẹp mắt dù bạn dùng điện thoại nào.</li>
                            </ul>
                            <h2>Chính Sách Hậu Mãi & Cam Kết Từ Chúng Tôi</h2> [phần tiêu đề này và nội dung con bên dưới giữ nguyên]
                            <ul>
                            <li><strong>Hỗ trợ 24/7 tận tâm:</strong> Đội ngũ chuyên viên luôn sẵn sàng giải đáp mọi thắc mắc qua hệ thống chat trực tuyến và chat bot AI – bạn không bao giờ phải chờ đợi.</li>
                            <li><strong>Trải nghiệm mua sắm thuận tiện:</strong> Chỉ với vài thao tác đơn giản trên hệ thống thanh toán bảo mật cao, bạn đã có thể sở hữu ngay cho riêng mình những bộ sưu tập yêu thích.</li>
                            <li><strong>Lưu trữ an toàn qua email:</strong> Bộ hình nền sẽ được gửi trực tiếp đến email của bạn, đảm bảo an toàn và dễ dàng tải xuống bất cứ lúc nào.</li>
                            <li><strong data-spm-anchor-id="5aebb161.2ef5001f.0.i66.73d1c9214poDmc">Cập nhật và nâng cấp miễn phí trọn đời: </strong>Bạn sẽ luôn được ưu tiên thông báo qua email mỗi khi bộ hình nền này cập nhật thêm phiên bản hình ảnh mới hoặc được tối ưu hóa để tương thích với các dòng điện thoại sắp ra mắt. </li>
                            </ul>

                            - Phần h2 tiếp theo trong nội dung cũ "Món quà..." xòa bỏ vì tôi đã gộp vào phần trên trước đó 

                            - Phần <p>Call action - viết lại theo hướng dẫn: <p class="callActionBox">🎉 <span onclick="openCloseModal(\'modalPaymentMethod\')">Tải ngay [tên sản phẩm]</span> để ...! Và cũng đừng quên ghé thăm <strong><a href="../../">'.env('DOMAIN_NAME').'</a></strong> thường xuyên để khám phá và cập nhật thêm nhiều bộ sưu tập hình nền điện độc đáo và ấn tượng khác nhé!</p>';
        }
        return $promptText;
    }

}
