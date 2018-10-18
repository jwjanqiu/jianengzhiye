<?php
/**
 * Created by PhpStorm.
 * User: PC-Qiu
 * Date: 2018/4/10
 * Time: 17:36
 */
namespace app\admin\model;
use think\Model;

class Membership extends Model
{
    protected $table = 'jnzy_membership';

    protected $insert = ['credit' => 0,'password'=>'123456'];

    protected function getSexAttr($value)
    {
        $sex = [
            0 => '女',
            1 => '男',
            2 => '保密'
        ];
        return $sex[$value];
    }

    /**
     * 查询所有会员信息
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function queryAllMembership()
    {
        $data = Membership::order('id','desc')->paginate(15,false,[
            'query' => request()->param()
        ]);
        return $data;
    }

    /**
     * 查询某个会员
     * @param $id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function queryMembership($id)
    {
        $data = Membership::get($id);
        return $data;
    }

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
        $result = Membership::create($data);
        if($result){
            return "注册成功";
        }else{
            return Membership::getError();
        }
    }

    /**
     * 更新用户数据
     * @param $data
     * @return array|bool|string
     * @throws \think\exception\DbException
     */
    public static function updateMembership($data)
    {
        $check = Membership::get(['mobile' => $data['mobile']]);
        if(false == empty($check))
        {
            return "该手机号码已注册";
        }
        $result = Membership::update($data);
        if ($result)
        {
            return '更新成功';
        }else{
            return Membership::getError();
        }
    }

    /**
     * 删除会员
     * @param $id
     * @return array|bool|string
     */
    public static function deleteMembership($id)
    {
        if (Membership::destroy($id))
        {
            return true;
        }else{
            return Membership::getError();
        }
    }

    /**
     * 根据电话或名字模糊搜索某个会员
     * @param $member
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function searchMember($member)
    {
        $condition['name|mobile'] = array('like','%' . $member . '%');
        $data = Membership::where($condition)->order('id','desc')->paginate(15,false,[
            'query' => request()->param()
        ]);
        return $data;
    }


    public static function login($data)
    {
        $result = Membership::get($data);
        return $result;
    }
}
