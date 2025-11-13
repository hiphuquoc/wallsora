<?php

namespace App\Http\Controllers\Admin;

use App\Models\RedirectInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class RedirectController extends Controller {
    
    public function list(Request $request){
        $params             = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewRedirectInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = RedirectInfo::getList($params);
        return view('admin.redirect.list', compact('list', 'viewPerPage'));
    }

    public function create(Request $request){
        $id             = 0;
        /* Message */
        $message        = [
            'type'      => 'danger',
            'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
        ];
        if(!empty($request->get('old_url'))&&!empty($request->get('new_url'))){
            $urlOld     = self::filterUrl($request->get('old_url'));
            $urlNew     = self::filterUrl($request->get('new_url'));
            self::createRedirectAndFix($urlOld, $urlNew);
            if(!empty($id)){
                /* Message */
                $message        = [
                    'type'      => 'success',
                    'message'   => '<strong>Thành công!</strong> Đã thêm redirect mới'
                ];
            }
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.redirect.list', ['id' => $id]);
    }

    public static function createRedirectAndFix($urlOld, $urlNew){
        $id = 0;
        /* xóa những bản redirect trùng */
        RedirectInfo::select('*')
                ->where('old_url', $urlOld)
                ->delete();
        RedirectInfo::select('*')
                ->where('old_url', $urlNew)
                ->where('new_url', $urlOld)
                ->delete();
        /* insert */
        $id         = RedirectInfo::insertItem([
        'old_url'   => $urlOld,
        'new_url'   => $urlNew
        ]);
        return $id;
    }

    public function delete(Request $request){
        $flag       = false;
        if(!empty($request->get('id'))){
            $flag   = RedirectInfo::find($request->get('id'))->delete();
        }
        echo $flag;
    }

    public static function filterUrl($input){
        $output     = null;
        if(!empty($input)){
            /* bỏ tên miền */
            $output     = str_replace(env('APP_URL'), '', $input);
            /* thêm / nếu không có */
            if(substr($output, 0, 1)!='/') $output = '/'.$output;
        }
        return $output;
    }
    
}
