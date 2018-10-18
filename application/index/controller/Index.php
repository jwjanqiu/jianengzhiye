<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/4/16
 * Time: 11:32
 */

namespace app\index\controller;

use think\Controller;
use app\admin\model\Membership;
use app\admin\model\Goods;
use app\index\model\Membership as MembershipModel;
use app\admin\model\Cate;
use think\Loader;

class Index extends Controller
{
    /**
     * 商城首页
     * @return mixed
     */
    public function index()
    {
        $mode = input('mode');
        if ($mode == 'other')
        {
            $user = cookie('user');
        }else{
            $user = input('user');
            cookie('user',$user);
        }
        $this->assign('user',$user);
        return $this->fetch();
    }

    /**
     * 注册
     * @return mixed
     */
    public function register()
    {
        return $this->fetch();
    }

    public function member_update()
    {
        $data = array(
            'name' => input('name'),
            'mobile' => input('mobile'),
            'password' => input('password'),
            'password_confirm' => input('password_confirm')
        );
        $validate = Loader::validate('Membership');
        if (!$validate->check($data))
        {
            $this->error($validate->getError());
        }
        $result = MembershipModel::addMembership($data);
        if ($result == '该手机号码已注册')
        {
            $this->error($result);
        }elseif ($result == "注册成功")
        {
            $this->success('注册成功',url('index/index/index',array('user'=> $data['name'])));
        }else{
            $this->error($result);
        }
    }

    /**
     * 登录页面
     * @return mixed
     */
    public function login()
    {
        return $this->fetch();
    }

    /**
     * 登录判断
     */
    public function checkUser()
    {
        $mobile = input('mobile');
        $password = input('password');
        $data = array('mobile' => $mobile,'password' => $password);
        $result = Membership::login($data);
        if (false == empty($result))
        {
            $this->success('登录成功',url('index/index/index',array('user'=> $result->name)));
        }else{
            $this->error('用户名或密码错误,请重新登录');
        }
    }
    /**
     *退出登录
     */
    public function logout()
    {
        cookie('user',null);
        $this->redirect('index/index/index');
    }

    /**
     * 查询所有符合条件货物
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function search_goods()
    {
        $goods_name = input('goods_name');
        $order = input('order');
        $list = Goods::getGoods($goods_name,$order);
        $this->assign('goods_name',$goods_name);
        $this->assign('count',count($list));
        $this->assign('user',cookie('user'));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 根据条件搜索商品
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function search_goods_withCondition()
    {
        $goods_name = input('goods_name');
        $condition = input('condition');
        $list = Goods::getGoodsWithCondition($goods_name,$condition);
        $this->assign('goods_name',$goods_name);
        $this->assign('count',count($list));
        $this->assign('user',cookie('user'));
        $this->assign('list',$list);
        return $this->fetch('../application/index/view/index/search_goods.html');
    }

    /**
     * 分类页面
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function cate_index()
    {
        $cateList = Cate::cateIndex();
        $goods = Goods::all();
        $this->assign('user',cookie('user'));
        $this->assign('goods',$goods);
        $this->assign('cateList',$cateList);
        return $this->fetch();
    }


    public function production_introduction()
    {
        $id = input('id');
        $goods = Goods::getGoodsById($id);
        $image = json_decode($goods->picture);
        $this->assign('goods',$goods);
        $this->assign('image',$image);
        $this->assign('user',cookie('user'));
        return $this->fetch();
    }
}