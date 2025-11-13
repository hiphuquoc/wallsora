<?php

namespace App\Helpers;

class Setting {

    public static function settingView($name, $listItem, $default, $total){
        
        return view('admin.template.settingView', compact('name', 'listItem', 'default', 'total'))->render();
    }

}