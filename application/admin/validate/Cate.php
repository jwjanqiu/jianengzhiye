<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/1/4
 * Time: 16:08
 */
namespace app\admin\validate;
use think\Validate;

class Cate extends Validate
{
    protected $rule = [
        ['cate_name','require|max:60','请输入标题|不能超过60个字符'],
    ];
}