<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/1/31
 * Time: 20:43
 */
namespace app\admin\model;
use think\Model;
use think\Session;

class Consume extends Model
{
    protected $table = "jnzy_consume";
    protected $insert = ['isconfirm' => 0,'isread' => 0];
    protected function getIsConfirmAttr($value)
    {
        $isConfirm = [
            0 => "待确认",
            1 => "已接单",
            -1 => "已取消",
            2 => "已完成"
        ];
        return $isConfirm[$value];
    }
    //新订单通知
    public static function getNewOrderNotify()
    {
        $count = Consume::where('isread',0)->count();
        return $count;
    }
    
}