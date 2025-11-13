<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendProductMail extends Mailable {
    use Queueable, SerializesModels;

    public $order;
    public $language;

    /**
     * Create a new message instance.
     *
     * @param array $order
     * @return void
     */
    public function __construct($order, $language) {
        $this->order    = $order;
        $this->language = $language;
    }

    public function build(){
        $email  = $this->subject(config('data_language_1.'.$this->language.'.order_confirmation').' - '.ucfirst(env('APP_NAME')))
                    ->view('wallpaper.mail.sendProduct')
                    ->with([
                        'order' => $this->order,
                        'language'  => $this->language,
                    ]);

        /* đính kèm tệp quá nhiều không sử dụng được */
        // foreach ($this->order->wallpapers as $wallpaper) {
        //     if (!empty($wallpaper->infoWallpaper->file_cloud_source)) {
        //         $path = \App\Helpers\Image::getUrlImageCloud($wallpaper->infoWallpaper->file_cloud_source);

        //         // Đảm bảo đường dẫn là chuỗi và hợp lệ
        //         if (is_string($path)) {
        //             $email->attach($path, [
        //                 'as' => basename($path), // Đặt tên file
        //                 'mime' => 'image/png',   // Đặt loại MIME thích hợp
        //             ]);
        //         }
        //     }
        // }

        return $email;
    }
}