<?php

namespace addons\geetest;

use app\admin\lib\Plugin;

class GeetestPlugin extends Plugin
{
    public $info = [
        'name' => 'Geetest',  // Demo插件英文名，改成你的插件英文就行了
        'title' => 'Geetest验证码',
        'description' => 'Geetest验证码',
        'status' => 1,
        'author' => '<a href="http://www.miniduo.cn">迷你哆云</a>',  // 开发者
        'version' => '1.0',
        'module' => 'addons',
    ];

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
}
