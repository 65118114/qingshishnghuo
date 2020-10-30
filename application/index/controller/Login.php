<?php
namespace app\index\controller;
use Think\Controller;
use aliyunsms\api_demo\SmsDemo  ;
use think\Db;
use think\facade\Session;
use think\Request;

class Login
{
   
    //注册发送验证码
    public function send()
    {
        //跨域
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        // echo '111';
          $mobile=trim(input('mobile'));
        //  var_dump( $mobile);die;
           $code = mt_rand(100000,999999);
        $demo=new SmsDemo();
         // var_dump($demo);die;
       $response= $demo->sendSms($mobile,$code);
      if($response->Message=="OK"){
           session('mobile_code',$code,300);
            return json(array('status'=>1,'info'=>$code));
      }else{
            return json(array('status'=>0,'info'=>'网络错误'));
      }
    }

    //随机生成一个用户邀请码
    public  function activeinfo(){
        //跨域
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
     //   $res=Db::name('user')->where('rand()')->limit(1)->value('mobile');
        $res=Db::name('user')->where('actstatus',1)->where('rand()')->limit(1)->value('mobile');
       // $res='13888888888';
        return json(['status' => 1,'info'=>$res]);
    }
    //绑定关系
    public function userinfo(Request $request)
    {
        //跨域
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
       // $userinfo=Db::name('user')->where('mobile', $mobile)->find();

        $one = Db::name('user')->where('mobile', $mobile)->value('pid');
        if ($request->isPost()) {
            $pid = trim(input('pid'));
          //  var_dump($pid);die;
            if (empty($one)) {
                //推荐人的上级
                $ppid = Db::name('user')->where('mobile', $pid)->value('pid');
                //var_dump( $ppid);die;
                $data = [
                    //获取登录手机号
                    'mobile' => $mobile,
                    //获取推荐人
                    'pid' => $pid,
                    'ppid' => $ppid,
                    'logintime' => time(),
                    'lasttime'=>time(),
                    'head'=>'http://api.qingshishenghuo.shop/uploads/healthy/20200416/868f3fb42c021d17ba19249a603e2a46.jpg',
                      'nickname'=>get_username(),
                ];
                //var_dump(  $data);die;
                $res = Db::name('user')->insert($data);
                if ($res) {
                    return json(['status' => 1,]);
                } else {
                    return json(['status' => 0]);
                }
            }else{
                $res1=Db::name('user')->where('mobile',$mobile)->update(['lasttime'=>time()]);
                if ($res1) {
                    return json(['status' => 1]);
                } else {
                    return json(['status' => 0]);
                }
            }
        }else{
            if (!empty($one)) {
                $newone=suohao($one);
                return json(['status'=> 0, 'info' => '1', 'msgmobile' => $newone]);
            }else{
                return json(['status' =>1]);
            }
        }

    }
    //推荐人手机号
    public  function  activemobile(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $pid=input('pid');
            $is_star=Db::name('user')->where('mobile',$pid)->value('is_star');
            if( $is_star !=0){
                 return json(['status' =>1,'info'=>$is_star]);
            }else{
                return json(['status'=>0]);
            }
        }
    }
}
