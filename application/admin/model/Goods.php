<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/1/5
 * Time: 14:20
 */
namespace app\admin\model;
use think\Model;
use app\admin\model\Cate;

class Goods extends Model
{
    protected $table = "jnzy_goods_data";
    protected $insert = ['purchase' => 0];
    //销售最佳
    public static function getMaxPurchase()
    {
        $info = Goods::get(function ($query){
            $query->order('purchase','desc')->order('update_time','desc');
        });
        return $info;
    }

    /**
     * 根据货物名称搜索货物
     * @param $goods_name
     * @param string $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getGoods($goods_name,$order = 'purchase')
    {
        $host = config('img_url');
//        $result = Goods::all(function ($query) use($goods_name,$order){
//            $query->where('goods_name','like','%' . $goods_name . '%')->order($order,'desc')->paginate(15,false,[
//                'query' => request()->param()
//            ]);
//        });
        $result = Goods::where('goods_name','like','%' . $goods_name . '%')->order($order,'desc')->paginate(6,false,[
            'query' => request()->param()
        ]);
        if (false == empty($result))
        {
            foreach ($result as $key => $value)
            {
                $image = json_decode($value->picture);
                $cate = Cate::get(['id' => $value->cate_id]);
                if (false == empty($image))
                {
                    $value->picture = $host . $image[0];
                    $value->cate_id = $cate->cate_name;
                }
            }
        }
        return $result;
    }

    /**
     * 带条件搜索商品
     * @param $goods_name
     * @param $condition
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getGoodsWithCondition($goods_name,$condition)
    {
        $host = config('img_url');
        $list = Goods::where('goods_name','like','%' . $goods_name . '%')->where('promotion',$condition)->order('purchase','desc')->paginate(15,false,[
            'query' => request()->param()
        ]);
        if (false == empty($list))
        {
            foreach ($list as $key => $value)
            {
                $image = json_decode($value->picture);
                $cate = Cate::get(['id' => $value->cate_id]);
                if (false == empty($image))
                {
                    $value->picture = $host . $image[0];
                    $value->cate_id = $cate->cate_name;
                }
            }
        }
        return $list;
    }

    /**
     * 以类别分组
     * @return array
     * @throws \think\exception\DbException
     */
    public static function getAllGoodsGroupByCate()
    {
        $cate = Cate::all();
        foreach ($cate as $value)
        {
            $item = Goods::all(function ($query)use ($value){
                $query->where('cate_id',$value->id)->order('id','desc');
            });
            $result[] = $item;
        }
        return $result;
    }


    public static function getGoodsById($id)
    {
        $goods = Goods::get($id);
        $image = json_decode($goods->picture);
        if (false == empty($image))
        {
            foreach ($image as $key => $value)
            {
                $image[$key] = config('img_url') . $value;
            }
            $goods->picture = json_encode($image);
        }
        return $goods;
    }
}
