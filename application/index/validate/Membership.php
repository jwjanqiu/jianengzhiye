<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/5/2
 * Time: 18:04
 */
namespace app\index\validate;
use think\Validate;

class Membership extends Validate
{
    protected $rule = [
        ['name','require|max:60','请输入姓名|请不要超过60个字符'],
        ['mobile','require|number|length:11','请输入手机号|请输入数字|手机号码格式不对'],
        ['password','require|confirm','请输入密码|两次密码输入不一致'],
    ];
}