<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Helpers\Url;
use App\Models\Product;
use App\Models\Page;
use App\Models\Category;
use App\Models\Style;
use App\Models\Event;

class PageController extends Controller{

    // public static function saleOff(){
    //     $language       = 'vi';
    //     SettingController::settingLanguage($language);
    //     $viewBy         = request()->cookie('view_by') ?? 'each_set';
    //     /* thông tin Page */
    //     $item           = Page::select('*')
    //         ->whereHas('seo', function($query){
    //             $query->where('slug', 'hinh-nen-dien-thoai-khuyen-mai');
    //         })
    //         ->with('seo', 'files')
    //         ->first();
    //         $flagMatch  = true;
    //     /* danh sách product => lấy riêng để dễ truyền vào template */
    //     $products       = Product::select('product_info.*')
    //                         ->join('seo', 'seo.id', '=', 'product_info.seo_id')
    //                         ->whereHas('prices', function($query) {
    //                             $query->where('sale_off', '>', 0);
    //                         })
    //                         ->with('seo', 'en_seo', 'files', 'prices', 'contents', 'categories')
    //                         ->orderBy('seo.ordering', 'DESC')
    //                         ->orderBy('id', 'DESC')
    //                         ->get();
    //     // $totalProduct   = Product::select('*')
    //     //                     ->whereHas('prices', function($query) {
    //     //                         $query->where('sale_off', '>', 0);
    //     //                     })
    //     //                     ->count();
    //     /* breadcrumb */
    //     $breadcrumb         = Url::buildBreadcrumb($item->seo->slug_full, $language);
    //     /* select của filter */
    //     $categories         = Category::all();
    //     $styles             = Style::all();
    //     $events             = Event::all();
    //     return view('wallpaper.category.promotion', compact('item', 'language', 'products', 'categories', 'styles', 'events', 'breadcrumb', 'viewBy'));
    // }

    // public static function enSaleOff(){
    //     $language       = 'en';
    //     SettingController::settingLanguage($language);
    //     $viewBy         = request()->cookie('view_by') ?? 'each_set';
    //     /* thông tin Page */
    //     $item           = Page::select('*')
    //         ->whereHas('en_seo', function($query){
    //             $query->where('slug', 'promotion-phone-wallpapers');
    //         })
    //         ->with('seo', 'en_seo', 'files')
    //         ->first();
    //         $flagMatch  = true;
    //     /* danh sách product => lấy riêng để dễ truyền vào template */
    //     $products       = Product::select('product_info.*')
    //                         ->join('seo', 'seo.id', '=', 'product_info.seo_id')
    //                         ->whereHas('prices', function($query) {
    //                             $query->where('sale_off', '>', 0);
    //                         })
    //                         ->with('seo', 'en_seo', 'files', 'prices', 'contents', 'categories')
    //                         ->orderBy('seo.ordering', 'DESC')
    //                         ->orderBy('id', 'DESC')
    //                         ->get();
    //     // $totalProduct   = Product::select('*')
    //     //                     ->whereHas('prices', function($query) {
    //     //                         $query->where('sale_off', '>', 0);
    //     //                     })
    //     //                     ->count();
    //     /* breadcrumb */
    //     $breadcrumb     = Url::buildBreadcrumb($item->seo->slug_full, $language);
    //     /* select của filter */
    //     $categories         = Category::all();
    //     $styles             = Style::all();
    //     $events             = Event::all();
    //     return view('wallpaper.category.promotion', compact('item', 'language', 'products', 'categories', 'styles', 'events', 'breadcrumb', 'viewBy'));
    // }

    // public static function searchProduct(Request $request){
    //     $keySearch      = $request->get('key_search') ?? null;
    //     $keySearch      = \App\Helpers\Charactor::convertStringSearch($request->get('key_search'));
    //     $language       = 'vi';
    //     SettingController::settingLanguage($language);
    //     $viewBy         = request()->cookie('view_by') ?? 'each_set';
    //     /* thông tin Page */
    //     $pathUrl        = substr(parse_url(url()->current())['path'], 1);
    //     $item           = Page::select('*')
    //         ->whereHas('seo', function($query) use($pathUrl){
    //             $query->where('slug_full', $pathUrl);
    //         })
    //         ->with('seo', 'files')
    //         ->first();
    //     if(!empty($item)){
    //         /* danh sách product */
    //         $products       =  Product::select('product_info.*')
    //             ->join('seo', 'seo.id', '=', 'product_info.seo_id')
    //             ->where('code', 'like', '%'.$keySearch.'%')
    //             ->orWhere('name', 'like', '%'.$keySearch.'%')
    //             ->orWhere('en_name', 'like', '%'.$keySearch.'%')
    //             ->with('seo', 'en_seo', 'files', 'prices', 'contents', 'categories')
    //             ->orderBy('seo.ordering', 'DESC')
    //             ->orderBy('id', 'DESC')
    //             ->get();
    //         /* breadcrumb */
    //         $breadcrumb     = Url::buildBreadcrumb($item->seo->slug_full, $language);
    //         /* select của filter */
    //         $categories     = Category::all();
    //         $styles         = Style::all();
    //         $events         = Event::all();
    //         $titlePage      = $item->name ?? $item->seo->title ?? null;
    //         return view('wallpaper.category.search', compact('item', 'language', 'titlePage', 'products', 'categories', 'styles', 'events', 'breadcrumb', 'viewBy'));
    //     }
    //     return redirect()->route('main.home');
    // }

    // public static function enSearchProduct(Request $request){
    //     $keySearch      = $request->get('key_search') ?? null;
    //     $keySearch      = \App\Helpers\Charactor::convertStringSearch($request->get('key_search'));
    //     $language       = 'en';
    //     SettingController::settingLanguage($language);
    //     $viewBy         = request()->cookie('view_by') ?? 'each_set';
    //     /* thông tin Page */
    //     $pathUrl        = substr(parse_url(url()->current())['path'], 1);
    //     $item           = Page::select('*')
    //         ->whereHas('en_seo', function($query) use($pathUrl){
    //             $query->where('slug_full', $pathUrl);
    //         })
    //         ->with('seo', 'en_seo', 'files')
    //         ->first();
    //     if(!empty($item)){
    //         /* danh sách product */
    //         $products       =  Product::select('product_info.*')
    //             ->join('seo', 'seo.id', '=', 'product_info.seo_id')
    //             ->where('code', 'like', '%'.$keySearch.'%')
    //             ->orWhere('name', 'like', '%'.$keySearch.'%')
    //             ->orWhere('en_name', 'like', '%'.$keySearch.'%')
    //             ->with('seo', 'en_seo', 'files', 'prices', 'contents', 'categories')
    //             ->orderBy('seo.ordering', 'DESC')
    //             ->orderBy('id', 'DESC')
    //             ->get();
    //         // $totalProduct   =  Product::select('product_info.*')
    //         //     ->where('code', 'like', '%'.$keySearch.'%')
    //         //     ->orWhere('name', 'like', '%'.$keySearch.'%')
    //         //     ->orWhere('en_name', 'like', '%'.$keySearch.'%')
    //         //     ->count();
    //         /* breadcrumb */
    //         $breadcrumb     = Url::buildBreadcrumb($item->seo->slug_full, $language);
    //         /* select của filter */
    //         $categories         = Category::all();
    //         $styles             = Style::all();
    //         $events             = Event::all();
    //         $titlePage      = $item->en_name ?? $item->en_seo->title ?? null;
    //         return view('wallpaper.category.search', compact('item', 'language', 'titlePage', 'products', 'categories', 'styles', 'events', 'breadcrumb', 'viewBy'));
    //     }
    //     return redirect()->route('main.home');
    // }

}
