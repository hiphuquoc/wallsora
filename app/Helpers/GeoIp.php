<?php

namespace App\Helpers;

class GeoIp {

    public static function getLocation(){
        $reader = app('geoip'); // Lấy instance từ container
        $ip = request()->ip();

        // Kiểm tra nếu đang chạy trên localhost, gán IP public để test
        if ($ip === '127.0.0.1') {
            $ip = '8.8.8.8'; // Dùng IP để test
        }

        try {
            $record = $reader->country($ip);

            return [
                'ip'        => $ip,
                'country'   => $record->country->name,
                'iso_code'  => $record->country->isoCode,
                'continent' => $record->continent->name,
            ];
        } catch (AddressNotFoundException $e) {
            return ['error' => 'IP not found in database'];
        } catch (\Exception $e) {
            return ['error' => 'Unable to determine location'];
        }
    }

}