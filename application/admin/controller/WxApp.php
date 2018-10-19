<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/18
 * Time: 16:06
 */

namespace app\admin\controller;

use app\admin\model\Banner;
use think\Controller;
use think\Model;

class WxApp extends Controller
{
    /**
     * banner列表页面
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function bannerSettings()
    {
        $list = Banner::getBanner();
        $this->assign('list', $list);
        $this->assign('name', '');
        return $this->fetch();
    }

    /**
     * 搜索Banner
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function banner_search()
    {
        $name = input('search_banner');
        $list = Banner::searchBanner($name);
        $this->assign('name', $name);
        $this->assign('list', $list);
        return $this->fetch('../application/admin/view/wx_app/bannerSettings.html');
    }
}
