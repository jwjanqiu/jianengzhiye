<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/5/8
 * Time: 15:36
 */
namespace app\admin\validate;
use think\Validate;

class Promotion extends Validate
{
    protected $rule = [
        ['promotion_name','require','请输入促销类型名']
    ];
}