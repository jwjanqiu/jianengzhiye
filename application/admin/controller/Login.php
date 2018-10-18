<?php
/**
 * Created by PhpStorm.
 * User: jwjan
 * Date: 2017/12/27
 * Time: 23:36
 */
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    //登录
    public function login()
    {
        $username = input('username');
        $password = input('password');
        $result = AdminModel::get([
            'username' => $username,
            'password' => sha1($password)
        ]);
        if(false == empty($result)){
            cookie('username',$username,43200);
            $this->redirect('index/index');
        }else{
            $this->error('用户名或密码错误');
        }
    }
 }