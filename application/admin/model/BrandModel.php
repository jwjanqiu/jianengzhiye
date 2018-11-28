<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/11/12
 * Time: 11:18
 */

namespace app\admin\model;

use think\Model;

class BrandModel extends Model
{
    protected $table = 'jnzy_brand';

    /**
     * 获取品牌
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public static function getBrand()
    {
        $list = self::order('sort_order')->paginate(15, false, [
            'query' => request()->param()
        ]);
        return $list;
    }

    /**
     * 添加数据
     * @param $data
     * @return BrandModel
     * @author Qiu
     */
    public static function insertData($data)
    {
        $result = self::create($data);
        return $result;
    }

    /**
     * api获取品牌类目
     * @return BrandModel[]|false
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public static function apiGetBrand(){
        $list = self::all(function ($query) {
            $query->order('sort_order');
        });
        return $list;
    }
}
