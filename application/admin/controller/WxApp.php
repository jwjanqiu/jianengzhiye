<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/10/18
 * Time: 16:06
 */

namespace app\admin\controller;

use app\admin\model\Banner;
use app\admin\model\Cate;
use app\admin\model\ChannelModel;
use app\admin\model\Goods;
use think\Controller;

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
    public function sort_order()
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

    public function brandSettings()
    {
        $host = config('img_url');
        $path = str_replace("\\", "/", $host);
        $list = Cate::paginate(15, false, [
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
        $this->assign('list', $list);
        $this->assign('name');
        return $this->fetch();
    }
}
