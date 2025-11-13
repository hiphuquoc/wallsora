<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\HelperController;
use App\Jobs\CheckTranslateOfPage;
use App\Jobs\UpdateSeoAndRemoveCheckTranslate;
use App\Models\CheckTranslate;
use App\Models\Seo;
use App\Helpers\Charactor;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CheckTranslateOfPageController extends Controller {

    public static function list(Request $request){
        $params     = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewCheckTranslateOfPage') ?? 20;
        $params['paginate'] = $viewPerPage;
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = CheckTranslate::select('*')
                                ->orderBy('title_vi', 'DESC')
                                ->paginate($params['paginate']);
        return view('admin.report.checkTranslateOfPage', compact('list', 'params', 'viewPerPage'));
    }

    public static function updatePageCheckTranslateOfPage(Request $request){
        $arraySuccess    = [];
        $arrayNotUpdate = [];
        foreach($request->get('data') as $idSeo => $keyChoose){ /* keyChoose = new | old */
            $infoCheck  = CheckTranslate::select('*')
                            ->where('seo_id', $idSeo)
                            ->with('infoSeo')
                            ->first();
            if($keyChoose=='newest'){
                /* update giá trị mới new_title, new_seo_title, new_seo_description và xóa row*/
                $dataUpdate = [];
                $dataUpdate['title']            = $infoCheck['new_title'];
                $dataUpdate['seo_title']        = $infoCheck['new_seo_title'];
                $dataUpdate['seo_description']  = $infoCheck['new_seo_description'];
                $dataUpdate['slug']             = Charactor::convertStrToUrl($dataUpdate['title']);
                $dataUpdate['slug_full']        = Seo::buildFullUrl($dataUpdate['slug'], $infoCheck->infoSeo->parent);
                /* tiến hành cập nhật - ở đây không cập nhật trực tiếp vì tốn nhiều thời gian - gọi cronjob */
                UpdateSeoAndRemoveCheckTranslate::dispatch($idSeo, $dataUpdate, $infoCheck->id);
                /* cập nhật lại status */
                $infoCheck->update(['status' => 1]);
                /* thêm id vào mảng thông báo */
                $arraySuccess[]                  = $idSeo;
            }else if($keyChoose=='oldest'){
                /* không làm gì cả và xóa row */
                $infoCheck->delete();
                $arrayNotUpdate[] = $idSeo;
            }
        }
        $response = [
            'flag' => true,
            'toast_type' => 'success',
            'toast_title' => 'Thành công!',
            'toast_message' => '👋 Đã báo cập nhật thành công <span class="highLight_700">' . count($arraySuccess) . '</span> trang ngôn ngữ. Giữ nguyên <span class="highLight_700">' . count($arrayNotUpdate) . '</span> trang ngôn ngữ.',
            'array_success' => $arraySuccess,
            'array_not_update'  => $arrayNotUpdate,
        ];
        return response()->json($response);
    }

    public static function checkTranslateOfPage(Request $request) {
        $arrayChecked   = [];
        $idSeo          = $request->get('seo_id') ?? 0;
        $infoPage       = HelperController::getFullInfoPageByIdSeo($idSeo);
        $arrayNotCheck  = ['vi', 'en'];
        foreach($infoPage->seos as $seo){
            if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $arrayNotCheck)){
                CheckTranslateOfPage::dispatch($seo->infoSeo->id, $seo->infoSeo->language);
                $arrayChecked[] = $seo->infoSeo->language;
            }
        }
        $response = [
            'flag' => true,
            'toast_type' => 'success',
            'toast_title' => 'Thành công!',
            'toast_message' => '👋 Đã gửi yêu cầu kiểm tra thành công <span class="highLight_700">' . count($arrayChecked) . '</span> trang ngôn ngữ.'
        ];
        
        return response()->json($response);
    }

    public static function reCheckTranslateOfPage(Request $request){
        $response = [
            'flag' => false,
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Đã xảy ra lỗi. Vui lòng thử lại.'
        ];
        if(!empty($request->get('seo_id'))&&!empty($request->get('language'))){
            $idSeo = $request->get('seo_id');
            $language = $request->get('language');
            $flag   = CheckTranslateOfPage::dispatch($idSeo, $language);
            if($flag == true){
                /* xóa record cũ */
                CheckTranslate::select('*')
                    ->where('seo_id', $idSeo)
                    ->where('language', $language)
                    ->delete();
                /* thông báo */
                $response = [
                    'flag' => true,
                    'toast_type' => 'success',
                    'toast_title' => 'Thành công!',
                    'toast_message' => '👋 Đã gửi yêu cầu kiểm tra lại thành công <span class="highLight_700">1</span> trang ngôn ngữ.'
                ];
            }
        }
        return response()->json($response);
    }
    
}
