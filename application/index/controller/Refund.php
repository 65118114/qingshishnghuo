<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\validate;

class Refund extends Controller
{
    // 申请退款
    public function addrefund(Request $request)
    {
        if ($request->isPost()) {
            $file = input('image');
            $newfile = substr($file, 0, -3);
            $order_id = input('order_id');
            $refundinfo = Db::name('refund')->where('order_id', $order_id)->find();
            if ($refundinfo) {
                return json(['status' => 0,'msg' => '订单已申请过退款,请联系客服处理!']);
            }
            $orderinfo = Db::name('order')->field('goods_id,order_status,attr_id,address_id')->where('order_id', $order_id)->find();
            if($orderinfo['goods_id'] ==122){
             return json(['status' => 0,'msg' => '虚拟商品不可退款!']);
            }
            $mobile = input('mobile');
            $user_id = Db::name('user')->where('mobile', $mobile)->value('user_id');
            $data = [
                'image' => $newfile,
                'order_id' => $order_id,
                'attr_id' => $orderinfo['attr_id'],
                'address_id' => $orderinfo['address_id'],
                'type' => input('type'),
                'user_id' => $user_id,
                'reason' => input('reason'),
                'remark' => input('remark'),
                'goods_id' => $orderinfo['goods_id'],
                'create_time' => time(),
                'first_status' => $orderinfo['order_status'],
            ];
            $refund_id = Db::name('refund')->insert($data,0,1);
            if ( $refund_id) {
                Db::name('order')->where('order_id', $order_id)->update(['order_status' => 6]);
                return json(['status' => 1, 'msg' => '申请成功,请等到系统处理','refund_id'=>$refund_id]);
            } else {
                return json(['status' => 0, 'msg' => '申请失败,请联系客服处理']);
            }
        }
    }

//退款详情
    public function refunddetail()
    {
        $refund_id = input('refund_id');
        $refundone = Db::name('refund')->field('exp_no,exp_name,type,refund_status,create_time,reason,attr_id,goods_id,order_id,is_agree')->where('refund_id', $refund_id)->find();
        $goods_gallery_1 = Db::name('goods_gallery')->where('goods_id', $refundone['goods_id'])->value('goods_gallery_1');
        $goods_name = Db::name('goods')->where('goods_id', $refundone['goods_id'])->value('goods_name');
        $attr_specs = Db::name('goods_attr')->where('attr_id', $refundone['attr_id'])->value('attr_specs');
        $orderinfo = Db::name('order')->field('num,price,trade_no')->where('order_id', $refundone['order_id'])->find();
        $refundone['goods_gallery_1'] = $goods_gallery_1;
        $refundone['goods_name'] = $goods_name;
        $refundone['attr_specs'] = $attr_specs;
        $refundone['num'] = $orderinfo['num'];
        $refundone['price'] = $orderinfo['price'];
        $refundone['trade_no'] = $orderinfo['trade_no'];
        return json($refundone);
    }

//取消售后
    public function cancel(Request $request)
    {
        if ($request->isPost()) {
            $refund_id = input('refund_id');
            $refundinfo = Db::name('refund')->where('refund_id', $refund_id)->find();
            $res = Db::name('order')->where('order_id', $refundinfo['order_id'])->update(['order_status' => $refundinfo['first_status']]);
            if ($res) {
                Db::name('refund')->where('refund_id', $refund_id)->delete();
                return json(['status' => 1, 'msg' => '取消成功,请等到系统处理']);
            }
        }
    }
   //填写物流信息
    public  function  addexp(Request $request){
        if ($request->isPost()) {
            $refund_id = input('refund_id');
            $exp_name = input('exp_name');
            $exp_no = input('exp_no');
            $sendmobile = input('sendmobile');
            $res = Db::name('refund')->where('refund_id', $refund_id)->update(['exp_name' => $exp_name,'exp_no'=>$exp_no,'sendmobile'=>$sendmobile]);
            if($res){
                return json(['status' => 1, 'msg' => '提交成功成功,请等到系统处理']);
            }
        }

    }
    // 处理图片上传
    public function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $file = $request->file('file');
            //  var_dump( $file);die;
            $validate = config('VALIDATE');
            $path = config('ASSESS_PATH');
            // var_dump($validate['ext'].'==='. $path);die;
            // var_dump($file->validate($validate));die;
            $res = $file->validate($validate)->move($path);
            //  var_dump(1);die;
            if ($res) {
                //var_dump( $res);die;
                $file_path = $res->getPathname();
                return json(['status' => 1, 'info' => config('server_name') . '/' . $file_path . '===']);
            } else {
                return json(['status' => 0, 'info' => $file->getError()]);
            }
        }
    }
}