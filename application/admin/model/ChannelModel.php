<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/11/5
 * Time: 17:39
 */

namespace app\admin\model;

use think\Model;

class ChannelModel extends Model
{
    protected $table = 'jnzy_channel';

    /**
     * 获取分类
     * @return ChannelModel[]|false
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public static function getChannel()
    {
        $channel = self::all(function ($query) {
            $query->order('sort_order');
        });
        return $channel;
    }

    public static function searchChannel($name)
    {
        $condition['name'] = array('like', '%' . $name . '%');
        $list = self::where($condition)->order('sort_order')->paginate(15,false,[
            'query' => request()->param()
        ]);
        return $list;
    }
}
