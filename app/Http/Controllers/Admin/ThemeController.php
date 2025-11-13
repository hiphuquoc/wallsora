<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Jobs\BuildScss;
use App\Models\Theme;
use App\Models\ThemeColor;

class ThemeController extends Controller {

    public function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $item               = Theme::select('*')
                                ->where('id', $id)
                                ->with('colors')
                                ->first();
        $item->colors       = self::convertCollectionColors($item->colors);
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.theme.view', compact('item', 'type', 'message'));
    }
    
    public function list(Request $request){
        $themes     = Theme::select('*')
                        ->with('colors')
                        ->get();
        for($i=0;$i<$themes->count();++$i){
            $themes[$i]->colors = self::convertCollectionColors($themes[$i]->colors);
        }
        return view('admin.theme.list', compact('themes'));
    }

    public function create(Request $request){
        try {
            DB::beginTransaction();
            /* insert theme_info */
            $idTheme = Theme::insertItem([
                'name'      => $request->get('name'),
                'type'      => $request->get('type'),
                'status'    => 0
            ]);
            /* insert theme_color */
            foreach($request->colors as $key => $value){
                ThemeColor::insertItem([
                    'theme_info_id' => $idTheme,
                    'name'          => $key,
                    'value'         => $value
                ]);
            }
            /* áp dụng màu */
            self::setColor($idTheme);
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã thêm mới Theme!'
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
        return redirect()->route('admin.theme.view', ['id' => $idTheme]);
    }

    public function update(Request $request){
        try {
            DB::beginTransaction();
            $idTheme            = $request->get('theme_info_id');
            /* update theme_info */
            Theme::updateItem($idTheme, [
                'name'  => $request->get('name'),
                'type'  => $request->get('type')
            ]);
            /* update theme_color */
            ThemeColor::select('*')
                ->where('theme_info_id', $idTheme)
                ->delete();
            foreach($request->colors as $key => $value){
                ThemeColor::insertItem([
                    'theme_info_id' => $idTheme,
                    'name'          => $key,
                    'value'         => $value
                ]);
            }
            /* áp dụng màu */
            self::setColor($idTheme);
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Theme!'
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
        return redirect()->route('admin.theme.view', ['id' => $idTheme]);
    }

    public static function setColor($id = null){
        $content        = null;
        /* lấy đường dẫn file vite */
        $fileVite       = null;
        $fileManifest   = './build/manifest.json';
        $contentManifest = file_get_contents($fileManifest);
        $key            = 'resources/sources/main/style.scss';
        $pattern        = '/"' . preg_quote($key, '/') . '":\s*{\s*"file":\s*"([^"]+)/';
        if (preg_match($pattern, $contentManifest, $matches)) $fileVite = $matches[1];
        if(!empty($fileVite)){
            $tmp        = pathinfo($fileVite);
            $fileViteNew = $tmp['dirname'].'/style-'.\App\Helpers\Charactor::randomString(10).'.css';
            $contentManifest = str_replace($fileVite, $fileViteNew, $contentManifest);
            file_put_contents($fileManifest, $contentManifest);
            $fileVite   = './build/'.$fileVite;
            $fileViteNew = './build/'.$fileViteNew;
            if(file_exists(public_path($fileVite))) {
                /* kiểm tra file map tồn tại không */
                $fileMap        = './build/assets/style-main.css';
                if(file_exists(public_path($fileMap))){
                    /* file gốc build ở development => lưu lại để làm map replace */
                    $content    = file_get_contents($fileMap);
                }else {
                    /* lần đầu chưa có file map => lấy file được buidl từ hệ thống dùng => copy ra lưu lại file map */
                    $content    = file_get_contents($fileVite);
                    /* copy ra một file làm file map */
                    if (copy($fileVite, $fileMap)) chmod($fileMap, 0777);
                }
                /* lấy ra tất cả các màu cần thay thế */
                $fileColor      = '../resources/sources/main/color.scss';
                $contentColor   = file_get_contents($fileColor);
                $matches        = [];
                preg_match_all('/(\$[a-zA-Z0-9_]+)\s*:\s*#([a-zA-Z0-9]{6})/', $contentColor, $matches);
                $arrayColorSource  = [];
                foreach ($matches[1] as $index => $varName) {
                    $arrayColorSource[substr($varName, 1)] = '#' . $matches[2][$index];
                }
                /* tạo map thay thế */
                $arrayMap               = [];
                foreach($arrayColorSource as $key => $value){
                    /* lấy thông tin theme đang được active */
                    if(!empty($id)){
                        $infoTheme      = Theme::select('*')
                            ->where('id', $id)
                            ->first();
                        $typeTheme      = $infoTheme->type;
                        /* chuyển tất cả các theme cùng loại khác sang non-active */
                        $themes         = Theme::select('*')
                                        ->where('type', $typeTheme)
                                        ->get();
                        foreach($themes as $theme){
                            Theme::updateItem($theme->id, ['status' => 0]);
                        }
                        /* active theme được chỉ định */
                        Theme::updateItem($id, ['status' => 1]);
                    }else {
                        $infoTheme      = Theme::select('*')
                            ->where('type', 'white')
                            ->where('status', 1)
                            ->with('colors')
                            ->first();
                    }
                    foreach($infoTheme->colors as $color){
                        if($key==$color->name){
                            $arrayMap[$value] = $color->value;
                            break;
                        }
                    }
                }
                /* tiến hành thay thế => thay cùng lúc để không bị lỗi trùng màu gốc và màu thay */
                $content = strtr($content, $arrayMap);
                /* xóa file vite được build cũ */
                if(file_exists(public_path($fileVite))) @unlink(public_path($fileVite));
                /* ghi vào file */
                file_put_contents($fileViteNew, $content);
                chmod($fileViteNew, 0777);
            }
        }
        return redirect()->route('admin.theme.list');
    }

    private static function convertCollectionColors($collectionColor = null){
        $output     = new \Illuminate\Database\Eloquent\Collection;
        if(!empty($collectionColor)){
            foreach($collectionColor as $color){
                $output->{$color->name} = $color->value;
            }
        }
        return $output;
    }
}
