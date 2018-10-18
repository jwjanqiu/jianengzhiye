<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/5/7
 * Time: 14:00
 */
namespace app\admin\model;
use think\Model;

class Promotion extends Model
{
    protected $table = 'jnzy_promotion';

    /**
     * 查询所有促销类型
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function queryAllPromotion()
    {
        $list = Promotion::order('id','desc')->paginate(15,false,[
            'query' => request()->param()
        ]);
        return $list;
    }

    /**
     * 查询某个促销类型
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function queryPromotion($id)
    {
        $info = Promotion::get($id);
        return $info;
    }

    /**
     * 添加新的促销类型
     * @param $data
     * @return array|string
     * @throws \think\exception\DbException
     */
    public static function addPromotion($data)
    {
        $check = Promotion::get(['promotion_name' => $data['promotion_name']]);
        if (false == empty($check))
        {
            return '已存在该促销类型';
        }
        $result = Promotion::create($data);
        if ($result)
        {
            return '添加成功';
        }else{
            return Promotion::getError();
        }
    }

    /**
     * 修改促销类型
     * @param $data
     * @return array|bool|string
     * @throws \think\exception\DbException
     */
    public static function updatePromotion($data)
    {
        $check = Promotion::get(['promotion_name' => $data['promotion_name']]);
        if (false == empty($check))
        {
            return '已存在该促销类型';
        }
        $result = Promotion::update($data);
        if ($result)
        {
            return '更新成功';
        }else{
            return Promotion::getError();
        }
    }

    /**
     * 删除某个促销类型
     * @param $id
     * @return array|bool|string
     */
    public static function deletePromotion($id)
    {
        if (Promotion::destroy($id))
        {
            return true;
        }else{
            return Promotion::getError();
        }
    }

    public static function searchPromotion($promotion_name)
    {
        $list = Promotion::where('promotion_name','like','%' . $promotion_name . '%')->paginate(15,false,[
            'query' => request()->param()
        ]);
        return $list;
    }
}