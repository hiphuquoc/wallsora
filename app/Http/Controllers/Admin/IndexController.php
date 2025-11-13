<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Google\Client as Google_Client;
use App\Models\Seo;

class IndexController extends Controller {

    public static function indexUrl($idSeo, $type = 'URL_UPDATED'){
        $statusCode = 0;
        if(!empty($idSeo)){
            /* lấy thông tin seo */
            $infoSeo    = Seo::find($idSeo);
            $url        = env('APP_URL').'/'.$infoSeo->slug_full;
            /* gửi index */
            $client = new Google_Client();
            // service_account_file.json is the private key that you created for your service account.
            $client->setAuthConfig('../credentials.json');
            $client->addScope('https://www.googleapis.com/auth/indexing');
            // Get a Guzzle HTTP Client
            $httpClient = $client->authorize();
            $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
            // Define contents here. The structure of the content is described in the next step.
            $content = '{
                "url": "'.$url.'",
                "type": "'.$type.'"
            }';
            $response   = $httpClient->post($endpoint, ['body' => $content]);
            $statusCode = $response->getStatusCode();
        }        
        return $statusCode;
    }

}
