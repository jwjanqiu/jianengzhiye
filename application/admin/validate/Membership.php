<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/4/11
 * Time: 10:37
 */
namespace app\admin\validate;
use think\Validate;

class Membership extends Validate
{
    protected $rule = [
        ['name','require|max:60','请输入姓名|请不要超过60个字符'],
        ['mobile','require|number|length:11','请输入手机号|请输入数字|手机号码格式不对'],
        ['sex','require','请选择性别'],
        ['birthday','require','请选择生日']
    ];
}