<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
class Userorder
{
    //接受评价数据入库
    public function addassess(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $file= input('image');
            $newfile=substr( $file,0,-3);
            $mobile= input('mobile');
            $head=Db::name('user')->where('mobile',$mobile)->value('head');
            $data = [
                'mobile' => input('mobile'),
                'content' => input('content'),
                'create_time' => time(),
                'goods_id' => input('goods_id'),
                'file'=>$newfile,
                'order_id' => input('order_id'),
                'head' =>  $head,
            ];
  //   var_dump($newfile);die;
            $result=Db::name('order')->where('order_id',$data['order_id'])->update(['order_status'=>4]);

            $res= Db::name('assess')->insert($data);
            if ($res && $result) {
                return json(['status' => 1]);
            } else {
                return json(['status' => 0]);
            }
        }

    }

    //展示最新评价
    public function newassess(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $goods_id = input('goods_id');
        $list = Db::name('assess')->order('assess_id desc')->where('goods_id', $goods_id)->find();
        $count = Db::name('assess')->where('goods_id', $goods_id)->count('assess_id');
        //var_dump(  $list);die;
        if(empty($list)){
            return json(['status'=>0]);
        }
        $list['image'] = explode('===', $list['file']);
        $list['mobile'] = suohao($list['mobile']);
        $list['count'] = $count;
        return json($list);
    }

    //展示所有评价
    public function showassess(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $goods_id = input('goods_id');
        $list = Db::name('assess')->where('goods_id', $goods_id)->select();
        $count = Db::name('assess')->where('goods_id', $goods_id)->count('assess_id');
        foreach ($list as $k => $v) {
            $list[$k]['image'] = explode('===', $v['file']);
            $list[$k]['mobile'] = suohao($list[$k]['mobile']);
            $list[$k]['count'] = $count;
        }
        return json($list);
    }

    //展示订单
    public function showorder()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $order_status = input('order_status');
        if($order_status > -1){
           $where['order_status']= $order_status;
        }
       // var_dump( $where['order_status']);die;
        $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
        $list=Db::name('order')->order('order_id desc')->where($where)->where('user_id', $user_id)->select();
     // $list=Db::name('order')->where('user_id', $user_id)->select();
        foreach ($list as $k=>$v){
            $goodsinfo=Db::name('goods')->where('goods_id',$v['goods_id'])->find();
           $goods_gallery_one=Db::name('goods_gallery')->where('goods_id',$v['goods_id'])->value('goods_gallery_1');
            $goods_attr=Db::name('goods_attr')->where('attr_id',$v['attr_id'])->find();
            $list[$k]['goods_name']=  $goodsinfo['goods_name'];
            $list[$k]['attr_specs']=   $goods_attr['attr_specs'];
            $list[$k]['attr_price']=   $goods_attr['ppro_price'];
            $list[$k]['goods_gallery_one']=   $goods_gallery_one;

                $refundinfo=Db::name('refund')->where('order_id',$v['order_id'])->find();
                if(!empty($refundinfo)){
                    $list[$k]['refund_status']=$refundinfo['refund_status'];
                    $list[$k]['type']=$refundinfo['type'];
                    $list[$k]['refund_id']=$refundinfo['refund_id'];
                     $list[$k]['is_agree']=$refundinfo['is_agree'];
                }
          }

       return json($list);
    }
    //所有订单
    public function allshoworder()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        // var_dump( $where['order_status']);die;
        $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
        $list=Db::name('order')->order('order_id desc')->where('order_status','<>','6')->where('order_status','<>','7')->where('user_id', $user_id)->select();
        foreach ($list as $k=>$v){
            $goodsinfo=Db::name('goods')->where('goods_id',$v['goods_id'])->find();
            $goods_gallery_one=Db::name('goods_gallery')->where('goods_id',$v['goods_id'])->value('goods_gallery_1');
            $goods_attr=Db::name('goods_attr')->where('attr_id',$v['attr_id'])->find();
            $list[$k]['goods_name']=  $goodsinfo['goods_name'];
            $list[$k]['attr_specs']=   $goods_attr['attr_specs'];
            $list[$k]['attr_price']=   $goods_attr['ppro_price'];
            $list[$k]['goods_gallery_one']=   $goods_gallery_one;

            $refundinfo=Db::name('refund')->where('order_id',$v['order_id'])->find();
            if(!empty($refundinfo)){
                $list[$k]['refund_status']=$refundinfo['refund_status'];
                $list[$k]['type']=$refundinfo['type'];
                $list[$k]['refund_id']=$refundinfo['refund_id'];
            }
        }

        return json($list);
    }
    //订单详情
    public function orderdetail()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $order_id=input('order_id');
        $orderlist=Db::name('order')->where('order_id', $order_id)->find();
        $is_star=Db::name('user')->where('user_id', $orderlist['user_id'])->value('is_star');
        $orderlist['is_star']=$is_star;

        $addresslist=Db::name('address')->where('address_id', $orderlist['address_id'])->find();
        $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' =>  $orderlist['goods_id']))->find();
        $goods_name = Db::name('goods')->field('goods_name')->where(array('goods_id' =>  $orderlist['goods_id']))->find();
        $goods_attr = Db::name('goods_attr')->field('ooriginal_price,ppro_price,attr_specs')->where(array('attr_id' =>  $orderlist['attr_id']))->find();
        $goodslist=Db::name('goods')->where('rand()')->field('goods_id,goods_name,sale,pro_price')->limit(2)->select();
        foreach ( $goodslist as $k=>$v){
            $goods_gallery_1= Db::name('goods_gallery')->where(array('goods_id' => $v['goods_id']))->value('goods_gallery_1');
            $goodslist[$k]['goods_gallery_1']=$goods_gallery_1;
        }
        $list=array_merge( $orderlist,$addresslist,$goods_gallery_one,$goods_name,$goods_attr,$goodslist);
        return json($list);
    }
    //随机推荐2个商品
     public  function  randgoods(){
         header("Access-Control-Allow-Origin:*");
         header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
         header('Access-Control-Allow-Headers:x-requested-with, content-type');
         $mobile = input('mobile');
         $goodslist=Db::name('goods')->where('rand()')->where('goods_type',1)->field('goods_id,goods_name,sale,pro_price')->limit(2)->select();
         $is_star= Db::name('user')->where('mobile',$mobile)->value('is_star');
         foreach ( $goodslist as $k=>$v){
             $goods_gallery_1= Db::name('goods_gallery')->where(array('goods_id' => $v['goods_id']))->value('goods_gallery_1');
             $goodslist[$k]['goods_gallery_1']=$goods_gallery_1;
             $goodslist[$k]['is_star']=$is_star;
         }
         return json($goodslist);
     }
//确认收货
public  function  queryorder(Request $request)  {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
    header('Access-Control-Allow-Headers:x-requested-with, content-type');
    $order_id = input('order_id');
     if($request->isPost()){
         $paypassword=md5(input('paypassword'));
         $mobile=input('mobile');
         $paypass=Db::name('user')->where('mobile',$mobile)->value('paypassword');
      //   var_dump($paypass);die;
         if($paypassword != $paypass){
             return json(['status'=>0,'info'=>'支付密码错误']);
         }
         $res=Db::name('order')->where('order_id', $order_id)->update(['order_status'=>3,'take_time'=>time()]);
         if( $res){
             return json(['status'=>1]);
         }else{
             return json(['status'=>0]);
         }
     }else{
        $mobile=input('mobile');
        $paypassword=Db::name('user')->where('mobile',$mobile)->value('paypassword');
         if(empty($paypassword)){
             return json(['status'=>0]);
         }else{
             return json(['status'=>1]);
         }
    }

}
//取消订单
public  function  cancelorder(Request $request){
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
    header('Access-Control-Allow-Headers:x-requested-with, content-type');
    if($request->isPost()){
        $order_id = input('order_id');
        $card_log_id=Db::name('order')->where('order_id', $order_id)->value('card_log_id');
        if(!empty($card_log_id)){
            Db::name('card_log')->where('card_log_id',$card_log_id)->update(['is_use' => 0]);
        }
        $res=Db::name('order')->where('order_id', $order_id)->update(['order_status'=>5,'over_pay_time'=>time()]);
        if( $res){
            return json(['status'=>1]);
        }else{
            return json(['status'=>0]);
        }
    }
}

//查看物流信息
    public function queryExp(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $exp_name = $request->param('exp_name');
        // var_dump($exp_name );die;
        $exp_no = $request->param('exp_no');
        $goods_id = $request->param('goods_id');
        $goods_gallery_1 = Db::name('goods_gallery')->where('goods_id',$goods_id)->value('goods_gallery_1');
        $url = 'http://v.juhe.cn/exp/index?key=e923bc79255343f04e323e86bc38863f&com='.$exp_name .'&no='.$exp_no;
        $json = file_get_contents($url);
        // file_put_contents('exp.txt', $json);
        //  $json = file_get_contents('exp.txt');
        $data = json_decode($json, true);

      //  if ($data['error_code'] == 0) {
/*            $html = '<div class="delivery-detail"> <div class="inner"> <p>物流公司：' . $data['result']['company'] . '</p><p>物流单号:' . $data['result']['no'] . '</p><ul> ';

            foreach ($data['result']['list'] as $v) {

                $html .= '<li class="newest"> <p>' . $v['remark'] . '</p>  <p>' . $v['datetime'] . '</p>     </li> ';

            }
            $html .= '</ul></div></div>';*/
              
         
            if($data['error_code'] !=0){
              return json(['status' => '1','goods_gallery_1'=> $goods_gallery_1]);
            }else{
                  return json(['status' => '1', 'content' => $data,'goods_gallery_1'=> $goods_gallery_1]);
            }
      //  }

    }
    // 处理图片上传
    public function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

     /*   $files = $request->file('file');
        // var_dump( $files);die;
        $validate = config('VALIDATE');
        $path = config('ASSESS_PATH');
        foreach ($files as $file) {
            $res = $file->validate($validate)->move($path);
            if ($res) {
                $file_path .=config('server_name').'/'.$res->getPathname() . '===';
                // return json(['status' => 1, 'info' => 'http://qingshi.natapp1.cc/' . '/' . $file_path]);
            } else {
                //  return json(['status'=>'fail','msg'=>$file->getError()]);
            }
        }
        return $file_path;*/
        if($request->isPost()){
            $file = $request->file('file');
            //  var_dump( $file);die;
            $validate=config('VALIDATE');
            $path = config('ASSESS_PATH');
            // var_dump($validate['ext'].'==='. $path);die;
            // var_dump($file->validate($validate));die;
            $res=$file->validate($validate)->move($path);
            //  var_dump(1);die;
            if($res){
                //var_dump(    $res);die;
                $file_path=$res->getPathname();
                return json(['status'=>1,'info'=>config('server_name').'/'.$file_path.'===']);
            }else{
                return json(['status'=>0,'info'=>$file->getError()]);
            }
        }
    }


}