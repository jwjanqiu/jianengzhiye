<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/1/23
 * Time: 14:40
 */
namespace app\admin\validate;
use think\Validate;

class Product extends Validate
{
    protected $rule = [
        ['goods_name','require|max:60','请输入产品名'],
        ['cate_id','require','请选择分类'],
        ['retail_price','require','请输入零售价'],
        ['purchasing_cost','require','请输入进货价'],
        ['stock','require','请输入库存数'],
        ['bar_code','require','请扫商品码'],
    ];
}

