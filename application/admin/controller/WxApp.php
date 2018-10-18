<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/18
 * Time: 16:06
 */

namespace app\admin\controller;

use think\Controller;

class WxApp extends Controller
{
    public function bannerSettings()
    {
        $this->assign('list','');
        return $this->fetch();
    }
}
