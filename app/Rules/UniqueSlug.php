<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Seo;
use App\Http\Controllers\Admin\HelperController;

class UniqueSlug implements Rule {
    protected $request;
    protected $titleMatch;
    protected $languageMatch;

    public function __construct($request) {
        $this->request = $request;
    }

    public function passes($attribute, $value) {
        $language = $this->request->get('language');
        $type = !empty($this->request->get('seo_id')) && $this->request->get('type') == 'edit' ? 'update' : 'insert';
        $idSeo = $this->request->get('seo_id');
        $parent = $this->request->get('parent') ?? 0;
        $slugFull = Seo::buildFullUrl($this->request->get('slug'), $parent);

        $flagSlugFullUnique = Seo::checkSlugFullUnique($slugFull, $type, $idSeo);
        if ($flagSlugFullUnique) {
            $idSeoMatch = Seo::select('*')
                ->where('slug_full', $slugFull)
                ->first();
            $infoPageMatch = HelperController::getFullInfoPageByIdSeo($idSeoMatch->id);
            $this->languageMatch  = '';
            $this->titleMatch     = '';
            foreach ($infoPageMatch->seos as $seo) {
                if (!empty($seo->infoSeo->language) && $seo->infoSeo->language == $language) {
                    $this->languageMatch = $seo->infoSeo->language;
                    $this->titleMatch = $infoPageMatch->seo->title;
                    break;
                }
            }
            return false;
        }
        return true;
    }

    public function message() {
        return 'Dường dẫn tĩnh đã trùng với trang '.$this->titleMatch.' (ngôn ngữ '.$this->languageMatch.') trên hệ thống!';
    }
}
