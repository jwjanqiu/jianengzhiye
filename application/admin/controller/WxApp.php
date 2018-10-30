<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/18
 * Time: 16:06
 */

namespace app\admin\controller;

use app\admin\model\Banner;
use app\admin\model\Goods;
use think\Controller;

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

    public function banner_add()
    {
        $info = array(
            'id' => '',
            'name' => '',
            'link' => ''
        );
        $this->assign('info',$info);
        $this->assign('img_list','');
        return $this->fetch();
    }

    public function getProduct()
    {
        $name = input('search_name');
        $condition['goods_name'] = array('like','%'.$name.'%');
        $list = Goods::all(function ($query) use($condition){
            $query->field('id,goods_name')->where($condition)->order('id','desc');
        });
        return json_encode($list);
    }
    public function banner_update()
    {
        $info = input();
        $host = "../../uploads/";
        $image_url = $host . $info['image'];
        $path = str_replace("\\","/",$image_url);
        $link = Goods::field('id')->where('goods_name',$info['link'])->find();
        $data = array(
            'name' => $info['name'],
            'image_url' => str_replace(',','',$path),
            'link' => $link['id']
        );
        $result = Banner::create($data);
        if ($result){
            $this->success('修改成功','admin/WxApp/bannerSettings');
        }else{
            $this->success($result->getError());
        }
    }
}
