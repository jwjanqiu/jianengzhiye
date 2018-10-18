<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/5/2
 * Time: 17:17
 */
namespace app\index\model;
use think\Model;

class Membership extends Model
{
    protected $table = 'jnzy_membership';

    protected $insert = ['credit' => 0,'sex' => 2,'birthday' => '1970-01-01'];

    /**
     * 添加新会员
     * @param $data
     * @return array|bool|string
     */
    public static function addMembership($data)
    {
        $check = Membership::get(['mobile' => $data['mobile']]);
        if(false == empty($check))
        {
            return "该手机号码已注册";
        }
        unset($data['password_confirm']);
        $result = Membership::create($data);
        if($result){
            return "注册成功";
        }else{
            return Membership::getError();
        }
    }
}