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

class ImproveTranslateController extends Controller {


    public static function updateNotes(Request $request) {
        /* Thông báo mặc định */
        $response = [
            'flag' => false,
            'toast_type' => 'error',
            'toast_title' => 'Thất bại!',
            'toast_message' => '❌ Đã xảy ra lỗi khi cập nhật thông tin. Vui lòng thử lại.'
        ];
        /* lấy dữ liệu */
        $idSeoVi    = $request->get('seo_id');
        $notes      = $request->get('notes') ?? '';
        /* lấy thông tin trang */
        $infoPage   = HelperController::getFullInfoPageByIdSeo($idSeoVi);
        /* cập nhật cơ sở dữ liệu */
        $flag       = $infoPage->update(['notes' => $notes]);
        /* trả thông báo */
        if($flag==true){
            $response = [
                'flag' => true,
                'toast_type' => 'success',
                'toast_title' => 'Thành công!',
                'toast_message' => '👋 Đã cập nhật notes thành công với nội dung <span class="highLight_500">' . $notes . '</span>.'
            ];
        }
        return response()->json($response);
    }

}
