<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/21
 * Time: 13:58
 */
namespace   app\admin\controller;

use think\Controller;
use  think\captcha\Captcha;
use think\Session;
use Org\Util\Rbac;

class Login  extends  Controller{
    protected $admin;
    public  function __construct()
    {
        parent::__construct();
        $this->admin=model('Admin');
    }

    public function index(){
        if(session('uid')>0){
            return redirect('Index/index');
        }else{
            return $this->fetch();
        }


    }

    public  function verify(){
        ob_clean();//清除缓存
        $captcha= new Captcha();
        $captcha->fontsize=30;
        $captcha->length=4;
        $captcha->useNoise=false;
        $captcha->useCurve=false;
        return $captcha->entry();
    }
    public function login(){
        $code=input('code');
        //var_dump( $code);die;
        $username=input('username');
        $password=input('password');
        if(empty($code) || empty($username) || empty($password)){
            return json(array('status'=>0,'info'=>'参数不正确'));
        }
        $captcha=new Captcha();
        if(!$captcha->check($code)){
            return json(array('status'=>0,'info'=>'验证码不正确'));
        }
        //根据用户查询用户的数据
        $admin_one=$this->admin->getOne(array('username'=>$username));
        // echo getRandKey();die;
        // var_dump($admin_one['salt']);die;
        // var_dump( md5(md5($password).$admin_one['salt']));die;
        if(!empty($admin_one) && $admin_one['password'] == md5(md5($password).$admin_one['salt'])){
            session('uid',$admin_one['admin_id']);
            session('username',$admin_one['username']);
            session('login_time',date('Y-m-d H:i:s',time()));
            session('login_ip',get_client_ip());
            if ($username == config('rbac.RBAC_SUPERADMIN')) {
                session(config('rbac.ADMIN_AUTH_KEY'), true);
            }
            //读取用户权限
            Rbac::saveAccessList();
            //var_dump($_SESSION);die;
            //更新用户信息
            $data=array('login_time'=>time(),
                'login_ip'=>get_client_ip()
            );

            $this->admin->saveData(array('admin_id'=>$admin_one['admin_id']),$data);
            return json(array('status'=>1,'info'=>'登录成功'));
        }else{

            return json(array('status'=>0,'info'=>'账号或者密码错误'));
        }
    }
    public function logout(){
        session('uid',null);
        session('_ACCESS_LIST',null);
        session(config('rbac.ADMIN_AUTH_KEY'),null);
        $this->success('退出成功',url('login/index'));
    }
}