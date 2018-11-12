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
        $host = config('img_url');
        $path = str_replace("\\", "/", $host);
        $list = self::order('sort_order')->paginate(15, false, [
            'query' => request()->param()
        ]);
        foreach ($list as $key => $value) {
            if ($value['image']) {
                $image = json_decode($value->image, true);
                $list[$key]['image'] = $path . $image[0];
            } else {
                $list[$key]['image'] = '';
            }
        }
        return $list;
    }
}
