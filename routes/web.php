<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController as CategoryPublic;
use App\Http\Controllers\CategoryMoneyController as CategoryMoneyPublic;
use App\Http\Controllers\VNPayController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConfirmController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController as OrderPublic;
use App\Http\Controllers\CategoryBlogController as CategoryBlogPublic;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SettingController as SettingPublic;
use App\Http\Controllers\SearchController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CategoryBlogController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Admin\WallpaperController;
use App\Http\Controllers\Admin\FreeWallpaperController;
use App\Http\Controllers\Admin\SeoFreeWallpaperController;
use App\Http\Controllers\Admin\ProductPriceController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\PromptController;
use App\Http\Controllers\Admin\ApiAIController;
use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\ImproveController;
use App\Http\Controllers\Admin\ImproveTranslateController;
use App\Http\Controllers\Admin\CheckTranslateOfPageController;
use App\Http\Controllers\Admin\HelperController;
use App\Http\Controllers\Admin\TranslateController;
use App\Http\Controllers\CheckOnpageController;

use App\Http\Controllers\Auth\ProviderController;
use App\Http\Controllers\GoogledriveController;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/call-jobs', function () {
//     include base_path('callMultiJobs.php');
// });
// === SITEMAP ROUTES - ĐẶT TRÊN CÙNG, TRƯỚC MỌI ROUTE KHÁC ===
Route::get('sitemap.xml', fn() => SitemapController::serve('sitemap.xml'))
    ->name('sitemap.main');

Route::get('sitemap/{type}.xml', fn($type) => SitemapController::serve("sitemap/{$type}.xml"))
    ->where('type', '[a-zA-Z0-9_-]+')
    ->name('sitemap.child');

Route::get('sitemap/{language}/{type}.xml', function ($language, $type) {
    if (preg_match('/^(.+)-(\d+)(\.xml)?$/', $type, $m)) {
        $file = "sitemap/{$language}/{$m[1]}-{$m[2]}.xml";
    } else {
        $file = "sitemap/{$language}/{$type}.xml";
    }
    return SitemapController::serve($file);
})
    ->where('language', '[a-z]{2}')
    ->where('type', '[a-zA-Z0-9_-]+(\-\d+)?')
    ->name('sitemap.childForLanguage');
/* thiết lập giao diện */
Route::get('/setViewMode', [AjaxController::class, 'setViewMode'])->name('main.setViewMode');
/* login */
Route::get('/he-thong', [LoginController::class, 'loginForm'])->name('admin.loginForm');
Route::post('/loginAdmin', [LoginController::class, 'loginAdmin'])->name('admin.loginAdmin');
Route::post('/loginCustomer', [LoginController::class, 'loginCustomer'])->name('admin.loginCustomer');
Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');
Route::get('/createUser', [LoginController::class, 'create'])->name('admin.createUser');
/* login với google */
Route::get('/setCsrfFirstTime', [CookieController::class, 'setCsrfFirstTime'])->name('main.setCsrfFirstTime');
Route::post('/auth/google/callback', [ProviderController::class, 'googleCallback'])->name('main.google.callback');
/* Url IPN (bên thứ 3) => để VNPay gọi qua check (1 lần nữa) xem đơn hàng xác nhận chưa => trong trường hợp mạng khách hàng có vấn đề */
Route::post('/vnpay/url_ipn', [VNPayController::class, 'handleIPN'])->name('main.vnpay.ipn');
/* redis test */
Route::get('/redis-test', function () {
    try {
        Cache::put('redis_test', 'ok', 10);
        return Cache::get('redis_test') === 'ok' ? 'Redis hoạt động' : 'Redis không lưu được';
    } catch (\Exception $e) {
        return 'Lỗi Redis: ' . $e->getMessage();
    }
});
Route::get('/redis-keys', function () {
    try {
        $keys = Redis::connection('default')->keys('*');
        return response()->json($keys);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/redis-info', function () {
    try {
        $info = Redis::connection('default')->info();
        return response()->json($info);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
Route::middleware(['auth', 'role:admin', 'check.admin.subdomain'])->prefix('he-thong')->group(function () {
    /* ===== AI ===== */
    Route::get('/chatGpt', [ChatGptController::class, 'chatGpt'])->name('main.chatGpt');
    Route::get('/improveContent', [ImproveController::class, 'improveContent'])->name('main.improveContent');
    Route::post('/updateNotes', [ImproveTranslateController::class, 'updateNotes'])->name('admin.updateNotes');
    Route::get('/list', [CheckTranslateOfPageController::class, 'list'])->name('admin.checkTranslateOfPage.list');
    Route::post('/checkTranslateOfPage', [CheckTranslateOfPageController::class, 'checkTranslateOfPage'])->name('admin.checkTranslateOfPage.callAI');
    Route::post('/updatePageCheckTranslateOfPage', [CheckTranslateOfPageController::class, 'updatePageCheckTranslateOfPage'])->name('admin.checkTranslateOfPage.updatePageCheckTranslateOfPage');
    Route::post('/reCheckTranslateOfPage', [CheckTranslateOfPageController::class, 'reCheckTranslateOfPage'])->name('admin.checkTranslateOfPage.reCheckTranslateOfPage');
    /* ===== REDIRECT ===== */
    Route::prefix('redirect')->group(function(){
        Route::get('/list', [RedirectController::class, 'list'])->name('admin.redirect.list');
        Route::get('/create', [RedirectController::class, 'create'])->name('admin.redirect.create');
        Route::get('/delete', [RedirectController::class, 'delete'])->name('admin.redirect.delete');
    });
    /* wallpaper */
    Route::prefix('wallpaper')->group(function(){
        Route::get('/list', [WallpaperController::class, 'list'])->name('admin.wallpaper.list');
        Route::post('/loadOneRow', [WallpaperController::class, 'loadOneRow'])->name('admin.wallpaper.loadOneRow');
        Route::post('/loadModalUploadAndEdit', [WallpaperController::class, 'loadModalUploadAndEdit'])->name('admin.wallpaper.loadModalUploadAndEdit');
        Route::post('/uploadWallpaperWithSource', [WallpaperController::class, 'uploadWallpaperWithSource'])->name('admin.wallpaper.uploadWallpaperWithSource');
        Route::post('/changeWallpaperWithSource', [WallpaperController::class, 'changeWallpaperWithSource'])->name('admin.wallpaper.changeWallpaperWithSource');
        Route::post('/deleteWallpaperAndSource', [WallpaperController::class, 'deleteWallpaperAndSource'])->name('admin.wallpaper.deleteWallpaperAndSource');
        Route::post('/loadFormUploadSourceAndWallpaper', [WallpaperController::class, 'loadFormUploadSourceAndWallpaper'])->name('admin.wallpaper.loadFormUploadSourceAndWallpaper');
        Route::post('/searchWallpapers', [WallpaperController::class, 'searchWallpapers'])->name('admin.wallpaper.searchWallpapers');
    });
    /* free wallpaper */
    Route::prefix('freeWallpaper')->group(function(){
        Route::get('/list', [FreeWallpaperController::class, 'list'])->name('admin.freeWallpaper.list');
        Route::post('/loadOneRow', [FreeWallpaperController::class, 'loadOneRow'])->name('admin.freeWallpaper.loadOneRow');
        Route::post('/loadModalUploadAndEdit', [FreeWallpaperController::class, 'loadModalUploadAndEdit'])->name('admin.freeWallpaper.loadModalUploadAndEdit');
        Route::post('/uploadWallpaper', [FreeWallpaperController::class, 'uploadWallpaper'])->name('admin.freeWallpaper.uploadWallpaper');
        Route::post('/updateWallpaper', [FreeWallpaperController::class, 'updateWallpaper'])->name('admin.freeWallpaper.updateWallpaper');
        Route::post('/deleteWallpaper', [FreeWallpaperController::class, 'deleteWallpaper'])->name('admin.freeWallpaper.deleteWallpaper');
        Route::post('/addFormUpload', [FreeWallpaperController::class, 'addFormUpload'])->name('admin.freeWallpaper.addFormUpload');
    });
    /* seo free wallpaper */
    Route::prefix('seoFreeWallpaper')->group(function(){
        Route::get('/list', [SeoFreeWallpaperController::class, 'list'])->name('admin.seoFreeWallpaper.list');
        Route::get('/view', [SeoFreeWallpaperController::class, 'view'])->name('admin.seoFreeWallpaper.view');
        Route::post('/createAndUpdate', [SeoFreeWallpaperController::class, 'createAndUpdate'])->name('admin.seoFreeWallpaper.createAndUpdate');
    });
    /* prompt */
    Route::prefix('prompt')->group(function(){
        Route::get('/list', [PromptController::class, 'list'])->name('admin.prompt.list');
        Route::get('/view', [PromptController::class, 'view'])->name('admin.prompt.view');
        Route::post('/createAndUpdate', [PromptController::class, 'createAndUpdate'])->name('admin.prompt.createAndUpdate');
        Route::get('/loadColumnTable', [PromptController::class, 'loadColumnTable'])->name('admin.prompt.loadColumnTable');
        Route::post('/getPromptTextById', [PromptController::class, 'getPromptTextById'])->name('admin.prompt.getPromptTextById');
        Route::get('/delete', [PromptController::class, 'delete'])->name('admin.prompt.delete');
    });
    /* api ai */
    Route::prefix('apiai')->group(function(){
        Route::get('/list', [ApiAIController::class, 'list'])->name('admin.apiai.list');
        Route::get('/changeApiActive', [ApiAIController::class, 'changeApiActive'])->name('admin.apiai.changeApiActive');
    });
    /* product */
    Route::prefix('product')->group(function(){
        Route::get('/list', [ProductController::class, 'list'])->name('admin.product.list');
        Route::get('/listLanguageNotExists', [ProductController::class, 'listLanguageNotExists'])->name('admin.product.listLanguageNotExists');
        Route::get('/view', [ProductController::class, 'view'])->name('admin.product.view');
        Route::post('/createAndUpdate', [ProductController::class, 'createAndUpdate'])->name('admin.product.createAndUpdate');
        // Route::post('/create', [ProductController::class, 'create'])->name('admin.product.create');
        // Route::post('/update', [ProductController::class, 'update'])->name('admin.product.update');
        Route::post('/searchProductCopied', [ProductController::class, 'searchProductCopied'])->name('admin.product.searchProductCopied');
        Route::post('/updateProductCopied', [ProductController::class, 'updateProductCopied'])->name('admin.product.updateProductCopied');
        Route::get('/delete', [ProductController::class, 'delete'])->name('admin.product.delete');
    });
    /* product price */
    Route::prefix('productPrice')->group(function(){
        Route::post('/loadWallpaperByProductPrice', [ProductPriceController::class, 'loadWallpaperByProductPrice'])->name('admin.productPrice.loadWallpaperByProductPrice');
        Route::post('/addWallpaperToProductPrice', [ProductPriceController::class, 'addWallpaperToProductPrice'])->name('admin.productPrice.addWallpaperToProductPrice');
        Route::post('/deleteWallpaperToProductPrice', [ProductPriceController::class, 'deleteWallpaperToProductPrice'])->name('admin.productPrice.deleteWallpaperToProductPrice');
    });
    /* category */
    Route::prefix('category')->group(function(){
        Route::get('/list', [CategoryController::class, 'list'])->name('admin.category.list');
        Route::get('/listLanguageNotExists', [CategoryController::class, 'listLanguageNotExists'])->name('admin.category.listLanguageNotExists');
        Route::get('/view', [CategoryController::class, 'view'])->name('admin.category.view');
        Route::post('/createAndUpdate', [CategoryController::class, 'createAndUpdate'])->name('admin.category.createAndUpdate');
        Route::get('/delete', [CategoryController::class, 'delete'])->name('admin.category.delete');
        Route::get('/removeThumnailsOfCategory', [CategoryController::class, 'removeThumnailsOfCategory'])->name('admin.category.removeThumnailsOfCategory');
        Route::post('/loadFreeWallpaperOfCategory', [CategoryController::class, 'loadFreeWallpaperOfCategory'])->name('admin.category.loadFreeWallpaperOfCategory');
        Route::post('/seachFreeWallpaperOfCategory', [CategoryController::class, 'seachFreeWallpaperOfCategory'])->name('admin.category.seachFreeWallpaperOfCategory');
        Route::post('/chooseFreeWallpaperForCategory', [CategoryController::class, 'chooseFreeWallpaperForCategory'])->name('admin.category.chooseFreeWallpaperForCategory');
    });
    /* tag */
    Route::prefix('tag')->group(function(){
        Route::get('/list', [TagController::class, 'list'])->name('admin.tag.list');
        Route::get('/listLanguageNotExists', [TagController::class, 'listLanguageNotExists'])->name('admin.tag.listLanguageNotExists');
        Route::get('/view', [TagController::class, 'view'])->name('admin.tag.view');
        Route::post('/createAndUpdate', [TagController::class, 'createAndUpdate'])->name('admin.tag.createAndUpdate');
        Route::get('/delete', [TagController::class, 'delete'])->name('admin.tag.delete');
    });
    /* page */
    Route::prefix('page')->group(function(){
        Route::get('/list', [PageController::class, 'list'])->name('admin.page.list');
        Route::get('/view', [PageController::class, 'view'])->name('admin.page.view');
        Route::post('/createAndUpdate', [PageController::class, 'createAndUpdate'])->name('admin.page.createAndUpdate');
        Route::get('/delete', [PageController::class, 'delete'])->name('admin.page.delete');
    });
    /* ===== Category Blog ===== */
    Route::prefix('categoryBlog')->group(function(){
        Route::get('/', [CategoryBlogController::class, 'list'])->name('admin.categoryBlog.list');
        Route::post('/createAndUpdate', [CategoryBlogController::class, 'createAndUpdate'])->name('admin.categoryBlog.createAndUpdate');
        Route::get('/view', [CategoryBlogController::class, 'view'])->name('admin.categoryBlog.view');
        Route::get('/delete', [CategoryBlogController::class, 'delete'])->name('admin.categoryBlog.delete');
    });
    /* ===== Blog ===== */
    Route::prefix('blog')->group(function(){
        Route::get('/', [BlogController::class, 'list'])->name('admin.blog.list');
        Route::get('/view', [BlogController::class, 'view'])->name('admin.blog.view');
        Route::post('/createAndUpdate', [BlogController::class, 'createAndUpdate'])->name('admin.blog.createAndUpdate');
        /* Delete AJAX */
        Route::get('/delete', [BlogController::class, 'delete'])->name('admin.blog.delete');
        Route::get('/loadProduct', [BlogController::class, 'loadProduct'])->name('admin.blog.loadProduct');
        Route::get('/chooseProduct', [BlogController::class, 'chooseProduct'])->name('admin.blog.chooseProduct');
        Route::get('/loadThemeProductChoosed', [BlogController::class, 'loadThemeProductChoosed'])->name('admin.blog.loadThemeProductChoosed');
        Route::get('/removeOneProductChoosed', [BlogController::class, 'removeOneProductChoosed'])->name('admin.blog.removeOneProductChoosed');
        Route::get('/clearProductChoosed', [BlogController::class, 'clearProductChoosed'])->name('admin.blog.clearProductChoosed');
        Route::get('/getListProductChoose', [BlogController::class, 'getListProductChoose'])->name('admin.blog.getListProductChoose');
        Route::get('/callAIWritePerProduct', [BlogController::class, 'callAIWritePerProduct'])->name('admin.blog.callAIWritePerProduct');
    });
    /* ===== Order ===== */
    Route::prefix('order')->group(function(){
        Route::get('/', [OrderController::class, 'list'])->name('admin.order.list');
        Route::get('/view', [OrderController::class, 'view'])->name('admin.order.view');
        Route::get('/viewExport', [OrderController::class, 'viewExport'])->name('admin.order.viewExport');
        // Route::post('/create', [OrderController::class, 'create'])->name('admin.order.create');
        /* Delete AJAX */
        // Route::get('/delete', [BlogController::class, 'delete'])->name('admin.blog.delete');
    });
    /* setting */
    Route::prefix('setting')->group(function(){
        Route::get('/view', [SettingController::class, 'view'])->name('admin.setting.view');
        Route::get('/slider', [SettingController::class, 'slider'])->name('admin.setting.slider');
    });
    /* theme */
    Route::prefix('theme')->group(function(){
        Route::get('/view', [ThemeController::class, 'view'])->name('admin.theme.view');
        Route::post('/create', [ThemeController::class, 'create'])->name('admin.theme.create');
        Route::post('/update', [ThemeController::class, 'update'])->name('admin.theme.update');
        Route::get('/list', [ThemeController::class, 'list'])->name('admin.theme.list');
        Route::get('/{id}/setColor', [ThemeController::class, 'setColor'])->name('admin.theme.setColor');
    });
    /* slider */
    Route::prefix('slider')->group(function(){
        Route::post('/remove', [SliderController::class, 'remove'])->name('admin.slider.remove');
    });
    /* gallery */
    Route::prefix('gallery')->group(function(){
        Route::post('/remove', [GalleryController::class, 'remove'])->name('admin.gallery.remove');
    });
    /* gallery */
    Route::prefix('source')->group(function(){
        Route::post('/remove', [SourceController::class, 'remove'])->name('admin.source.remove');
    });
    /* image */
    Route::prefix('image')->group(function(){
        Route::get('/', [ImageController::class, 'list'])->name('admin.image.list');
        Route::post('/uploadImages', [ImageController::class, 'uploadImages'])->name('admin.image.uploadImages');
        Route::get('/loadImage', [ImageController::class, 'loadImage'])->name('admin.image.loadImage');
        Route::get('/loadModal', [ImageController::class, 'loadModal'])->name('admin.image.loadModal');
        Route::post('/changeImage', [ImageController::class, 'changeImage'])->name('admin.image.changeImage');
        Route::post('/removeImage', [ImageController::class, 'removeImage'])->name('admin.image.removeImage');
    });
    /* ===== CACHE ===== */
    Route::prefix('cache')->group(function(){
        Route::get('/clearCacheHtml', [CacheController::class, 'clear'])->name('admin.cache.clearCache');
    });
    /* ===== HELPER ===== */
    Route::prefix('helper')->group(function(){
        Route::get('/convertStrToSlug', [HelperController::class, 'convertStrToSlug'])->name('admin.helper.convertStrToSlug');
        Route::post('/deleteLanguage', [HelperController::class, 'deleteLanguage'])->name('admin.helper.deleteLanguage');
    });
    /* ===== TRANSLATE ===== */
    Route::prefix('translate')->group(function(){
        Route::get('/list', [TranslateController::class, 'list'])->name('admin.translate.list');
        Route::get('/delete', [TranslateController::class, 'delete'])->name('admin.translate.delete');
        Route::get('/redirectEdit', [TranslateController::class, 'redirectEdit'])->name('admin.translate.redirectEdit');
        Route::post('/reRequestTranslate', [TranslateController::class, 'reRequestTranslate'])->name('admin.translate.reRequestTranslate');
        Route::post('/createJobTranslateContentAjax', [TranslateController::class, 'createJobTranslateContentAjax'])->name('admin.translate.createJobTranslateContentAjax');
        Route::post('/createMultiJobTranslateContent', [TranslateController::class, 'createMultiJobTranslateContent'])->name('admin.translate.createMultiJobTranslateContent');
        Route::post('/createJobTranslateAndCreatePageAjax', [TranslateController::class, 'createJobTranslateAndCreatePageAjax'])->name('admin.translate.createJobTranslateAndCreatePageAjax');
        Route::post('/autoTranslateMissing', [TranslateController::class, 'autoTranslateMissing'])->name('admin.translate.autoTranslateMissing');
        /* job auto viết content đặt tạm ở đây */
        Route::post('/createJobWriteContent', [TranslateController::class, 'createJobWriteContent'])->name('admin.translate.createJobWriteContent');
    });
});
Route::middleware(['check.domain'])->group(function () {
    /* my account */
    Route::middleware('auth')->group(function (){
        Route::prefix('tai-khoan')->group(function(){
            Route::get('/tai-xuong-cua-toi', [AccountController::class, 'orders'])->name('main.account.orders');
        });
    });
    /* check onpage website */
    Route::get('/buildListPostByUrl', [CheckOnpageController::class, 'buildListPostByUrl'])->name('main.checkOnpage.buildListPostByUrl');
    Route::get('/crawler', [CheckOnpageController::class, 'crawler'])->name('main.checkOnpage.crawler');
    /* login với facebook */
    Route::get('/auth/facebook/redirect', [ProviderController::class, 'facebookRedirect'])->name('main.facebook.redirect');
    Route::get('/auth/facebook/callback', [ProviderController::class, 'facebookCallback'])->name('main.facebook.callback');
    /* tải hình ảnh khi hoàn tất thanh toán */
    Route::get('/downloadSource', [GoogledriveController::class, 'downloadSource'])->name('main.downloadSource');
    /* nháp */
    Route::get('/test123', [HomeController::class, 'test'])->name('main.test');
    /* lỗi */
    Route::get('/error', [\App\Http\Controllers\ErrorController::class, 'handle'])->name('error.handle');
    Route::get('/addToCart', [CartController::class, 'addToCart'])->name('main.addToCart');
    Route::get('/updateCart', [CartController::class, 'updateCart'])->name('main.updateCart');
    Route::get('/removeProductCart', [CartController::class, 'removeProductCart'])->name('main.removeProductCart');
    Route::get('/viewSortCart', [CartController::class, 'viewSortCart'])->name('main.viewSortCart');
    Route::get('/loadTotalCart', [CartController::class, 'loadTotalCart'])->name('main.loadTotalCart');
    Route::get('/paymentNow', [CheckoutController::class, 'paymentNow'])->name('main.paymentNow');
    Route::post('/paymentCart', [CheckoutController::class, 'paymentCart'])->name('main.paymentCart');
    Route::get('/handlePaymentMomo', [ConfirmController::class, 'handlePaymentMomo'])->name('main.handlePaymentMomo');
    Route::get('/handlePaymentZalopay', [ConfirmController::class, 'handlePaymentZalopay'])->name('main.handlePaymentZalopay');
    Route::get('/handlePaymentVNPay', [ConfirmController::class, 'handlePaymentVNPay'])->name('main.handlePaymentVNPay');
    Route::get('/handlePaymentPaypal', [ConfirmController::class, 'handlePaymentPaypal'])->name('main.handlePaymentPaypal');
    Route::get('/handlePaymentTwoCheckout', [ConfirmController::class, 'handlePaymentTwoCheckout'])->name('main.handlePaymentTwoCheckout');
    /* order */
    Route::post('/order', [OrderPublic::class, 'create'])->name('main.order');
    Route::get('/viewConfirm', [OrderPublic::class, 'viewConfirm'])->name('main.viewConfirm');
    /* category blog */
    Route::get('/showSortBoxInCategoryTag', [CategoryBlogPublic::class, 'showSortBoxInCategoryTag'])->name('main.showSortBoxInCategoryTag');
    /* AJAX */
    Route::get('/buildTocContentMain', [AjaxController::class, 'buildTocContentMain'])->name('main.buildTocContentMain');
    Route::get('/loadLoading', [AjaxController::class, 'loadLoading'])->name('ajax.loadLoading');
    Route::get('/loadDistrictByIdProvince', [AjaxController::class, 'loadDistrictByIdProvince'])->name('ajax.loadDistrictByIdProvince');
    Route::get('/registryEmail', [AjaxController::class, 'registryEmail'])->name('ajax.registryEmail');
    // Route::get('/registrySeller', [AjaxController::class, 'registrySeller'])->name('ajax.registrySeller');
    Route::get('/setMessageModal', [AjaxController::class, 'setMessageModal'])->name('ajax.setMessageModal');
    Route::get('/checkLoginAndSetShow', [AjaxController::class, 'checkLoginAndSetShow'])->name('ajax.checkLoginAndSetShow');
    Route::get('/loadImageFromGoogleCloud', [AjaxController::class, 'loadImageFromGoogleCloud'])->name('ajax.loadImageFromGoogleCloud');
    Route::get('/loadImageSource', [AjaxController::class, 'loadImageSource'])->name('ajax.loadImageSource');
    Route::get('/downloadImageSource', [AjaxController::class, 'downloadImageSource'])->name('ajax.downloadImageSource');
    Route::get('/setViewBy', [AjaxController::class, 'setViewBy'])->name('ajax.setViewBy');
    Route::get('/showSortBoxFreeWallpaper', [AjaxController::class, 'showSortBoxFreeWallpaper'])->name('ajax.showSortBoxFreeWallpaper');
    Route::get('/showSortBoxWallpaper', [AjaxController::class, 'showSortBoxWallpaper'])->name('ajax.showSortBoxWallpaper');
    Route::get('/showSortBoxFreeWallpaperInTag', [AjaxController::class, 'showSortBoxFreeWallpaperInTag'])->name('ajax.showSortBoxFreeWallpaperInTag');
    Route::get('/setSortBy', [AjaxController::class, 'setSortBy'])->name('ajax.setSortBy');
    Route::get('/downloadImgFreeWallpaper', [AjaxController::class, 'downloadImgFreeWallpaper'])->name('ajax.downloadImgFreeWallpaper');
    Route::get('/setFeelingFreeWallpaper', [AjaxController::class, 'setFeelingFreeWallpaper'])->name('ajax.setFeelingFreeWallpaper');
    Route::get('/loadOneFreeWallpaper', [AjaxController::class, 'loadOneFreeWallpaper'])->name('ajax.loadOneFreeWallpaper');
    Route::get('/loadMoreWallpaper', [CategoryMoneyPublic::class, 'loadMoreWallpaper'])->name('main.category.loadMoreWallpaper');
    Route::get('/loadmoreFreeWallpapers', [CategoryPublic::class, 'loadmoreFreeWallpapers'])->name('main.category.loadmoreFreeWallpapers');
    Route::get('/loadInfoCategory', [CategoryPublic::class, 'loadInfoCategory'])->name('main.category.loadInfoCategory');
    Route::get('/toogleHeartFeelingFreeWallpaper', [AjaxController::class, 'toogleHeartFeelingFreeWallpaper'])->name('ajax.toogleHeartFeelingFreeWallpaper');
    Route::get('/loadLinkDownloadGuide', [AjaxController::class, 'loadLinkDownloadGuide'])->name('ajax.loadLinkDownloadGuide');
    Route::get('/loadProductPrice', [AjaxController::class, 'loadProductPrice'])->name('ajax.loadProductPrice');
    Route::get('/calculaterRightPrice', [AjaxController::class, 'calculaterRightPrice'])->name('ajax.calculaterRightPrice');
    /* Search */
    Route::get('/searchAjax', [SearchController::class, 'searchAjax'])->name('search.searchAjax');
    /* setting */
    Route::get('/settingCollapsedMenu', [SettingPublic::class, 'settingCollapsedMenu'])->name('main.settingCollapsedMenu');
    Route::get('/getStatusCollapse', [SettingPublic::class, 'getStatusCollapse'])->name('main.getStatusCollapse');
    Route::get('/settingGPSVisitor', [SettingPublic::class, 'settingGPSVisitor'])->name('main.settingGPSVisitor');
    Route::get('/settingIpVisitor', [SettingPublic::class, 'settingIpVisitor'])->name('main.settingIpVisitor');
    Route::get('/settingTimezoneVisitor', [SettingPublic::class, 'settingTimezoneVisitor'])->name('main.settingTimezoneVisitor');
    /* trang chủ */
    $validLanguages = ['']; // Ngôn ngữ mặc định
    foreach (config('language') as $key => $value) {
        $validLanguages[] = $key;
    }
    Route::get('/{language?}', [HomeController::class, 'home'])
        ->where('language', implode('|', $validLanguages))
        ->name('main.home');
    /* trang giỏ hàng */
    $validCarts     = config('main_'.env('APP_NAME').'.url_cart_page');
    if(!empty($validCarts)){
        Route::get('/{slugCart}', [CartController::class, 'index'])
                ->where('slugCart', implode('|', $validCarts))
                ->name('main.cart');
    }
    
    /* trang xác nhận */
    $validSlugs = config('main_'.env('APP_NAME').'.url_confirm_page');
    if(!empty($validSlugs)){
        Route::get('/{slug}', [ConfirmController::class, 'confirm'])
                ->where('slug', implode('|', $validSlugs))
                ->name('main.confirm');
    }
    /* ROUTING */
    Route::middleware(['checkRedirect'])->group(function () {
        Route::get("/{slug}/{slug2?}/{slug3?}/{slug4?}/{slug5?}/{slug6?}/{slug7?}/{slug8?}/{slug9?}/{slug10?}", [RoutingController::class, 'routing'])->name('routing');
    });
});