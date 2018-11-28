<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/11
 * Time: 16:48
 */

namespace app\api\controller;

use app\admin\model\Banner;
use app\admin\model\BrandModel;
use app\admin\model\Cate;
use app\admin\model\ChannelModel;
use think\Controller;

class Index extends Controller
{
    /**
     * 返回类别
     * @return Cate[]|false
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function getCategory()
    {
        $cate = Cate::all(function ($query){
            $query->order('id','asc');
        });
        return $this->returnMsg(1,'请求成功',$cate);
    }

    /**
     * 获取首页数据
     * @return array
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function getIndexData()
    {
        $banner = Banner::all();
        $channel = ChannelModel::getChannel();
        $brand = BrandModel::apiGetBrand();
        $data = array(
            'banner' => $banner,
            'channel' => $channel,
            'brand' => $brand
        );
        return $this->returnMsg(1,'请求成功',$data);
    }

}
