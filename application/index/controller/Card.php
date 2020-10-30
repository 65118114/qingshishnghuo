<?php
 namespace app\index\controller;
 use think\Config;
 use think\Controller;
 use think\Db;
 use think\Request;

 class Card {


     //优惠券展示
      public  function  index(){
          header("Access-Control-Allow-Origin:*");
          header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
          header('Access-Control-Allow-Headers:x-requested-with, content-type');

      }
     //领取优惠券
     public  function  getcard(Request $request){
         header("Access-Control-Allow-Origin:*");
         header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
         header('Access-Control-Allow-Headers:x-requested-with, content-type');
         $mobile=input('mobile');
         $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
         if ($request->isPost()) {
             $code =input('code');
            // var_dump(  $result);die;
             //领取时间  受否使用is_use  到期时间
            $num = Db::name('card_log')->where('pidcode',$code)->where('user_id', $user_id)->count('card_log_id');
             $limitnum = Db::name('card')->where('code',$code)->value('limit_num');
             $newlimitnum=intval($limitnum);
           //  var_dump(  $newlimitnum  );die;
             if($num >=$newlimitnum){
                 return json(['status'=>0,'msg'=>'领取数量已经达到最大值']);
             }
             $data = [
                 'get_time'=>time(),
                 'endtime' => input('endtime'),
                 'starttime' => input('starttime'),
                 'code' =>  $code.mt_rand(10000,99999),
                 'pidcode' => $code,
                 'user_id'=>$user_id,
                 'card_door'=>input('card_door'),
                 'card_price'=>input('card_price'),
             ];
             $res = Db::name('card_log')->insert($data);
             if($res){
                 $result = Db::name('card')->where('code',$code)->setDec('card_num',1);
                 if($result){
                     return json(['status'=>1,'msg'=>'领取成功']);
                 }else{
                     return json(['status'=>0,'msg'=>'优惠券数量扣除失败']);
                 }
             }else{
                 return json(['status'=>0,'msg'=>'领取失败']);
             }
         }else{
              $list=Db::name('card')->where('card_num','<>',0)->select();
             foreach ($list as $k => $v) {
                 $num = Db::name('card_log')->where('pidcode',$v['code'])->where('user_id', $user_id)->find();
                 if(empty($num)){
                     $list[$k]['is_get']=0;
                 }else{
                     $list[$k]['is_get']=1;
                 }
              //   $list['limit_num'];
                 $get_num= Db::name('card_log')->where('pidcode',$v['code'])->where('user_id', $user_id)->count('card_log_id');
                 $list[$k]['get_num']=$get_num;
             }
              return json( $list);
         }
     }
     //用户所拥的券
     public  function  usercard(Request $request){
         header("Access-Control-Allow-Origin:*");
         header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
         header('Access-Control-Allow-Headers:x-requested-with, content-type');
         $mobile=input('mobile');
         $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
         $time = time();
         $all_status=input('all_status');
         if($all_status ==1) {
             $list = Db::name('card_log')->field('card_log_id,card_door,card_price,starttime,endtime')->where('user_id', $user_id)->select();
             if (empty($list)) {
                 return json(['status' => 0,]);
             }
             foreach ($list as $k => $v) {
                 if ($v['endtime'] < $time) {
                  //  var_dump($v['endtime']);die;
                     $list[$k]['card_status'] = 2;
                 } else {
                     if ($v['is_use'] == 0) {
                         $list[$k]['card_status'] = 0;
                     } else {
                         $list[$k]['card_status'] = 1;
                     }
                 }
             }
         }elseif ($all_status ==2 ){
             $list = Db::name('card_log')->field('card_log_id,card_door,card_price,starttime,endtime')->where('user_id', $user_id)->where('is_use',1)->select();
             if (empty($list)) {
                 return json(['status' => 0, ]);
             }
         }elseif ($all_status ==3){
             $list = Db::name('card_log')->field('card_log_id,card_door,card_price,starttime,endtime')->where('user_id', $user_id)->where('endtime','<',$time)->select();
             if (empty($list)) {
                 return json(['status' => 0]);
             }
         }
         return json(['status' => 1,'msg' =>$list]);
     }
    //用户可用优惠券
     public  function  usablecard(){
         $mobile=input('mobile');
         $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
         $time=time();
         $list=Db::name('card_log')->field('card_log_id,card_door,card_price')->where('is_use',0)->where('endtime','>',$time)->where('user_id', $user_id)->select();
         //var_dump($list);die;
         return json( $list);
     }
 }