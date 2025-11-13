<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Upload;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Prompt;
use App\Models\RelationSeoCategoryBlog;
use App\Http\Requests\CategoryBlogRequest;
use App\Models\CategoryBlog;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\DB;

class CategoryBlogController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = CategoryBlog::getTreeCategory();
        return view('admin.categoryBlog.list', compact('list', 'params'));
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
        $item               = CategoryBlog::select('*')
                                ->where('id', $id)
                                ->with('seo.contents', 'seos.infoSeo.contents', 'seos.infoSeo.jobAutoTranslate')
                                ->first();
        if(empty($item)) $flagView = false;
        if($flagView==true){
            /* chức năng copy source */
            $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
            $itemSourceToCopy   = CategoryBlog::select('*')
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
                                    ->where('reference_table', 'category_blog')
                                    ->get();
            /* trang canonical -> cùng là sản phẩm */
            $idProduct          = $item->id ?? 0;
            $sources            = CategoryBlog::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($language){
                                        $query->where('language', $language);
                                    })
                                    ->where('id', '!=', $idProduct)
                                    ->get();
            /* type */
            $type               = !empty($itemSeo) ? 'edit' : 'create';
            $type               = $request->get('type') ?? $type;
            /* trang cha */
            if($type=='edit'){
                /* loại trừ chính nó ra */
                $parents        = CategoryBlog::select('*')
                                    ->where('id', '!=', $item->id)
                                    ->get();
            }else {
                $parents        = CategoryBlog::all();
            }
            /* đếm số lượng trang đang chọn trang gốc là trang này => để hiện thị nút "Copy sang trang con" */
            $idSeoVi            = $item->seo->id ?? 0;
            $countChild         = Seo::select('*')
                                    ->where('link_canonical', $idSeoVi)
                                    ->count();
            /* list danh sách trang chưa đủ content (html) */
            $languageNotEnoughContent = CategoryController::getListPageNotEnoughContent($item);
            return view('admin.categoryBlog.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'language', 'sources', 'parents', 'countChild', 'languageNotEnoughContent', 'message'));
        } else {
            return redirect()->route('admin.categoryBlog.list');
        }
    }

    public function createAndUpdate(Request $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id');
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idCategory         = $request->get('category_blog_id');
            $language           = $request->get('language');
            $categoryType       = 'category_blog';
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
            /* update page */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $categoryType, $dataPath);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
            }
            /* kiểm tra insert thành công không */
            if(!empty($idSeo)){
                /* insert seo_content */
                if(!empty($request->get('content'))) CategoryController::insertAndUpdateContents($idSeo, $request->get('content'));
                if($language=='vi'){
                    /* insert hoặc update category_info */
                    $flagShow           = !empty($request->get('flag_show'))&&$request->get('flag_show')=='on' ? 1 : 0;
                    if(empty($idCategory)){ /* check xem create category hay update category */
                        $idCategory          = CategoryBLog::insertItem([
                            'flag_show'     => $flagShow,
                            'seo_id'        => $idSeo,
                        ]);
                    }else {
                        CategoryBlog::updateItem($idCategory, [
                            'flag_show'     => $flagShow,
                        ]);
                    }
                }
                /* relation_seo_category_info */
                $relationSeoCategoryInfo = RelationSeoCategoryBlog::select('*')
                                        ->where('seo_id', $idSeo)
                                        ->where('category_blog_id', $idCategory)
                                        ->first();
                if(empty($relationSeoCategoryInfo)) RelationSeoCategoryBlog::insertItem([
                    'seo_id'        => $idSeo,
                    'category_blog_id'   => $idCategory
                ]);
                DB::commit();
                /* Message */
                $message        = [
                    'type'      => 'success',
                    'message'   => '<strong>Thành công!</strong> Đã cập nhật Category Blog!'
                ];
                /* nếu có tùy chọn index => gửi google index */
                if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                    $flagIndex = IndexController::indexUrl($idSeo);
                    if($flagIndex==200){
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Category Blog và Báo Google Index!';
                    }else {
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Category Blog <span style="color:red;">nhưng báo Google Index lỗi</span>';
                    }
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
        return redirect()->route('admin.categoryBlog.view', ['id' => $idCategory, 'language' => $language]);
    }

    public function delete(Request $request){
        try {
            DB::beginTransaction();
            
            $id = $request->get('id');

            if (!$id) return false;

            $info       = CategoryBlog::select('*')
                            ->where('id', $id)
                            ->with(['files' => function($query){
                                $query->where('relation_table', 'seo.type');
                            }])
                            ->with('seo', 'seos')
                            ->first();

            // Xoá ảnh đại diện chính
            if (!empty($info->seo->image)) {
                Upload::deleteWallpaper($info->seo->image);
            }

            // Xoá các quan hệ
            $info->blogs()->delete();
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
            
            $info->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }
}
