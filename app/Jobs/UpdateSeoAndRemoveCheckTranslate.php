<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\CheckTranslate;

class UpdateSeoAndRemoveCheckTranslate implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $idSeo;
    private $idCheck;
    public  $tries = 5; // Số lần thử lại

    public function __construct($idSeo, array $data, $idCheck){
        $this->idSeo        = $idSeo;
        $this->idCheck      = $idCheck;
        $this->data         = $data;
    }

    public function handle(){
        try {
            $flag = Seo::updateItem($this->idSeo, $this->data);
            if($flag==true){
                /* xóa thông tin check */
                CheckTranslate::select('*')
                    ->where('id', $this->idCheck)
                    ->delete();
            }else {
                /* cập nhật trạng thái fail (status = 2) */
                CheckTranslate::updateItem($this->idCheck, ['status' => 2]);
            }
        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }
}
