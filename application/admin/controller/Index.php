<?php

namespace app\admin\controller;

use app\admin\model\Banner;
use app\admin\model\ChannelModel;
use app\admin\model\Consume;
use app\admin\model\Membership;
use app\admin\model\Target;
use think\Controller;
use app\admin\model\Cate as CateModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Consume as ConsumeModel;
use app\admin\model\Target as TargetModel;
use app\admin\model\Membership as MembershipModel;
use app\admin\model\Promotion;
use think\Db;
use think\Loader;


class Index extends Controller
{
    //判断是否登录
    public function _initialize()
    {
        $username = cookie('username');
        if ($username == null) {
            $this->redirect('Index.php/admin/login/index');
        }
    }

    //主页
    public function index()
    {
        $username = cookie('username');
//        $result = ConsumeModel::getNewOrderNotify();
//        $this->assign('newOrder',$result);
        //每月目标
        $monthlyTarget = TargetModel::whereTime('create_time', 'm')->value('amount');
        //目前收入
        $income = ConsumeModel::where('isconfirm', 2)->whereTime('create_time', 'm')->sum('total_price');
        //当季收入
        $season = ceil((date('n')) / 3);
        $sTime = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y')));
        $eTime = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y')));
        $seasonIncome = ConsumeModel::where('isconfirm', 2)->whereTime('create_time', 'between', [$sTime, $eTime])->sum('total_price');
        $serverInfo = array(
            'Operation_System' => PHP_OS,
            'Runtime_environment' => $_SERVER["SERVER_SOFTWARE"],
            'Server_Date' => date("Y-m-d"),
            'Domain_Name' => $_SERVER["SERVER_NAME"],
            'Disk_free_Space' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M'
        );
        //首页购物记录
        $list = ConsumeModel::all(function ($query) {
            $query->where('isread', 0)->order('id', 'desc')->limit(5);
        });

        //销售最佳
        $info = GoodsModel::getMaxPurchase();
        $host = config('img_url');
        $img_array = json_decode($info['picture']);
//        print_r($img_array);exit();
        $detail = array(
            'name' => $info->goods_name,
            'num' => $info->purchase
        );
        if (false == empty($img_array)) {
            $detail['img'] = $host . $img_array[0];
        } else {
            $detail['img'] = '';
        }
//        print_r($info);exit();
        $this->assign('detail', $detail);
        $this->assign('list', $list);
        $this->assign('serverInfo', $serverInfo);
        $this->assign('username', $username);
        $this->assign('monthlyTarget', $monthlyTarget);
        $this->assign('sum', $income - $monthlyTarget);
        $this->assign('income', $income);
        $this->assign('seasonIncome', $seasonIncome);
        return $this->fetch();
    }

    //退出登录
    public function logout()
    {
        cookie('username', null);
        $this->redirect('Index.php/admin/login/index');
    }

    //分类
    public function category()
    {
        $username = cookie('username');
        $list = CateModel::order('id', 'desc')->paginate(15, false, [
            'query' => request()->param()
        ]);
        $this->assign('username', $username);
        $this->assign('list', $list);
        return $this->fetch();
    }

    //添加或更新分类
    public function cate_update()
    {
        $id = input('id');
        $cate_name = input('cate_name');
        $channel_id = input('channel');
        $old_img = input('post.old_img/a');
        $new_img = input('image');
        $img_data = [];//保存照片信息
        if (false == empty($new_img)) {
            $img_data = explode(',', rtrim($new_img, ','));
        }
        if (false == empty($old_img)) {
            foreach ($old_img as $key => $value) {
                $img_data[] = $value;
            }
        }
        $category = new CateModel;
        if (empty($id)) {
            $data = array(
                'cate_name' => $cate_name,
                'channel_id' => $channel_id
            );
            if (false == empty($img_data)) {
                $data['image'] = json_encode($img_data);
            }
            if ($category->validate('Cate')->save($data)) {
                $this->success('添加成功', 'admin/index/category');
            } else {
                $this->error($category->getError());
            }
        } else {
            $data = array(
                'id' => $id,
                'cate_name' => $cate_name,
                'channel_id' => $channel_id,
                'image' => json_encode($img_data)
            );
            if (CateModel::update($data)) {
                $this->success('修改成功', 'admin/index/category');
            } else {
                $this->error('修改失败');
            }
        }
    }

    //返回添加页面
    public function category_add()
    {
        $select = ChannelModel::all(function ($query) {
            $query->order('sort_order', 'asc');
        });
        if (count($select) < 1) {
            return $this->error('请先添加频道');
        }
        $this->assign('select', $select);
        $this->assign('img_list', '');
        $this->assign('username', cookie('username'));
        $this->assign('info', array(
            'id' => '',
            'channel_id' => '',
            'cate_name' => ''
        ));
        return $this->fetch();
    }

    //分类页面搜索
    public function category_search()
    {
        $cate_name = input('search_cate');
        if (false == empty($cate_name)) {
            $list = CateModel::where(function ($query) use ($cate_name) {
                $query->where('cate_name', 'like', '%' . $cate_name . '%')->order('id', 'desc');
            })->paginate(15, false, [
                "query" => request()->param()
            ]);
        } else {
            $list = CateModel::order('id', 'desc')->paginate(15, false, [
                'query' => request()->param()
            ]);
        }
        $this->assign('list', $list);
        $this->assign('username', cookie('username'));
        return $this->fetch('../application/admin/view/index/category.html');
    }

    //分类页面删除
    public function category_del()
    {
        $id = input('id');
        $category = CateModel::get($id);
        if ($category->delete()) {
            return 1;
        } else {
            return $category->getError();
        }
    }

    //分类页面修改
    public function category_edit()
    {
        $host = config('img_url');
        $path = str_replace("\\", "/", $host);
        $id = input('id');
        $info = CateModel::get($id);
        $img_array = json_decode($info['image']);
        if (false == empty($img_array)) {
            foreach ($img_array as $key => $value) {
                $img_list[$key]['id'] = $key;
                $img_list[$key]['img'] = $path . $value;
                $img_list[$key]['name'] = $value;
            }
        } else {
            $img_list = '';
        }
        $select = ChannelModel::all(function ($query) {
            $query->order('sort_order', 'asc');
        });
        $this->assign('select',$select);
        $this->assign('img_list', $img_list);
        $this->assign('info', $info);
        $this->assign('username', cookie('username'));
        return $this->fetch('../application/admin/view/index/category_add.html');
    }

    //分类页面批量删除
    public function multidel()
    {
        $id = input('id');
        $id_str = substr($id, 0, -1);
        $mode = input('mode');
        if (false == empty($id_str)) {
            switch ($mode) {
                case 'product':
                    GoodsModel::destroy($id_str);
                    break;
                case 'cate':
                    CateModel::destroy($id_str);
                    break;
                case 'member':
                    MembershipModel::destroy($id_str);
                    break;
                case 'promotion':
                    Promotion::destroy($id_str);
                    break;
                case 'banner':
                    Banner::destroy($id_str);
                    break;
                case 'channel':
                    ChannelModel::destroy($id_str);
                    break;
            }
        }
        return 1;
    }

    //商品管理页面
    public function product()
    {
        $list = GoodsModel::order('id', 'desc')->paginate(15, false, [
            'query' => request()->param()
        ]);
        $this->assign('list', $list);
        $this->assign('username', cookie('username'));
        return $this->fetch();
    }

    //返回添加页面
    public function product_add()
    {
        $this->assign('username', cookie('username'));
        $info = array(
            'id' => '',
            'goods_name' => '',
            'cate_id' => '',
            'retail_price' => '',
            'purchasing_cost' => '',
            'stock' => '',
            'bar_code' => '',
            'introduction' => '',
            'promotion' => ''
        );
        $select = CateModel::all();
        $promotionList = Promotion::all();
        $this->assign('select', $select);
        $this->assign('promotionList', $promotionList);
        $this->assign('img_list', '');
        $this->assign('info', $info);
        return $this->fetch();
    }

    //商品搜索页面
    public function product_search()
    {
        $name = input('search_name');
        $list = GoodsModel::where(function ($query) use ($name) {
            $query->where('goods_name', 'like', '%' . $name . '%')->order('id', 'desc');
        })->paginate(15, false, [
            "query" => request()->param()
        ]);
        $this->assign('list', $list);
        $this->assign('username', cookie('username'));
        return $this->fetch('../application/admin/view/index/product.html');
    }

    //商品编辑页面
    public function product_edit()
    {
        $host = config('img_url');
        $path = str_replace("\\", "/", $host);
        $id = input('id');
        $select = CateModel::all();
        $promotionList = Promotion::all();
        $info = GoodsModel::get([
            'id' => $id
        ]);
        $img_array = json_decode($info['picture']);
        if (false == empty($img_array)) {
            foreach ($img_array as $key => $value) {
                $img_list[$key]['id'] = $key;
                $img_list[$key]['img'] = $path . $value;
                $img_list[$key]['name'] = $value;
            }
        } else {
            $img_list = '';
        }
        $this->assign('img_list', $img_list);
        $this->assign('promotionList', $promotionList);
        $this->assign('select', $select);
        $this->assign('info', $info);
        $this->assign('username', cookie('username'));
//        print_r($img_list);exit();
        return $this->fetch('../application/admin/view/index/product_add.html');
    }

    //更新或添加产品
    public function product_update()
    {
        $id = input('id');
        $goods_name = input('goods_name');
        $cate = input('cate');
        $promotion = input('promotion');
        $retail_price = input('retail_price');
        $purchasing_cost = input('purchasing_cost');
        $stock = input('stock');
        $bar_code = input('bar_code');
        $old_img = input('post.old_img/a');
        $new_img = input('image');
        $introduction = input('introduction');
        $img_data = [];//保存照片信息
        if (false == empty($new_img)) {
            $img_data = explode(',', rtrim($new_img, ','));
        }
        $product = new GoodsModel();
        if (false == empty($old_img)) {
            foreach ($old_img as $key => $value) {
                $img_data[] = $value;
            }
        }
//        $a = rtrim($new_img,',');
//        print_r($img_data);
//        exit();
        if (empty($id)) {
            $data = array(
                'goods_name' => $goods_name,
                'cate_id' => $cate,
                'promotion' => $promotion,
                'retail_price' => $retail_price,
                'purchasing_cost' => $purchasing_cost,
                'stock' => $stock,
                'bar_code' => $bar_code,
                'introduction' => $introduction
            );
            if (false == empty($img_data)) {
                $data['picture'] = json_encode($img_data);
            }
//            print_r($data);exit();
            if ($result = $product->validate('Product')->save($data)) {
                $this->success('添加成功', 'admin/index/product');
            } else {
                $this->error($product->getError());
            }
        } else {
            $data = array(
                'id' => $id,
                'goods_name' => $goods_name,
                'cate_id' => $cate,
                'promotion' => $promotion,
                'retail_price' => $retail_price,
                'purchasing_cost' => $purchasing_cost,
                'stock' => $stock,
                'bar_code' => $bar_code,
                'introduction' => $introduction,
                'picture' => json_encode($img_data)
            );
            if ($result = GoodsModel::update($data)) {
                $this->success('修改成功', 'admin/index/product');
            } else {
                $this->success($result->getError());
            }
        }
//        print_r($data);
    }

    //图片处理
    public function image_process()
    {
        $files = \request()->file('');
//        $info = $files->move(ROOT_PATH . 'public' . DS . 'uploads');

//        print_r($files);
//        $name = [];
        foreach ($files as $item) {
            $info = $item->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $path['name'] = $info->getSaveName();
            }
//            Log::write('data: '.$info,'info');
        }
//        print_r(json($path));
        return json_encode($path);
    }

    //插入数据
    public function addData()
    {
        $data['goods'] = '贪玩蓝月';
        $data['total_price'] = 25.50;
        $data['address'] = '广东省广州市海珠区新港东路2433号';
        $data['phone'] = 13432380277;
        if ($result = ConsumeModel::create($data)) {
            $this->success('添加成功');
        } else {
            $this->error($result->getError());
        }
    }

    //账单
    public function bill()
    {
        $this->assign('username', cookie('username'));
        return $this->fetch();
    }

    //每月账单
    public function monthBill()
    {
//        $arr = ConsumeModel::all();
//        echo json_encode($arr);
        $month = date('n');//当前月份
        $data = array();
        if ($month != 1) {
            for ($i = $month; $i >= 1; $i--) {
                $start_time = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - ($i - 1), 1, date("Y"))));
                $end_time = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") - ($i - 2), 0, date("Y"))));
//            echo $start_time . "-" . $end_time.'<br>';
                $data[] = Db::table('jnzy_consume')
                    ->where('isconfirm', 2)
                    ->whereTime('create_time', 'between', [$start_time, $end_time])
                    ->sum('total_price');
            }
        } else {
            $start_time = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y"))));
            $end_time = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("t"), date("Y"))));
            $data[] = Db::table('jnzy_consume')
                ->where('isconfirm', 2)
                ->whereTime('create_time', 'between', [$start_time, $end_time])
                ->sum('total_price');
        }
        echo json_encode($data);
    }

    //季度账单
    public function seasonBill()
    {
        $season = ceil((date('n') / 3));//当月是第几季度
//        echo '<br>本季度起始时间:<br>';
//        echo $season."<br>";
        if ($season == 1) {
            $start_time = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y')));
            $end_time = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y')));
            $data[] = Db::table('jnzy_consume')
                ->where('isconfirm', 2)
                ->whereTime('create_time', 'between', [$start_time, $end_time])
                ->sum('total_price');
        } else {
            for ($i = $season - 1; $i >= 0; $i--) {
                $tem = $season - $i;
                $start_time = date('Y-m-d H:i:s', mktime(0, 0, 0, $tem * 3 - 3 + 1, 1, date('Y')));
                $end_time = date('Y-m-d H:i:s', mktime(23, 59, 59, $tem * 3, date('t', mktime(0, 0, 0, $season * 3, 1, date("Y"))), date('Y')));
                $data[] = Db::table('jnzy_consume')
                    ->where('isconfirm', 2)
                    ->whereTime('create_time', 'between', [$start_time, $end_time])
                    ->sum('total_price');
            }
        }
        echo json_encode($data);
    }

    //近五天账单
    public function fiveDaysBill()
    {
        for ($i = 5; $i >= 1; $i--) {
            $beginTime = mktime(0, 0, 0, date('m'), date('d') - $i, date('Y'));
            $endTime = mktime(0, 0, 0, date('m'), date('d') - ($i - 1), date('Y')) - 1;
//            echo $beginTime .'-'. $endTime.'<br>';
            $sum = ConsumeModel::where('isconfirm', 2)->whereTime('create_time', 'between', [$beginTime, $endTime])->sum('total_price');
            $info['sum'] = $sum;
            $info['time'] = date('Y-m-d', $beginTime);
            $data[] = $info;
//            echo ConsumeModel::getLastSql().'<br>';
        }
        echo json_encode($data);
    }

    //购物记录
    public function consume()
    {
        $list = ConsumeModel::order('id', 'desc')->paginate(15, false, [
            'query' => request()->param()
        ]);
        $this->assign('list', $list);
        $this->assign('username', cookie('username'));
        return $this->fetch();
    }

    //购物搜索
    public function consume_search()
    {
        $search = input('search_field');
        $condition['order_id|name|phone'] = array('like', '%' . $search . '%');
        $list = ConsumeModel::where(function ($query) use ($condition) {
            $query->where($condition)->order('id' . 'desc');
        })->paginate(15, false, [
            'query' => \request()->param()
        ]);
        $this->assign('list', $list);
        $this->assign('username', cookie('username'));
        return $this->fetch('../application/admin/view/index/consume.html');
    }

    //状态改变
    public function consume_confirm()
    {
        $id = input('id');
        $code = input('code');
        $info = ConsumeModel::get($id);
        switch ($code) {
            case -1:
                $info->isconfirm = -1;
                $info->isread = 1;
                break;
            case 1:
                $info->isconfirm = 1;
                $info->isread = 1;
                break;
            case 2:
                $info->isconfirm = 2;
                $info->isread = 1;
                break;
        }
        $result = $info->save();
        if ($result) {
            return 1;
        } else {
            return false;
        }
    }

    //账单详情
    public function consume_info()
    {
        $id = input("id");
        $info = ConsumeModel::get($id);
//        print_r($info);exit();
        $this->assign('info', $info);
        $this->assign('username', cookie("username"));
        return $this->fetch();
    }

    //实时消息
    public function realTimeNotify()
    {
        $result = ConsumeModel::getNewOrderNotify();
        if ($result) {
            echo json_encode($result);
        } else {
            echo 0;
        }
    }

    //月目标
    public function monthlyTarget()
    {
        $amount = input('amount');
        $data = Target::get(function ($query) {
            $query->whereTime('create_time', 'm');
        });
        if (false == empty($data)) {
            $data->amount = $amount;
            $result = $data->save();
        } else {
            $data['amount'] = $amount;
            $result = TargetModel::create($data);
        }
        if ($result) {
            return 1;
        } else {
            return false;
        }
    }

    //会员主页
    public function membership()
    {
        $membership = MembershipModel::queryAllMembership();
        $this->assign('username', cookie('username'));
        $this->assign('list', $membership);
        return $this->fetch();
    }

    //会员添加
    public function member_add()
    {
        $this->assign('username', cookie('username'));
        $this->assign('info', array(
            'id' => '',
            'name' => '',
            'sex' => '',
            'mobile' => '',
            'birthday' => ''
        ));
        return $this->fetch();
    }

    //会员编辑
    public function member_edit()
    {
        $id = input('id');
        $info = MembershipModel::queryMembership($id);
        $this->assign('username', cookie('username'));
        $this->assign('info', $info);
        return $this->fetch('../application/admin/view/index/member_add.html');
    }

    //会员更新
    public function member_update()
    {
        $id = input('id');
        $name = input('name');
        $sex = input('sex');
        $mobile = input('mobile');
        $birthday = input('birthday');
        $credit = input('credit');
        $password = input('password');
        $validate = Loader::validate('Membership');
        if (empty($id)) {
            $data = array(
                'name' => $name,
                'mobile' => $mobile,
                'sex' => $sex,
                'birthday' => $birthday
            );
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $result = MembershipModel::addMembership($data);
            if ($result == "该手机号码已注册") {
                $this->error($result);
            } elseif ($result == "注册成功") {
                $this->success('添加成功', 'admin/index/membership');
            } else {
                $this->error($result);
            }
        } else {
            $data = array(
                'id' => $id,
                'name' => $name,
                'password' => $password,
                'credit' => $credit,
                'sex' => $sex,
                'mobile' => $mobile,
                'birthday' => $birthday
            );
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $result = Membership::updateMembership($data);
            if ($result == '更新成功') {
                $this->success('修改成功', 'admin/index/membership');
            } else {
                $this->error($result);
            }
        }
    }

    //会员删除
    public function member_del()
    {
        $id = input('id');
        $result = MembershipModel::deleteMembership($id);
        if ($result) {
            return 1;
        } else {
            return $result;
        }
    }

    //会员搜索
    public function member_search()
    {
        $member = input('search_member');
        $memberList = Membership::searchMember($member);
        $this->assign('username', cookie('username'));
        $this->assign('list', $memberList);
        return $this->fetch('../application/admin/view/index/membership.html');
    }

    //促销类型
    public function promotion()
    {
        $list = Promotion::queryAllPromotion();
        $this->assign('username', cookie('username'));
        $this->assign('list', $list);
        return $this->fetch();
    }

    //促销添加
    public function promotion_add()
    {
        $this->assign('username', cookie('username'));
        $this->assign('info', array(
            'id' => '',
            'promotion_name' => ''
        ));
        return $this->fetch();
    }

    //促销编辑
    public function promotion_edit()
    {
        $id = input('id');
        $info = Promotion::queryPromotion($id);
        $this->assign('username', cookie('username'));
        $this->assign('info', $info);
        return $this->fetch('../application/admin/view/index/promotion_add.html');
    }

    //促销更新
    public function promotion_update()
    {
        $id = input('id');
        $promotion_name = input('promotion_name');
        $validate = Loader::validate('Promotion');
        if (empty($id)) {
            $data = array(
                'promotion_name' => $promotion_name
            );
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $result = Promotion::addPromotion($data);
            if ($result == '添加成功') {
                $this->success($result, 'admin/index/promotion');
            } else {
                $this->error($result);
            }
        } else {
            $data = array(
                'id' => $id,
                'promotion_name' => $promotion_name
            );
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $result = Promotion::updatePromotion($data);
            if ($result == '更新成功') {
                $this->success('更新成功', 'admin/index/promotion');
            } else {
                $this->error($result);
            }
        }
    }

    //促销删除
    public function promotion_del()
    {
        $id = input('id');
        $result = Promotion::deletePromotion($id);
        if ($result) {
            return 1;
        } else {
            return $result;
        }
    }

    //促销搜索
    public function promotion_search()
    {
        $promotion_name = input('search_promotion');
        $promotionList = Promotion::searchPromotion($promotion_name);
        $this->assign('username', cookie('username'));
        $this->assign('list', $promotionList);
        return $this->fetch('../application/admin/view/index/promotion.html');
    }
}

