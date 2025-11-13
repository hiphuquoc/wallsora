<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\Upload;
use App\Http\Requests\CategoryRequest;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Prompt;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Models\Tag;
use App\Http\Controllers\Admin\GalleryController;
use App\Models\RelationCategoryInfoTagInfo;
use App\Models\RelationCategoryThumnail;
use App\Models\RelationSeoCategoryInfo;

class CategoryController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params     = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = Category::getTreeCategory(['seo.language' => 'vi']);
        return view('admin.category.list', compact('list', 'params'));
    }

    public static function listLanguageNotExists(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo danh mục */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewCategoryInfoLanguageNotExists') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Category::listLanguageNotExists($params);
        return view('admin.category.listLanguageNotExists', compact('list', 'params', 'viewPerPage'));
    }

    public static function view(Request $request){
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
        $item               = Category::select('*')
                                ->where('id', $id)
                                ->with('thumnails', 'seo.contents', 'seos.infoSeo.contents', 'seos.infoSeo.jobAutoTranslate')
                                ->first();
        if(empty($item)) $flagView = false;
        if($flagView==true){
            /* chức năng copy source */
            $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
            $itemSourceToCopy   = Category::select('*')
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
            $arrayTypeCategory = [];
            foreach(config('main_'.env('APP_NAME').'.category_type') as $c) $arrayTypeCategory[] = $c['key'];
            $prompts            = Prompt::select('*')
                                    ->whereIn('reference_table', $arrayTypeCategory)
                                    ->orderBy('ordering', 'ASC')
                                    ->get();
            /* trang canonical -> cùng là sản phẩm */
            $idProduct          = $item->id ?? 0;
            $sources            = Category::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($language){
                                        $query->where('language', $language);
                                    })
                                    ->where('id', '!=', $idProduct)
                                    ->get();
            /* tags con */
            $tags               = Tag::all();
            /* type */
            $type               = !empty($itemSeo) ? 'edit' : 'create';
            $type               = $request->get('type') ?? $type;
            /* trang cha */
            if($type=='edit'){
                /* loại trừ chính nó ra */
                $parents        = Category::select('*')
                                    ->where('id', '!=', $item->id)
                                    ->get();
            }else {
                $parents        = Category::all();
            }
            /* đếm số lượng trang đang chọn trang gốc là trang này => để hiện thị nút "Copy sang trang con" */
            $idSeoVi            = $item->seo->id ?? 0;
            $countChild         = Seo::select('*')
                                    ->where('link_canonical', $idSeoVi)
                                    ->count();
            /* list danh sách trang chưa đủ content (html) */
            $languageNotEnoughContent = self::getListPageNotEnoughContent($item);
            return view('admin.category.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'language', 'sources', 'parents', 'tags', 'countChild', 'languageNotEnoughContent', 'message'));
        } else {
            return redirect()->route('admin.category.list');
        }
    }

    public function createAndUpdate(CategoryRequest $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id') ?? 0;
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idCategory         = $request->get('category_info_id');
            $language           = $request->get('language');
            $categoryType       = $request->get('category_type') ?? null;
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
                if(!empty($request->get('content'))) self::insertAndUpdateContents($idSeo, $request->get('content'));
                /* update seo */
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
                /* insert seo_content */
                if(!empty($request->get('content'))) self::insertAndUpdateContents($idSeo, $request->get('content'));
            }
            /* update những phần khác */
            if($language=='vi'){
                /* insert hoặc update category_info */
                $flagShow           = !empty($request->get('flag_show'))&&$request->get('flag_show')=='on' ? 1 : 0;
                if(empty($idCategory)){ /* check xem create category hay update category */
                    $idCategory          = Category::insertItem([
                        'flag_show'     => $flagShow,
                        'seo_id'        => $idSeo,
                    ]);
                }else {
                    Category::updateItem($idCategory, [
                        'flag_show'     => $flagShow,
                    ]);
                }
                /* insert relation_category_info_tag_info */
                RelationCategoryInfoTagInfo::select('*')
                    ->where('category_info_id', $idCategory)
                    ->delete();
                if(!empty($request->get('tags'))){
                    foreach($request->get('tags') as $idTagInfo){
                        RelationCategoryInfoTagInfo::insertItem([
                            'category_info_id'      => $idCategory,
                            'tag_info_id' => $idTagInfo
                        ]);
                    }
                }
                /* insert gallery và lưu CSDL */
                if($request->hasFile('galleries')){
                    $name           = $request->get('slug');
                    $params         = [
                        'attachment_id'     => $idCategory,
                        'relation_table'    => 'category_info',
                        'name'              => $name,
                        'file_type'         => 'gallery',
                    ];
                    GalleryController::upload($request->file('galleries'), $params);
                }
            }
            /* relation_seo_category_info */
            $relationSeoCategoryInfo = RelationSeoCategoryInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('category_info_id', $idCategory)
                                    ->first();
            if(empty($relationSeoCategoryInfo)) RelationSeoCategoryInfo::insertItem([
                'seo_id'        => $idSeo,
                'category_info_id'   => $idCategory
            ]);
            
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Category!'
            ];
            /* nếu có tùy chọn index => gửi google index */
            if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                $flagIndex = IndexController::indexUrl($idSeo);
                if($flagIndex==200){
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Category và Báo Google Index!';
                }else {
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Category <span style="color:red;">nhưng báo Google Index lỗi</span>';
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
        return redirect()->route('admin.category.view', ['id' => $idCategory, 'language' => $language]);
    }

    public static function insertAndUpdateContents($idSeo, $arrayContent){
        SeoContent::select('*')
            ->where('seo_id', $idSeo)
            ->delete();
        foreach($arrayContent as $ordering => $content){
            SeoContent::insertItem([
                'seo_id'    => $idSeo,
                'content'   => $content,
                'ordering'  => $ordering
            ]);
        }
    }

    public function delete(Request $request){
        try {
            DB::beginTransaction();
            $id         = $request->get('id');

            if (!$id) return false;

            $info       = Category::select('*')
                            ->where('id', $id)
                            ->with(['files' => function($query){
                                $query->where('relation_table', 'seo.type');
                            }])
                            ->with('seo', 'products', 'freeWallpapers')
                            ->first();

            if (!$info) return false;
            
            // Xoá ảnh đại diện chính
            if (!empty($info->seo->image)) {
                Upload::deleteWallpaper($info->seo->image);
            }

            // Xoá các quan hệ
            $info->products()->delete();
            $info->freeWallpapers()->delete();
            $info->thumnails()->delete();
            $info->files()->delete();
            $info->tags()->delete();

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
            \App\Models\Category::withoutSyncingToSearch(function () use ($info) {
                $info->delete();
            });

            // Xoá khỏi Meilisearch (nếu index tồn tại)
            try {
                $meili = new MeilisearchClient(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
                $meili->index('category_info')->deleteDocument($id);
            } catch (\Exception $e) {
                // Bạn có thể log lỗi hoặc bỏ qua nếu không cần xử lý tiếp
                Log::warning("Meilisearch delete failed for category ID $id: ".$e->getMessage());
            }

            DB::commit();
            return true;
        } catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }

    public static function getListPageNotEnoughContent($infoPage){
        $response = [
            'html'  => '',
            'array' => [

            ]
        ];
        if(!empty($infoPage)){
            $tmp                = [];
            $countContentVi     = $infoPage->seo->contents->count() ?? 0;
            foreach($infoPage->seos as $seo){
                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language!='vi'){ /* đang so sánh số lượng content bản dịch với bản ngon ngữ khác nên không cần đếm qua vi */
                    $countTMP   = 0;
                    foreach($seo->infoSeo->contents as $content){
                        if(!empty(trim($content->content))) $countTMP += 1;
                    }
                    if($countTMP<$countContentVi) $tmp[] = $seo->infoSeo->language;
                }
            }
            if(!empty($tmp)) {
                $response['html']   = '<span style="color:#00bd7d;">('.count($tmp).' trang)</span> ' . implode(', ', $tmp);
                $response['array']  = $tmp;
            }
        }
        return $response;
    }

    public static function removeThumnailsOfCategory(Request $request){
        $flag = RelationCategoryThumnail::select('*')
                    ->where('free_wallpaper_info_id', $request->get('free_wallpaper_info_id'))
                    ->where('category_Info_id', $request->get('category_info_id'))
                    ->delete();
        echo $flag;
    }

    public static function loadFreeWallpaperOfCategory(Request $request){
        $idCategory         = $request->get('category_info_id');
        $item               = Category::select('*')
                                ->where('id', $idCategory)
                                ->with('thumnails')
                                ->first();
        $xhtml              = '';
        foreach($item->thumnails as $thumnail) $xhtml .= view('admin.category.oneRowGallery', compact('thumnail'))->render();

        echo $xhtml;
    }

    public static function seachFreeWallpaperOfCategory(Request $request){
        $idCategory         = $request->get('category_info_id');
        $item               = Category::select('*')
                                ->where('id', $idCategory)
                                ->with('thumnails', 'freeWallpapers')
                                ->first();
        $xhtml              = '';
        foreach($item->freeWallpapers as $freeWallpaper) {
            $selected   = '';
            foreach($item->thumnails as $thumnail){
                if(!empty($freeWallpaper->infoFreewallpaper->id)&&$thumnail->free_wallpaper_info_id==$freeWallpaper->infoFreewallpaper->id) $selected = 'selected';
            }
            $xhtml .= view('admin.category.oneRowSearchGallery', compact('freeWallpaper', 'selected'))->render();

        }

        echo $xhtml;
    }

    public static function chooseFreeWallpaperForCategory(Request $request){
        $action             = $request->get('action');
        $idCategory         = $request->get('category_info_id');
        $idFreewallpaper    = $request->get('free_wallpaper_info_id');
        /* đầu tiên sẽ delete tất cả */
        RelationCategoryThumnail::select('*')
            ->where('category_info_id', $idCategory)
            ->where('free_wallpaper_info_id', $idFreewallpaper)
            ->delete();
        /* nếu là create thì tạo lại */
        if($action=='create'){
            RelationCategoryThumnail::insertItem([
                'category_info_id'          => $idCategory,
                'free_wallpaper_info_id'    => $idFreewallpaper,
            ]);
        }
        /* không quan tâm hành động, trả về flag có hay không tồn tại relation để hiện thị selected */
        $tmp        = RelationCategoryThumnail::select('*')
            ->where('category_info_id', $idCategory)
            ->where('free_wallpaper_info_id', $idFreewallpaper)
            ->first();
        
        $flagHas    = !empty($tmp) ? true : false;

        return response()->json($flagHas);
    }
}
