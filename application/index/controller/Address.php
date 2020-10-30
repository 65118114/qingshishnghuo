<?php
 namespace app\index\controller;
 use think\Config;
 use think\Controller;
 use think\Db;
 use think\Request;

 class Address {


     //首页展示地址
      public  function  index(){
          header("Access-Control-Allow-Origin:*");
          header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
          header('Access-Control-Allow-Headers:x-requested-with, content-type');
          $mobile=input('mobile');
          //var_dump($mobile);die;
          $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
          $list=Db::name('address')->where('is_on',1)->where('status','<>',1)->where('user_id',$user_id)->select();
          $defa_list=Db::name('address')->where('status',1)->where('is_on',1)->where('user_id',$user_id)->select();
          if(!empty($defa_list)){
              $list=array_merge($defa_list,$list);
          }
         // var_dump( $list);die;
          return json( $list);
      }
     //新增地址
     public  function  address(Request $request){
         header("Access-Control-Allow-Origin:*");
         header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
         header('Access-Control-Allow-Headers:x-requested-with, content-type');
         $mobile=input('mobile');
         $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
         //$user_id=Db::name('address')->where('user_id', $user_id)->count('address_id');
         if ($request->isPost()) {
             $status = input('status');
             if($status ==1){
                 $result = Db::name('address')->where('user_id',$user_id)->update(['status' => '0']);
             }
            // var_dump(  $result);die;
             $data = [
                 'user_id' => $user_id,
                 'name' => input('name'),
                 'addmobile' => input('addmobile'),
                 'city' => input('city'),
                 'detailadd' => input('detailadd'),
                 'group' => input('group'),
                 'status' => input('status'),
             ];
             $address_id = Db::name('address')->insert($data,0,1);
             //$res=Db::name('')->insert($data);
             if($address_id){
                 return json(['status'=>1,'msg'=>'添加成功','address_id'=>$address_id ]);
             }else{
                 return json(['status'=>0,'msg'=>'添加失败']);
             }
         }

     }
     //修改地址
     public  function  editaddress(Request $request){
         header("Access-Control-Allow-Origin:*");
         header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
         header('Access-Control-Allow-Headers:x-requested-with, content-type');
         $address_id=input('address_id');
         $mobile=input('mobile');
         $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
         if ($request->isPost()) {
             $status = input('status');
             if($status ==1){
                 $result = Db::name('address')->where('user_id', $user_id)->update(['status' => '0']);
             }
             $data = [
                 'name' => input('name'),
                 'addmobile' => input('addmobile'),
                 'city' => input('city'),
                 'detailadd' => input('detailadd'),
                 'group' => input('group'),
                 'status' => input('status'),
             ];
           // var_dump( $address_id);die;
             $res = Db::name('address')->where('address_id', $address_id)->update($data);
             if($res){
                 return json(['status'=>1,'msg'=>'修改成功']);
             }else{
                 return json(['status'=>0,'msg'=>'修改失败']);
             }
         }else{
             $list = Db::name('address')->where('address_id', $address_id)->find();
             return json( $list);
         }

     }
     //修改默认地址

     public  function  editstatus(Request $request){
         header("Access-Control-Allow-Origin:*");
         header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
         header('Access-Control-Allow-Headers:x-requested-with, content-type');
         $address_id=input('address_id');
         $mobile=input('mobile');
         $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
         if ($request->isPost()) {
             $status = input('status');
             if($status ==1){
                 $result = Db::name('address')->where('user_id', $user_id)->update(['status' => '0']);
             }
             $data = [

                 'status' => input('status'),
             ];
             // var_dump( $address_id);die;
             $res = Db::name('address')->where('address_id', $address_id)->update($data);
             if($res){
                 return json(['status'=>1]);
             }else{
                 return json(['status'=>0]);
             }
         }else{
             $list = Db::name('address')->where('address_id', $address_id)->find();
             return json( $list);
         }

     }
       //删除用户地址
     public  function  deladdress(Request $request)
     {
         header("Access-Control-Allow-Origin:*");
         header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
         header('Access-Control-Allow-Headers:x-requested-with, content-type');

         if ($request->isPost()) {
             $address_id = input('address_id');
         //    var_dump($address_id );die;
             $res = Db::name('address')->where('address_id',$address_id)->update(['is_on'=>0]);
             if ($res) {
                 return json(['status' => 1,'msg'=>'删除成功']);
             } else {
                 return json(['status' => 0,'msg'=>'删除失败']);
             }
         }
     }
 }