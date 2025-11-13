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
use App\Models\ApiAI;
use App\Models\RelationSeoEnSeo;
use App\Models\Page;
use App\Models\Prompt;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use Illuminate\Support\Facades\Storage;

class ApiAIController extends Controller {

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewPromptInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = ApiAI::all();
        return view('admin.apiai.list', compact('list', 'viewPerPage', 'params'));
    }

    public static function changeApiActive(Request $request){
        $id     = $request->get('id') ?? 0;
        $action = $request->get('is_checked')=='true' ? 'active' : 'unactive';
        $infoApiAI = ApiAI::find($id);
        if($action=='active'){
            /* unactive tất cả api cùng loại */
            $tmp = ApiAI::select('*')
                    ->where('type', $infoApiAI->type)
                    ->get();
            foreach($tmp as $t){
                ApiAI::updateItem($t->id, [
                    'status' => 0
                ]);
            }
            /* active api được chọn */
            ApiAI::updateItem($id, [
                'status' => 1
            ]);
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật xong!',
                'redirect'  => route('admin.apiai.list'),
            ];
        } else {
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Không thể tắt API '.$infoApiAI->type.' duy nhất được kích hoạt!',
                'redirect'  => route('admin.apiai.list'),
            ];
        }
        $request->session()->put('message', $message);
        return response()->json($message); // Trả về JSON response
    }

    // public static function delete(Request $request){
    //     if(!empty($request->get('id'))){
    //         try {
    //             DB::beginTransaction();
    //             $id         = $request->get('id');
    //             $info       = Prompt::select('*')
    //                             ->where('id', $id)
    //                             ->first();
    //             $info->delete();
    //             DB::commit();
    //             return true;
    //         } catch (\Exception $exception){
    //             DB::rollBack();
    //             return false;
    //         }
    //     }
    // }

    // public static function loadColumnTable(Request $request){
    //     $response   = '';
    //     $id         = $request->get('prompt_info_id') ?? 0;
    //     $tableName = $request->get('table_name');
    //     $tmp        = DB::table($tableName)
    //                     ->join('seo', 'seo.id', '=', $tableName.'.seo_id')
    //                     ->where('seo.id', '>', 0)
    //                     ->first();
    //     /* thêm 2 cột khác */
    //     $tmp->content   = null;
    //     $tmp->tag       = null;
    //     $tmp->category  = null;
    //     /* lấy tên cột */
    //     $arrayAccept    = [
    //         'title', 'description', 'seo_title', 'seo_description', 'slug', 'link_canonical', 'content', 'tag', 'category'
    //     ];
    //     /* lấy thông tin prompt */
    //     $infoPrompt     = Prompt::select('*')
    //                         ->where('id', $id)
    //                         ->first();
    //     foreach($tmp as $key => $value){
    //         if(in_array($key, $arrayAccept)) {
    //             $selected   = null;
    //             if(!empty($infoPrompt->reference_name)&&$infoPrompt->reference_name==$key) $selected = 'selected';
    //             $response   .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';
    //         }
    //     }
                        
    //     echo $response;
    // }
}
