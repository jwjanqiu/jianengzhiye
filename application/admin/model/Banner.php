<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/19
 * Time: 15:58
 */

namespace app\admin\model;

use think\Model;

class Banner extends Model
{
    protected $table = 'jnzy_banner';

    /**
     * 获取banner
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public static function getBanner()
    {
        $banner = self::order('id', 'desc')->paginate(15, false, [
            'query' => request()->param()
        ]);
        return $banner;
    }

    /**
     * 搜索banner
     * @param $name
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public static function searchBanner($name)
    {
        $condition['name'] = array(
            'like', '%' . $name . '%'
        );
        $banner = self::where($condition)->order('id', 'desc')->paginate(15, false, [
            'query' => request()->param()
        ]);
        return $banner;
    }
}
