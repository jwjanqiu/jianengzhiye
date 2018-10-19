<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/11
 * Time: 16:48
 */

namespace app\api\controller;

use app\admin\model\Cate;
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
}
