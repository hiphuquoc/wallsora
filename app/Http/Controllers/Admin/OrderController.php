<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Helpers\Upload;
use App\Http\Requests\BrandRequest;
use App\Models\Seo;
use App\Models\Order;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;

class OrderController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    // public function update(BrandRequest $request){
    //     try {
    //         DB::beginTransaction();
    //         $idSeo              = $request->get('seo_id');
    //         $idBrand         = $request->get('brand_info_id');
    //         /* upload image */
    //         $dataPath           = [];
    //         if($request->hasFile('image')) {
    //             $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
    //             $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
    //         }
    //         /* update page */
    //         $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'brand_info', $dataPath);
    //         Seo::updateItem($idSeo, $insertSeo);
    //         /* insert brand_info */
    //         Brand::updateItem($idBrand, [
    //             'name'          => $request->get('name'),
    //             'description'   => $request->get('description')
    //         ]);
    //         /* insert slider và lưu CSDL */
    //         if($request->hasFile('slider')&&!empty($idBrand)){
    //             $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
    //             $params         = [
    //                 'attachment_id'     => $idBrand,
    //                 'relation_table'    => 'brand_info',
    //                 'name'              => $name
    //             ];
    //             SliderController::upload($request->file('slider'), $params);
    //         }
    //         /* insert gallery và lưu CSDL */
    //         if($request->hasFile('gallery')&&!empty($idBrand)){
    //             $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
    //             $params         = [
    //                 'attachment_id'     => $idBrand,
    //                 'relation_table'    => 'brand_info',
    //                 'name'              => $name
    //             ];
    //             GalleryController::upload($request->file('gallery'), $params);
    //         }
    //         DB::commit();
    //         /* Message */
    //         $message        = [
    //             'type'      => 'success',
    //             'message'   => '<strong>Thành công!</strong> Đã cập nhật Nhãn hàng!'
    //         ];
    //     } catch (\Exception $exception){
    //         DB::rollBack();
    //         /* Message */
    //         $message        = [
    //             'type'      => 'danger',
    //             'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
    //         ];
    //     }
    //     $request->session()->put('message', $message);
    //     return redirect()->route('admin.brand.view', ['id' => $idBrand]);
    // }

    public static function view(Request $request){
        // $message            = $request->get('message') ?? null;
        // $id                 = $request->get('id') ?? 0;
        // $item               = Brand::select('*')
        //                         ->where('id', $id)
        //                         ->with(['files' => function($query){
        //                             $query->where('relation_table', 'brand_info');
        //                         }])
        //                         ->with('seo')
        //                         ->first();
        // /* type */
        // $type               = !empty($item) ? 'edit' : 'create';
        // $type               = $request->get('type') ?? $type;
        // return view('admin.brand.view', compact('item', 'type', 'message'));
    }

    public static function viewExport(Request $request){
        $order          = Order::select('*')
                            ->where('id', $request->get('id'))
                            ->with('products.infoProduct', 'products.infoPrice', 'paymentMethod')
                            ->first();
        // dd($request->all());
        return view('admin.order.viewExport', compact('order'));
    }

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewOrderInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Order::getList($params);
        return view('admin.order.list', compact('list', 'viewPerPage', 'params'));
    }
}
