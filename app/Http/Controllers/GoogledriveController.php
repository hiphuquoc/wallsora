<?php

namespace App\Http\Controllers;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class GoogledriveController extends Controller {

    public static function downloadZipInFolder($folderName){
        $zip        = null;
        $tmp        = Gdrive::all($folderName, true);
        if(!empty($tmp)&&$tmp->isNotEmpty()){
            foreach($tmp as $t){
                if($t['extraMetadata']['extension']=='zip') {
                    $zip = $t['path'];
                    break;
                }
            }
            /* download */
            if(!empty($zip)) return Storage::disk('google')->download($zip);
        }
    }

    public static function downloadSource(Request $request){
        $file       = $request->get('file') ?? null;
        if(!empty($file)) return Storage::disk('google')->download($file);
    }

}
