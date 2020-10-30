<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;

class Notice extends Controller
{

    //通知首页
    public function index()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:get'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
//
//        $order = 'noti_id desc';
//        $list = Db::name('notice')->order($order)->select();
//        return json($list);


            $list = Db::name('notice')->order('noti_id desc')->select();
//  var_dump($list);die;
//            $a='';
//            foreach ( $list  as $k=>$v){
//                $a=   htmlspecialchars_decode($list[$k]['content']);
//                $list[$k]['htmlcontent'] = $a;
//            }
           return json( $list);
           //  var_dump( htmlspecialchars_decode($list[0]['content']));die;
           // return json(htmlspecialchars_decode($list[0]['content']) );

    }
  //通知详情
    public  function  notidetail(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:get'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $noti_id=input('noti_id');
        //var_dump($noti_id);die;
        $list = Db::name('notice')->where('noti_id',$noti_id)->find();
        return json([$list]);
    }
}