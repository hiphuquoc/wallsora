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

class TranslateController extends Controller {

    public static function list(Request $request){
        $params = [];
        
        /* paginate */
        $viewPerPage        = Cookie::get('viewTranslateReport') ?? 20;
        $params['paginate'] = $viewPerPage;
    
        /* Search theo ... */
        $params['search_status'] = $request->get('search_status') ?? 0;
        if (!empty($request->get('search_language'))) {
            $params['search_language'] = $request->get('search_language');
        }
    
        $list = Seo::select('*')
            ->whereHas('jobAutoTranslate', function ($query) use ($params) {
                $query->whereColumn('job_auto_translate.language', 'language')
                      ->where('status', $params['search_status']);
    
                if (!empty($params['search_language'])) {
                    $query->where('language', $params['search_language']);
                }
            })
            ->with(['contents', 'jobAutoTranslatelinks'])
            ->with(['jobAutoTranslate' => function ($query) {
                $query->whereColumn('job_auto_translate.language', 'language');
            }])
            ->paginate($params['paginate']);
    
        return view('admin.report.listAutoTranslateContent', compact('list', 'params', 'viewPerPage'));
    } 

    public static function reRequestTranslate(Request $request){
        $idSeoByLanguage        = $request->get('id_seo');
        $language               = $request->get('language');
        /* xóa báo cáo cũ */
        $request    = new Request(['id' => $idSeoByLanguage]);
        $result     = self::delete($request);
        /* lấy trang theo ngôn ngữ */
        $infoPage               = \App\Http\Controllers\Admin\HelperController::getFullInfoPageByIdSeo($idSeoByLanguage);
        $idSeoVI                = $infoPage->seo->id;
        /* tạo lại */
        $flag       = self::createJobTranslateContent($idSeoVI, $language);
        return response()->json($flag);
    }

    public static function createMultiJobTranslateContent(Request $request){
        /* Thông báo mặc định */
        $response = [
            'flag' => false,
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'
        ];
        /* lấy dữ liệu từ request */
        $slugVi     = $request->get('slug_vi');
        $option     = $request->get('option');
        $slug       = self::getSlugByUrl($slugVi);
        /* lấy thông tin trang gốc - vi */
        $tmp        = Seo::select('*')
                        ->where('slug', $slug)
                        ->first();
        $arrayIdSeoRequested = [];
        if(!empty($tmp->id)&&!empty($option)){
            /* lấy thông tin trang */
            $infoPage =     HelperController::getFullInfoPageByIdSeo($tmp->id);
            /* duyệt sang mảng để tạo yêu cầu */
            if(!empty($infoPage)){
                /* các option tương ứng giá trị nhận vào từ input trong function createMultiJobTranslateContent
                    option = 1 => Dịch nội dung *chỉ trang EN - nội dung có sẵn sẽ bị đè
                    option = 2 => Dịch nội dung tất cả các ngôn ngữ *ngoại trừ EN - nội dung có sẵn sẽ bị đè
                    option = 3 => Dịch các ngôn ngữ chưa đủ nội dung
                */
                switch ($option) {
                    case '1':
                        foreach($infoPage->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language=='en'){
                                $flag = self::createJobTranslateContent($infoPage->seo->id, $seo->infoSeo->language);
                                if($flag==true) $arrayIdSeoRequested[] = $infoPage->seo->id;
                            }
                        }
                        break;
                    case '2':
                        $arrayPrevent = ['vi', 'en'];
                        foreach($infoPage->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $arrayPrevent)){
                                $flag = self::createJobTranslateContent($infoPage->seo->id, $seo->infoSeo->language);
                                if($flag==true) $arrayIdSeoRequested[] = $infoPage->seo->id;
                            }
                        }
                        break;
                    default:
                        $arrayTranslate     = $request->get('array_language');
                        foreach($infoPage->seos as $seo){
                            if(in_array($seo->infoSeo->language, $arrayTranslate)) {
                                $flag = self::createJobTranslateContent($infoPage->seo->id, $seo->infoSeo->language);
                                if($flag==true) $arrayIdSeoRequested[] = $infoPage->seo->id;
                            }
                        }
                        break;
                }
                /* Cập nhật thông báo */
                $count      = count($arrayIdSeoRequested);
                $response = [
                    'flag' => true,
                    'toast_type' => 'success',
                    'toast_title' => 'Thành công!',
                    'toast_message' => '👋 Đã gửi yêu cầu dịch nội dung cho <span class="highLight_500">' . $count . '</span> ngôn ngữ của trang <span class="highLight_500">' . $infoPage->seo->title . '</span>!'
                ];
            }
        }
        return response()->json($response);
    }

    public static function createJobTranslateContentAjax(Request $request){
        /* Thông báo mặc định */
        $response = [
            'flag' => false,
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'
        ];
        $idSeoVI        = $request->get('id_seo_vi');
        $language       = $request->get('language');
        $flag           = self::createJobTranslateContent($idSeoVI, $language);
        if($flag==true){
            $response = [
                'flag' => true,
                'toast_type' => 'success',
                'toast_title' => 'Thành công!',
                'toast_message' => '👋 Đã gửi yêu cầu dịch nội dung của ngôn ngữ <span class="highLight_500">' . $language . '</span> cho trang này!'
            ];
        }
        return response()->json($response);
    }

    public static function createJobTranslateContent($idSeoVI, $language, $arrayOrdering = []){ /* biến arrayOrdering quy định chỉ thực hiện những ordering nhất định - rỗng thì làm tất cả */
        $flag                   = false;
        /* lấy trang theo ngôn ngữ */
        $infoPage               = \App\Http\Controllers\Admin\HelperController::getFullInfoPageByIdSeo($idSeoVI);
        $idSeo                  = 0;
        if (!empty($infoPage->seos)) {
            foreach($infoPage->seos as $seo){
                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language) {
                    $idSeo = $seo->infoSeo->id;
                    break;
                }
            }
        }
        if (!empty($idSeo)&&$language!='vi') {
            /* kiểm tra xem có phải đang chạy có bất kì row status = 0 */
            $infoFlag   = JobAutoTranslate::select('*')
                ->where('seo_id', $idSeo)
                ->where('status', 0)
                ->first();
            if (empty($infoFlag)||!empty($arrayOrdering)) { /* có quy định ordering nào mới được thực hiện thì bỏ qua bước kiểm tra */
                /* lấy content bảng tiếng việt */
                foreach($infoPage->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language=='vi'){
                        $contents = $seo->infoSeo->contents;
                        break;
                    }
                }
                /* duyệt qua từng box content để xử lý */
                foreach ($contents as $content) {
                    /* lấy ordering làm key */
                    $ordering   = $content->ordering;
                    /* kiểm tra xem ordering này có được thực hiện không */
                    $flagCallJob = false;
                    if(!empty($arrayOrdering)){
                        if(in_array($ordering, $arrayOrdering)) {
                            $flagCallJob = true;
                        }
                    }else {
                        $flagCallJob = true;
                    }
                    /* thực thi */
                    if($flagCallJob==true){
                        /* tạo đánh dấu đang và đã thực hiện tính năng */
                        JobAutoTranslate::select('*')
                            ->where('seo_id', $idSeo)
                            ->where('ordering', $ordering)
                            ->where('language', $language)
                            ->delete();
                        JobAutoTranslate::insertItem([
                            'seo_id'    => $idSeo,
                            'ordering'  => $ordering,
                            'language'  => $language
                        ]);
                        /* lấy prompt đang được áp dụng cho content */
                        $type       = HelperController::determinePageType($infoPage->seo->type);
                        $infoPrompt = Prompt::select('*')
                                        ->where('reference_name', 'content')
                                        ->where('type', 'translate_content')
                                        ->where('reference_table', $type)
                                        ->first();
                        /* tạo job */
                        AutoTranslateContent::dispatch($ordering, $language, $idSeo, $infoPrompt->id);
                    }
                }
                $flag = true;
            }
        }
        return $flag;
    }

    public static function createJobTranslateAndCreatePageAjax(Request $request) {
        /* Thông báo mặc định */
        $response = [
            'flag' => false,
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'
        ];
    
        /* Lấy thông tin */
        $slugVi = $request->get('slug_vi');
        $slug = self::getSlugByUrl($slugVi);
    
        /* Lấy thông tin trang gốc - vi */
        $seoRecord = Seo::where('slug', $slug)->first();
    
        if ($seoRecord) {
            /* Lấy thông tin đầy đủ của trang */
            $infoPage = HelperController::getFullInfoPageByIdSeo($seoRecord->id);
    
            if ($infoPage) {
                $arrayLanguageRequested = self::createJobTranslateAndCreatePage($infoPage);
                $count = count($arrayLanguageRequested) ?? 0;
    
                /* Cập nhật thông báo */
                $response = [
                    'flag' => true,
                    'toast_type' => 'success',
                    'toast_title' => 'Thành công!',
                    'toast_message' => '👋 Đã gửi yêu cầu tạo <span class="highLight_500">' . $count . '</span> ngôn ngữ cho trang <span class="highLight_500">' . $infoPage->seo->title . '</span>!'
                ];
            }
        }
    
        return response()->json($response);
    }

    public static function createJobWriteContent(Request $request) {
        /* Thông báo mặc định */
        $response = [
            'flag' => false,
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'
        ];
    
        /* Lấy thông tin */
        $idSeo      = $request->get('seo_id') ?? 0;
        /* Lấy thông tin đầy đủ của trang */
        $infoPage   = HelperController::getFullInfoPageByIdSeo($idSeo);
        $typePage   = HelperController::determinePageType($infoPage->seo->type);
        $prompts    = Prompt::select('*')
                        ->where('reference_table', $typePage)
                        ->where('type', 'auto_content')
                        ->where('reference_name', 'content')
                        ->get();
        if(!empty($prompts)&&$prompts->isNotEmpty()){

            $count      = 0;
            foreach($prompts as $prompt){
                AutoWriteContent::dispatch($prompt->ordering, $idSeo, $prompt->id);
                ++$count;
            }
            
            /* Cập nhật thông báo */
            $response = [
                'flag' => true,
                'toast_type' => 'success',
                'toast_title' => 'Thành công!',
                'toast_message' => '👋 Đã gửi yêu cầu viết nội dung <span class="highLight_500">' . $count . '</span> box cho trang <span class="highLight_500">' . $infoPage->seo->title . '</span>!'
            ];
        }
        
        return response()->json($response);
    }

    private static function createJobTranslateAndCreatePage($infoPage) { /* function tự động tạo ra các trang ngôn ngữ khác gồm title, seo_title, seo_description, slug */
        $arrayLanguageRequested = [];
        if (!empty($infoPage)) {
            /* xây dựng aray của những ngôn ngữ đã có trang */
            $arrayLanguageHas       = [];
            $i                      = 0;
            foreach ($infoPage->seos as $p) {
                if (!empty($p->infoSeo->language)) {
                    $arrayLanguageHas[$i]['key']    = $p->infoSeo->language;
                    $arrayLanguageHas[$i]['title']  = $p->infoSeo->title;
                    $arrayLanguageHas[$i]['seo_title']    = $p->infoSeo->seo_title;
                    $arrayLanguageHas[$i]['seo_description']    = $p->infoSeo->seo_description;
                    $arrayLanguageHas[$i]['slug']    = $p->infoSeo->slug;
                    $arrayLanguageHas[$i]['parent']    = $p->infoSeo->parent;
                    ++$i;
                }
            }
            /* xây dựng array của những ngôn ngữ chưa có trang */
            $arrayLanguageNonHas    = [];
            $i                      = 0;
            foreach (config('language') as $ld) {
                foreach ($arrayLanguageHas as $lh) {
                    $flag           = false;
                    if ($lh['key'] == $ld['key']) {
                        $flag       = true;
                        break;
                    }
                }
                if ($flag == false) {
                    $arrayLanguageNonHas[$i]['key'] = $ld['key'];
                    $arrayLanguageNonHas[$i]['name'] = $ld['name'];
                    $arrayLanguageNonHas[$i]['name_by_language'] = $ld['name_by_language'];
                    ++$i;
                }
            }
            /* phân chia -> mỗi lần thực hiện 5 ngôn ngữ */
            $numberPertime          = 5;
            $chunkedArrays          = array_chunk($arrayLanguageNonHas, $numberPertime);
            foreach ($chunkedArrays as $arrayLanguageTranslate) {
                $flag = AutoTranslateAndCreatePage::dispatch($arrayLanguageHas, $arrayLanguageTranslate, $infoPage);
                if($flag) $arrayLanguageRequested = array_merge($arrayLanguageRequested, $arrayLanguageTranslate);
            }
        }
        return $arrayLanguageRequested;
    }

    public static function autoTranslateMissing(Request $request) {
        /* Thông báo mặc định */
        $response = [
            'flag' => false,
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'
        ];
    
        /* Lấy thông tin */
        $list   = Seo::select('*')
                    ->whereHas('jobAutoTranslate', function ($query) {
                        $query->where('status', 0);
                    })
                    ->with('contents', 'jobAutoTranslatelinks', 'jobAutoTranslate')
                    ->get();
        /* gọi job */
        $count  = 0;
        foreach($list as $item){
            foreach($item->jobAutoTranslate as $job){
                if($job->status==0){
                    $idSeo          = $job->seo_id;
                    $language       = $job->language;
                    $ordering       = $job->ordering;
                    $arrayOrdering  = [ $ordering ];
                    /* xóa trước mới gọi được job */
                    JobAutoTranslate::select('*')
                        ->where('seo_id', $idSeo)
                        ->where('ordering', $ordering)
                        ->where('language', $language)
                        ->delete();
                    /* gọi job */
                    $flag = self::createJobTranslateContent($idSeo, $language, $arrayOrdering);
                    if($flag) ++$count;
                }
            }
        }

        /* Cập nhật thông báo */
        $response = [
            'flag' => true,
            'toast_type' => 'success',
            'toast_title' => 'Thành công!',
            'toast_message' => '👋 Đã gửi yêu dịch lại nội dung của <span class="highLight_500">' . $count . '</span> box cho các trang còn thiếu!'
        ];
        
        return response()->json($response);
    }

    public static function getSlugByUrl($url){
        $slug   = '';
        if(!empty($url)){
            $tmp        = explode('/', $url);
            $slug       = $tmp[count($tmp) - 1];
        }
        return $slug;
    }

    public static function redirectEdit(Request $request){
        $language   = $request->get('language');
        $infoPage   = HelperController::getFullInfoPageByIdSeo($request->get('id_seo_by_language'));
        $idPage     = $infoPage->id;
        $type       = $infoPage->seo->type;
        $type       = HelperController::determinePageType($type);
        $tmp        = explode('_', $type);
        $typeRoute  = $tmp[0];
    
        // dd($typeRoute);
        // Tạo URL thay vì redirect
        $url = route('admin.'.$typeRoute.'.view', [
            'id'        => $idPage,
            'language'  => $language,
        ]);
    
        // Trả về URL
        return response()->json(['url' => $url]);
    }

    public static function delete(Request $request) {
        if (!empty($request->get('id'))) {
            try {
                DB::beginTransaction();
                $idSeo      = $request->get('id');
                $info       = Seo::select('*')
                    ->where('id', $idSeo)
                    ->with('jobAutoTranslate', 'jobAutoTranslateLinks')
                    ->first();
                $info->jobAutoTranslate()->delete();
                $info->jobAutoTranslateLinks()->delete();
                DB::commit();
                return true;
            } catch (\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }
}
