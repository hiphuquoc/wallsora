<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Helpers\Upload;
use App\Http\Requests\PromptRequest;
use App\Models\Seo;
use App\Models\EnSeo;
use App\Models\RelationSeoEnSeo;
use App\Models\Page;
use App\Models\Prompt;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use Illuminate\Support\Facades\Storage;

class PromptController extends Controller {

    public function createAndUpdate(PromptRequest $request){
        try {
            DB::beginTransaction();

            $id                 = $request->get('prompt_info_id') ?? 0;
            $arrayData          = [
                'type'              => $request->get('type'),
                'reference_table'   => $request->get('reference_table'),
                'reference_name'    => $request->get('reference_name'),
                'reference_prompt'  => $request->get('reference_prompt'),
                'tool'              => $request->get('tool'),
                'version'           => $request->get('version'),
            ];
            if(!empty($id)){
                /* update */
                Prompt::updateItem($id, $arrayData);
            }else {
                /* insert */
                $id = Prompt::insertItem($arrayData);
            }
            

            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Prompt!'
            ];
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.prompt.view', ['id' => $id]);
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $item               = Prompt::select('*')
                                ->where('id', $id)
                                ->first();
        $tmp                = Seo::select('type')
                                ->distinct()
                                ->pluck('type');
        /* lấy danh sách bảng */
        $tables             = [];
        $categoryType       = [];
        /* lọc với bảng trùng của category_info */
        foreach(config('main_'.env('APP_NAME').'.category_type') as $cType) $categoryType[] = $cType['key'];
        foreach($tmp as $t){
            if($t=='category_info'||!in_array($t, $categoryType)){
                if(!in_array($t, $tables)) {
                    $tables[] = $t;
                }
            }
        }
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.prompt.view', compact('item', 'type', 'tables', 'message'));
    }

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewPromptInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Prompt::getList($params);
        return view('admin.prompt.list', compact('list', 'viewPerPage', 'params'));
    }

    public static function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Prompt::select('*')
                                ->where('id', $id)
                                ->first();
                $info->delete();
                DB::commit();
                return true;
            } catch (\Exception $exception){
                DB::rollBack();
                return false;
            }
        }
    }

    public static function loadColumnTable(Request $request){
        $response   = '';
        $id         = $request->get('prompt_info_id') ?? 0;
        $tableName = $request->get('table_name');
        $tmp        = DB::table($tableName)
                        ->join('seo', 'seo.id', '=', $tableName.'.seo_id')
                        ->where('seo.id', '>', 0)
                        ->first();
        /* thêm 2 cột khác */
        $tmp->content   = null;
        $tmp->tag       = null;
        $tmp->category  = null;
        /* lấy tên cột */
        $arrayAccept    = [
            'title', 'description', 'seo_title', 'seo_description', 'slug', 'link_canonical', 'content', 'tag', 'category'
        ];
        /* lấy thông tin prompt */
        $infoPrompt     = Prompt::select('*')
                            ->where('id', $id)
                            ->first();
        foreach($tmp as $key => $value){
            if(in_array($key, $arrayAccept)) {
                $selected   = null;
                if(!empty($infoPrompt->reference_name)&&$infoPrompt->reference_name==$key) $selected = 'selected';
                $response   .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';
            }
        }
                        
        echo $response;
    }

    public static function getPromptTextById(Request $request){
        /* Thông báo mặc định */
        $response = [
            'flag'      => false,
            'content' => '',
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Không tìm thấy thông tin prompt.'
        ];
        $language   = $request->get('language') ?? 'vi';
        $idSeo      = $request->get('seo_id') ?? 0;
        $infoPage   = HelperController::getFullInfoPageByIdSeo($idSeo);
        $idPrompt   = $request->get('prompt_info_id') ?? 0;
        $infoPrompt = Prompt::find($idPrompt);

        $promptText = ChatGptController::convertPrompt($infoPage, $infoPrompt, $language);
        if(!empty($promptText)){
            $response = [
                'flag'      => true,
                'content' => $promptText,
                'toast_type' => 'success',
                'toast_title' => 'Thành công!',
                'toast_message' => 'Đã sao chép 1 prompt vào Clipboard!'
            ];
        }
        return response()->json($response);
    }
}
