<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/1/4
 * Time: 14:52
 */
namespace app\admin\model;
use think\Model;

class Cate extends Model
{
    protected $table = 'jnzy_cate';

    /**
     * 返回分类列表
     * @return false|static[]
     * @throws \think\exception\DbException
     */
    public static function cateIndex()
    {
        $host = "http://jianengzhiye.qiuyunxin.com/uploads/";
        $cateList = Cate::all(function($query){
            $query->order('id','asc');
        });
        foreach ($cateList as $key => $value)
        {
            $image = json_decode($value->image);
            if (false == empty($image))
            {
                $value->image = $host . $image[0];
            }
        }
        return $cateList;
    }
}