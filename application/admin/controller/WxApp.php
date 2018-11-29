<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/18
 * Time: 16:06
 */

namespace app\admin\controller;

use app\admin\model\Banner;
use app\admin\model\BrandModel;
use app\admin\model\Cate;
use app\admin\model\ChannelModel;
use app\admin\model\Goods;
use think\Controller;
use think\Log;

class WxApp extends Controller
{
    /**
     * banner列表页面
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function bannerSettings()
    {
        $list = Banner::getBanner();
        $this->assign('list', $list);
        $this->assign('name', '');
        return $this->fetch();
    }

    /**
     * 搜索
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function search()
    {
        $name = input('search');
        $mode = input('mode');
        switch ($mode) {
            case 'banner':
                $list = Banner::searchBanner($name);
                $url = '../application/admin/view/wx_app/bannerSettings.html';
                break;
            case 'channel':
                $list = ChannelModel::searchChannel($name);
                $url = '../application/admin/view/wx_app/channelSettings.html';
        }

        $this->assign('name', $name);
        $this->assign('list', $list);
        return $this->fetch($url);
    }

    /**
     * 添加banner页面（banner添加页面）
     * @return mixed
     * @author Qiu
     */
    public function banner_add()
    {
        $info = array(
            'id' => '',
            'name' => '',
            'link' => ''
        );
        $this->assign('info', $info);
        $this->assign('img_list', '');
        return $this->fetch();
    }

    /**
     * 实时搜索
     * @return false|string
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function getProduct()
    {
        $name = input('search_name');
        $condition['goods_name'] = array('like', '%' . $name . '%');
        $list = Goods::all(function ($query) use ($condition) {
            $query->field('id,goods_name')->where($condition)->order('id', 'desc');
        });
        return json_encode($list);
    }

    /**
     * 更新banner（处理添加逻辑）
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function banner_update()
    {
        $info = input();
        $host = config('img_url');
        $image_url = $host . $info['image'];
        $path = str_replace("\\", "/", $image_url);
        $link = Goods::field('id')->where('goods_name', $info['link'])->find();
        $data = array(
            'name' => $info['name'],
            'image_url' => str_replace(',', '', $path),
            'link' => $link['id']
        );
        $result = Banner::create($data);
        if ($result) {
            $this->success('添加成功', 'admin/WxApp/bannerSettings');
        } else {
            $this->success($result->getError());
        }
    }

    /**
     * 单一删除
     * @return array|int|string
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function single_del()
    {
        $id = input('id');
        $mode = input('mode');
        switch ($mode) {
            case 'banner':
                $result = Banner::get($id);
                break;
            case 'channel':
                $result = ChannelModel::get($id);
                break;
            case 'brand':
                $result = BrandModel::get($id);
                break;
        }
        if ($result->delete()) {
            return 1;
        } else {
            return $result->getError();
        }
    }

    /**
     * 频道首页
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function channelSettings()
    {
        $list = ChannelModel::order('sort_order')->paginate(15, false, [
            'query' => request()->param()
        ]);
        $this->assign('list', $list);
        $this->assign('name');
        return $this->fetch();
    }

    /**
     * 频道添加
     * @return mixed
     * @author Qiu
     */
    public function channel_add()
    {
        if (isset($_POST['doSubmit'])) {
            $info = input();
            $host = config('img_url');
            $icon_url = $host . $info['image'];
            $path = str_replace("\\", "/", $icon_url);
            $sort_order = ChannelModel::count();
            $data = array(
                'name' => $info['name'],
                'icon_url' => str_replace(',', '', $path),
                'url' => 'aaaa',
                'sort_order' => $sort_order + 1
            );
            if ($result = ChannelModel::create($data)) {
                $this->success('添加成功', 'admin/WxApp/channelSettings');
            } else {
                $this->success($result->getError());
            }
        } else {
            $info = array(
                'id' => '',
                'name' => '',

            );
            $this->assign('img_list');
            $this->assign('info', $info);
            return $this->fetch();
        }
    }

    /**
     * channel排序
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function channel_sort_order()
    {
        if (isset($_POST['doSubmit'])) {
            $sort = input('sort/a');
            $id = input('id/a');
            foreach ($id as $key => $value) {
                $list[] = array('id' => $value, 'sort_order' => $sort[$key]);
            }
            $channel = new ChannelModel();
            if ($result = $channel->saveAll($list)) {
                $this->success('修改成功', 'admin/WxApp/channelSettings');
            } else {
                $this->success($result->getError());
            }
        } else {
            $list = ChannelModel::order('sort_order')->paginate(15, false, [
                'query' => request()->param()
            ]);
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 品牌设定首页
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function brandSettings()
    {
        $list = BrandModel::getBrand();
        $this->assign('list', $list);
        $this->assign('name');
        return $this->fetch();
    }

    /**
     * 增加品牌
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function brand_add()
    {
        if (isset($_POST['doSubmit'])) {
            //获取统计
            $count = BrandModel::count();
            if ($count >= 6){
                return $this->error('已超过上限');
            }
            $cate_id = input('cate_id');
            //根据cate_id获取图片，名字
            $cate = Cate::find($cate_id);
            $name = $cate['cate_name'];
            $host = config('img_url');
            $image = $cate['image'] ? json_decode($cate['image'],true) : '';
            $image_url = isset($image[0]) ? $host . $image[0] : '';
            $pic_url = str_replace("\\", "/", $image_url);
            //根据cate_id获取产品最低价格
            $floor_price = Goods::where('cate_id',$cate_id)->order('retail_price','asc')->value('retail_price');
            $data = array(
                'cate_id' => $cate_id,
                'name' => $name,
                'pic_url' => $pic_url,
                'sort_order' => $count + 1,
                'floor_price' => $floor_price
            );
            if($result = BrandModel::insertData($data)){
                return $this->success('添加成功','admin/WxApp/brandSettings');
            }else{
                return $this->error($result->getError());
            }
        } else {
            $info = array(
                'id' => '',
            );
            $cate = Cate::all();
            $this->assign('cate',$cate);
            $this->assign('info', $info);
            return $this->fetch();
        }
    }

    /**
     * 实时搜索分类名字
     * @return false|string
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function getCate()
    {
        $cate_name = input('search_name');
        $condition['cate_name'] = array('like', '%' . $cate_name . '%');
        $list = Cate::all(function ($query) use ($condition) {
            $query->where($condition)->order('id', 'desc');
        });
        return json_encode($list);
    }

    /**
     * brand排序修改
     * @return mixed
     * @throws \think\exception\DbException
     * @author Qiu
     */
    public function brand_sort_order(){
        if (isset($_POST['doSubmit'])){
            $sort = input('sort/a');
            $id = input('id/a');
            foreach ($id as $key => $value) {
                $list[] = array('id' => $value, 'sort_order' => $sort[$key]);
            }
            $brand = new BrandModel();
            if ($result = $brand->saveAll($list)) {
                $this->success('修改成功', 'admin/WxApp/brandSettings');
            } else {
                $this->success($result->getError());
            }
        }else{
            $list = BrandModel::getBrand();
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
}
