<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Http\Requests\BlogRequest;
use App\Helpers\Upload;
use App\Models\CategoryBlog;
use App\Models\Blog;
use App\Models\Seo;
use App\Models\RelationCategoryBlogBlogInfo;
use App\Models\RelationSeoBlogInfo;
use App\Models\Prompt;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Product;
use App\Helpers\Image;
use App\Services\BuildInsertUpdateModel;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;

class BlogController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo tên */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewBlogInfo') ?? 50;
        $params['paginate'] = $viewPerPage;
        $categories         = CategoryBlog::all();
        $list               = Blog::getList($params);
        return view('admin.blog.list', compact('list', 'categories', 'params', 'viewPerPage'));
    }

    public function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* kiểm tra xem ngôn ngữ có nằm trong danh sách không */
        $flagView           = false;
        foreach(config('language') as $ld){
            if($ld['key']==$language) {
                $flagView   = true;
                break;
            }
        }
        /* tìm theo ngôn ngữ */
        $item               = Blog::select('*')
                                ->where('id', $id)
                                ->with('seo.contents', 'seos.infoSeo.contents', 'seos.infoSeo.jobAutoTranslate')
                                ->first();
        if(empty($item)) $flagView = false;
        if($flagView==true){
            /* chức năng copy source */
            $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
            $itemSourceToCopy   = Blog::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeoSourceToCopy){
                                        $query->where('id', $idSeoSourceToCopy);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
            $itemSeoSourceToCopy    = [];
            if(!empty($itemSourceToCopy->seos)){
                foreach($itemSourceToCopy->seos as $s){
                    if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language) {
                        $itemSeoSourceToCopy = $s->infoSeo;
                        break;
                    }
                }
            }
            /* lấy item seo theo ngôn ngữ được chọn */
            $itemSeo            = [];
            if(!empty($item->seos)){
                foreach($item->seos as $s){
                    if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language) {
                        $itemSeo = $s->infoSeo;
                        break;
                    }
                }
            }
            /* prompts */
            $prompts            = Prompt::select('*')
                                    ->where('reference_table', 'blog_info')
                                    ->get();
            /* trang canonical -> cùng là sản phẩm */
            $idProduct          = $item->id ?? 0;
            $sources            = Blog::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($language){
                                        $query->where('language', $language);
                                    })
                                    ->where('id', '!=', $idProduct)
                                    ->get();
            /* lấy category_info dùng để search sản phẩm */
            $categories         = Category::select('*')
                                    ->whereHas('seo', function($query){
                                        $query->where('level', 2);
                                    })
                                    ->get();
            /* lấy category_info dùng để search sản phẩm */
            $tags               = Tag::all();
            /* type */
            $type               = !empty($itemSeo) ? 'edit' : 'create';
            $type               = $request->get('type') ?? $type;
            /* đếm số lượng trang đang chọn trang gốc là trang này => để hiện thị nút "Copy sang trang con" */
            $idSeoVi            = $item->seo->id ?? 0;
            $countChild         = Seo::select('*')
                                    ->where('link_canonical', $idSeoVi)
                                    ->count();
            /* trang cha */
            $parents            = CategoryBlog::all();
            /* list danh sách trang chưa đủ content (html) */
            $languageNotEnoughContent = CategoryController::getListPageNotEnoughContent($item);
            return view('admin.blog.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'categories', 'tags', 'language', 'sources', 'parents', 'countChild', 'languageNotEnoughContent', 'message'));
        } else {
            return redirect()->route('admin.blog.list');
        }
    }

    public function createAndUpdate(Request $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id') ?? 0;
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idBlog             = $request->get('blog_info_id');
            $language           = $request->get('language');
            $categoryType       = 'blog_info';
            $type               = $request->get('type');
            /* check xem là create seo hay update seo */
            $action             = !empty($idSeo)&&$type=='edit' ? 'edit' : 'create';
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
           /* update page & content */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $categoryType, $dataPath);
            if($action=='edit'){
                /* insert seo_content => ghi chú quan trọng: vì trong update Item có tính năng replace url thay đổi trong content, nên bắt buộc phải cập nhật content trước để cố định dữ liệu */
                if(!empty($request->get('content'))) CategoryController::insertAndUpdateContents($idSeo, $request->get('content'));
                /* update seo */
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
                /* insert seo_content */
                if(!empty($request->get('content'))) CategoryController::insertAndUpdateContents($idSeo, $request->get('content'));
            }
            /* update những phần khác */
            if($language=='vi'){
                /* insert hoặc update blog_info */
                $status           = !empty($request->get('status'))&&$request->get('status')=='on' ? 1 : 0;
                $outstanding      = !empty($request->get('outstanding'))&&$request->get('outstanding')=='on' ? 1 : 0;
                if(empty($idBlog)){ /* check xem create category hay update category */
                    $idBlog          = Blog::insertItem([
                        'status'        => $status,
                        'outstanding'   => $outstanding,
                        'seo_id'        => $idSeo,
                    ]);
                }else {
                    Blog::updateItem($idBlog, [
                        'status'        => $status,
                        'outstanding'   => $outstanding,
                    ]);
                }
                /* insert relation_category_blog_blog_info */
                RelationCategoryBlogBlogInfo::select('*')
                    ->where('blog_info_id', $idBlog)
                    ->delete();
                if(!empty($request->get('categories'))){
                    foreach($request->get('categories') as $idCategoryBlog){
                        RelationCategoryBlogBlogInfo::insertItem([
                            'category_blog_id'  => $idCategoryBlog,
                            'blog_info_id'      => $idBlog
                        ]);
                    }
                }
            }
            /* relation_seo_blog_info */
            $relationSeoBlogInfo = RelationSeoBlogInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('blog_info_id', $idBlog)
                                    ->first();
            if(empty($relationSeoBlogInfo)) RelationSeoBlogInfo::insertItem([
                'seo_id'        => $idSeo,
                'blog_info_id'   => $idBlog
            ]);
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Bài Viết!'
            ];
            /* nếu có tùy chọn index => gửi google index */
            if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                $flagIndex = IndexController::indexUrl($idSeo);
                if($flagIndex==200){
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Bài Viết và Báo Google Index!';
                }else {
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Bài Viết <span style="color:red;">nhưng báo Google Index lỗi</span>';
                }
            }
        } catch (\Exception $exception){
            DB::rollBack();
        }
        /* có lỗi mặc định Message */
        if(empty($message)){
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.blog.view', ['id' => $idBlog, 'language' => $language]);
    }

    public function delete(Request $request){
        try {
            DB::beginTransaction();
            
            $id = $request->get('id');

            if (!$id) return false;

            $info       = Blog::select('*')
                            ->where('id', $id)
                            ->with('seo', 'seos')
                            ->first();

            if (!$info) return false;

            // Xoá ảnh đại diện chính
            if (!empty($info->seo->image)) {
                Upload::deleteWallpaper($info->seo->image);
            }

            // Xoá các quan hệ
            $info->categories()->delete();

            // Xoá các bản ghi liên quan trong seos
            foreach ($info->seos as $s) {
                if (!empty($s->infoSeo->image)) {
                    Upload::deleteWallpaper($s->infoSeo->image);
                }

                if (!empty($s->infoSeo->contents)) {
                    foreach ($s->infoSeo->contents as $c) {
                        $c->delete();
                    }
                }

                $s->infoSeo()->delete();
                $s->delete();
            }

            // Liên quan tới dữ liệu đã index trên Melisearch
            $engineManager = app(EngineManager::class);
            $engineManager->forgetEngines();
            // Tiếp tục với phần xóa dữ liệu
            \App\Models\Blog::withoutSyncingToSearch(function () use ($info) {
                $info->delete();
            });

            // Xoá khỏi Meilisearch (nếu index tồn tại)
            try {
                $meili = new MeilisearchClient(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
                $meili->index('blog_info')->deleteDocument($id);
            } catch (\Exception $e) {
                // Bạn có thể log lỗi hoặc bỏ qua nếu không cần xử lý tiếp
                Log::warning("Meilisearch delete failed for blog ID $id: ".$e->getMessage());
            }
            DB::commit();
            return true;
        } catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }

    public function loadProduct(Request $request){
        $params     = [];
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        if(!empty($request->get('search_tag'))) $params['search_tag'] = $request->get('search_tag');
        $params['paginate'] = PHP_INT_MAX;
        $products           = Product::getList($params);
        $response           = '';
        foreach($products as $product){
            $response       .= view('admin.blog.itemProduct', compact('product'))->render();
        }
        echo $response;
    }

    public function chooseProduct(Request $request){
        // Lấy `product_info_id` và `array_wallpaper_info_id` từ request
        $productInfoId = $request->input('product_info_id');
        $wallpaperIds = $request->input('array_wallpaper_info_id');

        // Lấy `productChoose` từ session, nếu chưa có thì đặt thành mảng rỗng
        $productChoose = session()->get('productChoose', []);

        // Lưu dữ liệu mới vào mảng `productChoose` với key là `product_info_id`
        $productChoose[$productInfoId] = $wallpaperIds;

        // Cập nhật session với mảng `productChoose` đã thay đổi
        session(['productChoose' => $productChoose]);

        echo 'success';
    }

    public function loadThemeProductChoosed(){
        $productChoose  = session()->get('productChoose', []);
        $productIds     = [];
        foreach($productChoose as $key => $p) $productIds[] = $key;
        $products       = Product::select('*')
                            ->whereIn('id', $productIds)
                            ->get();
        $response       = '';
        if(!empty($products)){
            foreach($products as $product){
                $wallpaperIds   = $productChoose[$product->id];
                $response       .= view('admin.blog.itemProductChoosed', compact('product', 'wallpaperIds'))->render();
            }
        }
        
        echo $response;
    }

    public function removeOneProductChoosed(Request $request) {
        $idProduct = $request->get('product_info_id');
        $productChoose = session()->get('productChoose', []);
    
        // Xóa sản phẩm khỏi mảng session nếu tồn tại
        foreach($productChoose as $key => $value) {
            if($key == $idProduct) {
                unset($productChoose[$key]);
                break;
            }
        }
    
        // Lưu lại mảng đã cập nhật vào session
        session()->put('productChoose', $productChoose);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Sản phẩm đã được xóa khỏi danh sách chọn.',
        ]);
    }

    public function clearProductChoosed(){
        session()->forget('productChoose');
    }

    public function getListProductChoose(){
        $productChoose      = session()->get('productChoose', []);

        return response()->json($productChoose);
    }

    public function callAIWritePerProduct(Request $request){
        $titleInput     = $request->get('title_input');
        $idProduct      = $request->get('product_info_id');
        $wallpapers     = $request->get('wallpapers');
        $maxRetries     = 3; // Số lần thử lại tối đa
        $retryCount     = 0; // Số lần thử lại hiện tại
        $isSuccessful   = false; // Biến để kiểm tra thành công

        // Lấy thông tin sản phẩm
        $infoProduct = Product::select('*')
            ->where('id', $idProduct)
            ->with('prices.wallpapers.infoWallpaper')
            ->first();

        // Tạo prompt
        $promptText = self::promptWriteSuggest($infoProduct, $titleInput);

        // Lặp lại việc gọi API cho đến khi thành công hoặc đạt đến số lần thử tối đa
        while (!$isSuccessful && $retryCount < $maxRetries) {
            try {
                // Gọi API ChatGPT
                $infoPrompt = new \stdClass;
                $infoPrompt->version = config('main_'.env('APP_NAME').'.ai_version')[1];
                $response = \App\Http\Controllers\Admin\ChatGptController::callApi($promptText, $infoPrompt);

                // Xử lý kết quả từ API nếu thành công
                $content = str_replace(['```html', '```'], '', $response['content']);
                $contentImageBox = self::htmlImageGroupBox($infoProduct, $wallpapers);
                $content = $content . $contentImageBox;

                // Trả kết quả thành công và đánh dấu là hoàn tất
                echo $content;
                $isSuccessful = true;
            } catch (\Exception $e) {
                // Tăng số lần thử nếu gặp lỗi
                $retryCount++;

                // Nếu đạt số lần thử tối đa và vẫn thất bại, ghi log lỗi
                if ($retryCount >= $maxRetries) {
                    \Log::error("Failed to process product ID {$idProduct} after {$maxRetries} retries.", [
                        'error' => $e->getMessage(),
                        'product_id' => $idProduct,
                    ]);
                    echo "Error processing product ID {$idProduct}. Please try again later.";
                } else {
                    // Log số lần thử lại
                    \Log::warning("Retrying API call for product ID {$idProduct}. Attempt {$retryCount}.");
                }
            }
        }
    }

    private static function promptWriteSuggest($infoProduct, $titleInput){
        /* 
            Tôi đang viết blog với tiêu đè: $titleInput
            Bên dưới tôi có thông tin của sản phẩm cần gợi ý và viết vào bài blog này:
            - tiêu đê sản phẩm: $titleProduct
            Bạn hãy hoàn thành yêu cầu sau giúp tôi:
            - viết một đoạn content giới thiệu ngắn khoảng 5-10 dòng để nêu bật được điểm độc đáo, hấp dẫn và đặc biệt của sản phẩm này
            - cũng nêu lên được tác dụng tích cực đối với tinh thần cho người sử dụng và nó phù hợp với những người nào
            - yêu cầu lời van mượt mà, hay và hấp dẫn. Diễn dạt nhẹ nhàng, lãng mạn, tương tự người viết và là một wow content
            - tôi dùng cho website nên viết chuẩn SEO, E-E-A-T. 
            - tôi chỉ cần bạn trả vê kết quả, không cần giải thích luyên thuyên.
            - ví dụ cho bạn tham khảo trình bày và trả về kết quả "<h2>tiêu đề sản phẩm</h2><p>nội dung 1</p><p>nội dung 2</p> (chia nội dung làm 2 phần cho dễ đọc)"
        */
        $result = "Tôi đang viết blog với tiêu đè: ".$titleInput." \n
            Bên dưới tôi có thông tin của sản phẩm cần gợi ý và viết vào bài blog này: \n
             - tiêu đê sản phẩm: ".$infoProduct->seo->title." \n
            Bạn hãy hoàn thành yêu cầu sau giúp tôi:\n
            - viết một đoạn content giới thiệu ngắn khoảng 5-10 dòng để nêu bật được điểm độc đáo, hấp dẫn và đặc biệt của sản phẩm này
            - cũng nêu lên được tác dụng tích cực đối với tinh thần cho người sử dụng và nó phù hợp với những người nào
            - yêu cầu lời van mượt mà, hay và hấp dẫn. Diễn dạt nhẹ nhàng, lãng mạn, tương tự người viết và là một wow content
            - tôi dùng cho website nên viết chuẩn SEO, E-E-A-T. 
            - tôi chỉ cần bạn trả vê kết quả, không cần giải thích luyên thuyên.
            - ví dụ cho bạn tham khảo trình bày và trả về kết quả <h2>tiêu đề sản phẩm</h2><p>nội dung 1</p><p>nội dung 2</p> (chia nội dung làm 2 phần cho dễ đọc)";
        return $result;
    }

    private static function htmlImageGroupBox($infoProduct, $wallpapers){
        /* 
            <p><a href="#" aria-lable="">Xem đầy đủ và tải bộ hình nền tại đây!</a></p>
            <div class="imageGroup">
                <div class="imageGroup_box">
                <div class="imageGroup_box_item"><img class="lazyload" title="Bộ Hình Nền Điện Thoại Gấu Con Đón Giáng Sinh 4k Tông Màu Xanh Da Trời Tuyệt Đẹp và Ấn Tượng" src="https://namecomvn.storage.googleapis.com/wallpapers/hinh-nen-dien-thoai-giang-sinh-noel-4k-tuyet-dep-v2-1728381709-2-mini.webp" alt="Bộ Hình Nền Điện Thoại Gấu Con Đón Giáng Sinh 4k Tông Màu Xanh Da Trời Tuyệt Đẹp và Ấn Tượng" loading="lazy" data-src="https://namecomvn.storage.googleapis.com/wallpapers/hinh-nen-dien-thoai-giang-sinh-noel-4k-tuyet-dep-v2-1728381709-2-small.webp"></div>
                <div class="imageGroup_box_item"><img class="lazyload" title="Bộ Hình Nền Điện Thoại Gấu Con Đón Giáng Sinh 4k Tông Màu Xanh Da Trời Tuyệt Đẹp và Ấn Tượng" src="https://namecomvn.storage.googleapis.com/wallpapers/hinh-nen-dien-thoai-giang-sinh-noel-4k-tuyet-dep-v2-1728383321-5-mini.webp" alt="Bộ Hình Nền Điện Thoại Gấu Con Đón Giáng Sinh 4k Tông Màu Xanh Da Trời Tuyệt Đẹp và Ấn Tượng" loading="lazy" data-src="https://namecomvn.storage.googleapis.com/wallpapers/hinh-nen-dien-thoai-giang-sinh-noel-4k-tuyet-dep-v2-1728383321-5-small.webp"></div>
                <div class="imageGroup_box_item"><img class="lazyload" title="Bộ Hình Nền Điện Thoại Gấu Con Đón Giáng Sinh 4k Tông Màu Xanh Da Trời Tuyệt Đẹp và Ấn Tượng" src="https://namecomvn.storage.googleapis.com/wallpapers/hinh-nen-dien-thoai-giang-sinh-noel-4k-tuyet-dep-v2-1728394958-1-mini.webp" alt="Bộ Hình Nền Điện Thoại Gấu Con Đón Giáng Sinh 4k Tông Màu Xanh Da Trời Tuyệt Đẹp và Ấn Tượng" loading="lazy" data-src="https://namecomvn.storage.googleapis.com/wallpapers/hinh-nen-dien-thoai-giang-sinh-noel-4k-tuyet-dep-v2-1728394958-1-small.webp"></div>
                </div>
                <div class="imageGroup_note" style="text-transform: lowercase;">Ảnh trong Bộ Hình Nền Điện Thoại Gấu Con Đón Giáng Sinh 4k Tông Màu Xanh Da Trời Tuyệt Đẹp và Ấn Tượng</div>
            </div>
        */
        $titleProduct = $infoProduct->seo->title;
        $result = '<p><a href="'.env('APP_URL').'/'.$infoProduct->seo->slug_full.'" aria-lable="'.$titleProduct.'">Xem đầy đủ và tải bộ hình nền tại đây!</a></p>';
        $result .= '<div class="imageGroup">
                        <div class="imageGroup_box">';
        foreach($wallpapers as $idWallpaper){
            foreach($infoProduct->prices as $price){
                foreach($price->wallpapers as $w){
                    if($w->id==$idWallpaper){
                        $urlImageMini   = Image::getUrlImageMiniByUrlImage($w->infoWallpaper->file_cloud_wallpaper);
                        $urlImageSmall  = Image::getUrlImageSmallByUrlImage($w->infoWallpaper->file_cloud_wallpaper);
                        $result         .= '<div class="imageGroup_box_item"><img class="lazyload" 
                                                src="'.$urlImageMini.'" 
                                                data-src="'.$urlImageSmall.'" 
                                                title="'.$titleProduct.'" 
                                                alt="'.$titleProduct.'" loading="lazy">
                                            </div>';
                    }
                }
            }
        }
        $result .= '</div>';
        $result .= '<div class="imageGroup_note" style="text-transform: lowercase;">Ảnh trong '.$titleProduct.'</div>';
        $result .= '</div>';
        return $result;
    }
}
