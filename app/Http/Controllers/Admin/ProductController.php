<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Http\Requests\ProductRequest;
use App\Models\Seo;
use App\Models\Tag;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\Wallpaper;
use App\Models\RelationCategoryProduct;
use App\Models\RelationSeoProductInfo;
use App\Models\Prompt;
use App\Models\SeoContent;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\IndexController;
use App\Models\RelationProductPriceWallpaperInfo;
use App\Jobs\CopyMultiProductJob;

class ProductController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function createAndUpdate(ProductRequest $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $keyTable           = 'product_info';
            $idSeo              = $request->get('seo_id') ?? 0;
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idProduct          = $request->get('product_info_id');
            $language           = $request->get('language');
            $type               = $request->get('type');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            /* update page & content */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            if(!empty($idSeo)&&$type=='edit'){
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
                /* insert hoặc update product_info */
                $infoProduct    = $this->BuildInsertUpdateModel->buildArrayTableProductInfo($request->all(), $idSeo);
                if(empty($idProduct)){ /* check xem create product hay update product */
                    $idProduct      = Product::insertItem($infoProduct);
                }else {
                    Product::updateItem($idProduct, $infoProduct);
                }
                /* lưu tag name */
                FreeWallpaperController::createOrGetTagName($idProduct, 'product_info', $request->get('tag'));
                /* update product_price 
                    => xóa các product_price nào id không tồn tại trong mảng mới 
                    => nào có tồn tại thì update - nào không thì thêm mới 
                */
                $priceSave          = [];
                
                foreach($request->get('prices') as $price){
                    if(!empty($price['id'])) $priceSave[]   = $price['id'];
                }
                $productPriceDelete = ProductPrice::select('*')
                                        ->where('product_info_id', $idProduct)
                                        ->whereNotIn('id', $priceSave)
                                        ->with('wallpapers')
                                        ->get();
                /* duyệt mảng delete files */
                foreach($productPriceDelete as $productPrice) {
                    RelationProductPriceWallpaperInfo::select('*')
                        ->where('product_price_id', $productPrice->id)
                        ->delete();
                    /* xóa product price */
                    $productPrice->delete();
                }
                /* update lại các product price còn lại */
                foreach($request->get('prices') as $price){
                    if(!empty($price['code_name'])&&!empty($price['price'])){
                        if(!empty($price['id'])&&$type=='edit'){
                            /* update */
                            $dataPrice              = $this->BuildInsertUpdateModel->buildArrayTableProductPrice($price, $idProduct, 'update');
                            ProductPrice::updateItem($price['id'], $dataPrice);
                        }else {
                            /* insert */
                            $dataPrice              = $this->BuildInsertUpdateModel->buildArrayTableProductPrice($price, $idProduct, 'insert');
                            ProductPrice::insertItem($dataPrice);
                        }
                    }
                }
                /* chủ đề */
                RelationCategoryProduct::select('*')
                    ->where('product_info_id', $idProduct)
                    ->delete();
                foreach(config('main_'.env('APP_NAME').'.category_type') as $type){
                    if(!empty($request->all()[$type['key']])){
                        foreach($request->all()[$type['key']] as $idCategory){
                            RelationCategoryProduct::insertItem([
                                'product_info_id'       => $idProduct,
                                'category_info_id'      => $idCategory
                            ]);
                        }
                    }
                }
                /* insert slider và lưu CSDL */
                if($request->hasFile('slider')&&!empty($idProduct)){
                    $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                    $params         = [
                        'attachment_id'     => $idProduct,
                        'relation_table'    => $keyTable,
                        'name'              => $name
                    ];
                    SliderController::upload($request->file('slider'), $params);
                }
            }
            /* relation_seo_product_info */
            $relationSeoCategoryInfo = RelationSeoProductInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('product_info_id', $idProduct)
                                    ->first();
            if(empty($relationSeoCategoryInfo)) RelationSeoProductInfo::insertItem([
                'seo_id'            => $idSeo,
                'product_info_id'   => $idProduct
            ]);
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Sản phẩm!'
            ];
            /* nếu có tùy chọn index => gửi google index */
            if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                $flagIndex = IndexController::indexUrl($idSeo);
                if($flagIndex==200){
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Sản phẩm và Báo Google Index!';
                }else {
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Sản phẩm! <span style="color:red;">nhưng báo Google Index lỗi</span>';
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
        return redirect()->route('admin.product.view', ['id' => $idProduct, 'language' => $language]);
    }

    public static function view(Request $request){
        $keyTable           = 'product_info';
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? 'vi';
        /* kiểm tra xem ngôn ngữ có nằm trong danh sách không */
        $flagView       = false;
        foreach(config('language') as $ld){
            if($ld['key']==$language) {
                $flagView = true;
                break;
            }
        }
        /* lấy thông tin item */
        $item               = Product::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query) use($keyTable){
                                    $query->where('relation_table', $keyTable);
                                }])
                                ->with('seo', 'seos', 'prices.wallpapers.infoWallpaper', 'categories')
                                ->first();
        if(empty($item)) $flagView = false;
        if($flagView==true){
            /* chức năng copy source */
            $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
            $itemSourceToCopy   = Product::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeoSourceToCopy){
                                        $query->where('id', $idSeoSourceToCopy);
                                    })
                                    ->with(['files' => function($query) use($keyTable){
                                        $query->where('relation_table', $keyTable);
                                    }])
                                    ->with('seo', 'seos', 'prices.wallpapers.infoWallpaper', 'categories')
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
                                    ->where('reference_table', $keyTable)
                                    ->orderBy('ordering', 'ASC')
                                    ->get();
            /* gộp lại thành parents và lọc bỏ page hinh-nen-dien-thoai */
            $parents            = Category::all();
            $wallpapers         = Wallpaper::select('*')
                                    ->get();
            $categories         = $parents;
            /* trang canonical -> cùng là sản phẩm */
            $idProduct          = $item->id ?? 0;
            $sources            = Product::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($language){
                                        $query->where('language', $language);
                                    })
                                    ->where('id', '!=', $idProduct)
                                    ->get();
            /* tag name */
            $tags               = Tag::all();
            $arrayTag           = [];
            foreach($tags as $tag) if(!empty($tag->seo->title)) $arrayTag[] = $tag->seo->title;
            /* đếm số lượng trang đang chọn trang gốc là trang này => để hiện thị nút "Copy sang trang con" */
            $idSeoVi            = $item->seo->id ?? 0;
            $countChild         = Seo::select('*')
                                    ->where('link_canonical', $idSeoVi)
                                    ->count();
            /* type */
            $type               = !empty($itemSeo) ? 'edit' : 'create';
            $type               = $request->get('type') ?? $type;
            /* list danh sách trang chưa đủ content (html) */
            $languageNotEnoughContent = CategoryController::getListPageNotEnoughContent($item);
            return view('admin.product.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'language', 'wallpapers', 'type', 'categories', 'sources', 'parents', 'arrayTag', 'countChild', 'languageNotEnoughContent', 'message'));
        }else {
            return redirect()->route('admin.product.list');
        }
    }

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo danh mục */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewProductInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Product::getList($params);
        $categories = Category::select('*')
                        ->with('products', 'seo')
                        ->get();
        return view('admin.product.list', compact('list', 'categories', 'viewPerPage', 'params'));
    }

    public static function listLanguageNotExists(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo danh mục */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewProductInfoLanguageNotExists') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Product::listLanguageNotExists($params);
        return view('admin.product.listLanguageNotExists', compact('list', 'params', 'viewPerPage'));
    }

    public static function searchProductCopied(Request $request){
        $xhtml  = '';
        if(!empty($request->get('id_seo'))){
            $idSeo      = $request->get('id_seo');
            $copiedSeos = Product::select('*')
                            ->whereHas('seo', function($query) use($idSeo){
                                $query->where('link_canonical', $idSeo);
                            })
                            ->get();
            $i          = 1;
            foreach($copiedSeos as $item){
                $no     = $i;
                $xhtml .= view('admin.product.row', compact('item', 'no'))->render();
                ++$i;
            }
        }
        echo $xhtml;
    }

    public static function updateProductCopied(Request $request){
        $idSeo      = $request->get('id_seo') ?? 0;
        if(!empty($idSeo)){ /* điều kiện này quan trọng -> vì nếu rỗng sẽ lấy hết sản phẩm */
            /* lấy sản phẩm gốc */
            $productSource = Product::select('*')
                                ->whereHas('seo', function($query) use($idSeo){
                                    $query->where('id', $idSeo);
                                })
                                ->with('seo', 'seos')
                                ->first();
            /* lấy danh sách sản phẩm copy */
            $products   = Product::select('*')
                        ->whereHas('seo', function($query) use($idSeo){
                            $query->where('link_canonical', $idSeo);
                        })
                        ->with('seo', 'seos')
                        ->get();
            // $response   = self::copyMultiProduct($productSource, $products);
            $response       = CopyMultiProductJob::dispatch($productSource, $products);
            $message        = [
                'type'      => 'success',
                'message'   => 'Đã gửi yêu cầu Copy sang trang con thành công! (Job chạy ngầm)',
            ];
            $request->session()->put('message', $message);
        }
    }

    public function delete(Request $request){
        try {
            DB::beginTransaction();
            $id = $request->get('id');

            if (!$id) return false;

            $info       = Product::select('*')
                            ->where('id', $id)
                            ->with(['files' => function($query){
                                $query->where('relation_table', 'seo.type');
                            }])
                            ->with('seo', 'seos', 'seo.contents', 'prices.wallpapers')
                            ->first();
                        
            if (!$info) return false;

            // Xoá ảnh đại diện chính
            if (!empty($info->seo->image)) {
                Upload::deleteWallpaper($info->seo->image);
            }

            // Xoá các quan hệ
            $info->prices->each(function ($price) {
                $price->wallpapers()->delete();
            });
            $info->prices()->delete();
            $info->categories()->delete();
            $info->files()->delete();

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
            \App\Models\Product::withoutSyncingToSearch(function () use ($info) {
                $info->delete();
            });

            // Xoá khỏi Meilisearch (nếu index tồn tại)
            try {
                $meili = new MeilisearchClient(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
                $meili->index('product_info')->deleteDocument($id);
            } catch (\Exception $e) {
                // Bạn có thể log lỗi hoặc bỏ qua nếu không cần xử lý tiếp
                Log::warning("Meilisearch delete failed for product ID $id: ".$e->getMessage());
            }

            DB::commit();
            return true;
        } catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }

    public static function copyMultiProduct($infoProductSource, $arrayInfoProduct){ /* đã chuyển ra job và đang không dùng */
        $response   = []; /* trả ra array id đã xử lý */
        try {
            DB::beginTransaction();
            foreach ($arrayInfoProduct as $t) {
                /* xóa relation seos -> infoSeo -> contents (nếu có) */
                foreach ($t->seos as $seo) {
                    foreach ($seo->infoSeo->contents as $content) {
                        SeoContent::select('*')
                            ->where('id', $content->id)
                            ->delete();
                    }
                    \App\Models\RelationSeoProductInfo::select('*')
                        ->where('seo_id', $seo->seo_id)
                        ->delete();
                    Seo::select('*')
                        ->where('id', $seo->seo_id)
                        ->delete();
                }
                /* tạo dữ liệu mới */
                foreach ($infoProductSource->seos as $seoS) {
                    /* tạo seo */
                    $tmp2   = $seoS->infoSeo->toArray();
                    $insert = [];
                    foreach ($tmp2 as $key => $value) {
                        if ($key != 'contents' && $key != 'id') $insert[$key] = $value;
                    }
                    $insert['link_canonical']   = $tmp2['id'];
                    $insert['slug']             = $tmp2['slug'] . '-' . $t->id;
                    $insert['slug_full']        = $tmp2['slug_full'] . '-' . $t->id;
                    $idSeo = Seo::insertItem($insert);
                    /* cập nhật lại seo_id của product */
                    if ($insert['language'] == 'vi') {
                        Product::updateItem($t->id, [
                            'seo_id' => $idSeo,
                        ]);
                    }
                    $response[] = $idSeo;
                    /* tạo relation_seo_product_info */
                    RelationSeoProductInfo::insertItem([
                        'seo_id'    => $idSeo,
                        'product_info_id' => $t->id,
                    ]);
                    /* tạo content */
                    foreach ($seoS->infoSeo->contents as $content) {
                        $contentInsert = $content->content;
                        $contentInsert = str_replace($seoS->infoSeo->slug_full, $insert['slug_full'], $contentInsert);
                        SeoContent::insertItem([
                            'seo_id'    => $idSeo,
                            'content'   => $contentInsert,
                            'ordering'  => $content->ordering,   
                        ]);
                    }
                }
                /* copy relation product và category */
                \App\Models\RelationCategoryProduct::select('*')
                    ->where('product_info_id', $t->id)
                    ->delete();
                foreach($infoProductSource->categories as $category){
                    \App\Models\RelationCategoryProduct::insertItem([
                        'category_info_id'       => $category->category_info_id,
                        'product_info_id'      => $t->id
                    ]);
                }
                /* copy relation product và tag */
                \App\Models\RelationTagInfoOrther::select('*')
                    ->where('reference_type', 'product_info')
                    ->where('reference_id', $t->id)
                    ->delete();
                foreach($infoProductSource->tags as $tag){
                    \App\Models\RelationTagInfoOrther::insertItem([
                        'tag_info_id'       => $tag->tag_info_id,
                        'reference_type'    => 'product_info',
                        'reference_id'      => $t->id
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $exception){
            DB::rollBack();
        }
        return $response;
    }
}
