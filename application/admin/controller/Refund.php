<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/25
 * Time: 11:27
 */

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;

class Refund extends Common
{
    public function __construct()
    {
        parent::__construct();

    }

    //退款首页
    public function index()
    {
        /*
                if ($this->request->isPost()) {
                    $mobile = input('post.mobile', 'n');
                    $status = input('post.status', '-1');
                    $level = input('post.level', '-1');
                } else {
                    $mobile = input('mobile', 'n');
                    $status = input('post.status', '-1');
                    $level = input('level', '-1');
                }
                $where = array();
                // $where['field']=array('id','remark','status');
                //查询会员名称
                if ($mobile != 'n' && $mobile != '') {
                    $where['mobile'] = $mobile;
                }

                //开启状态
                if ( $status > -1) {
                    $where['status'] = $status;
                }
                //等级
                if ( $level > -1) {
                    $where['level'] = $level;
                }*/
        //var_dump($status);
     

        $list = Db::name('refund')->order('refund_id  desc')->paginate(10, false, ['query' => request()->param()]);
        foreach ($list as $k => $v) {
            $goods = Db::name('goods')->where(array('goods_id' => $v['goods_id']))->find();
            $orderinfo = Db::name('order')->where(array('order_id' => $v['order_id']))->find();
       //   var_dump( $orderinfo);die;
            $gallery = Db::name('goods_gallery')->where(array('goods_id' => $v['goods_id']))->find();
            $attr = Db::name('goods_attr')->where(array('attr_id' => $v['attr_id']))->find();
            $userinfo = Db::name('user')->where(array('user_id' => $v['user_id']))->find();
            if (!empty($gallery) && !empty($attr)) {
                $list[$k] = array_merge($list[$k], $goods, $attr, $gallery, $userinfo,$orderinfo);
            }

        }
        //echo Db::name('role')->getLastSql();  exit;
//var_dump($list);die;
        $count = $list->total();
        $this->assign('list', $list);
        //$this->assign('mobile',$mobile);
        //    $this->assign('status', $status);
        $this->assign('count', $count);
        //   $this->assign('level', $level);
        //var_dump($class_status);die;
        return $this->fetch();

    }

    //确定退款
    public function sendadd()
    {
        $order_id = input('order_id');
        $price=input('price');
        $mobile = input('mobile');
        $type = input('type');
        $refund_id=input('refund_id');
        //var_dump( $type);die;
        if( $type == 1){
            $res=Db::name('user')->where('mobile',$mobile)->setInc('money',$price);
            if($res){
                //修改订单状态和退货状态
                $res1=Db::name('order')->where('order_id',$order_id)->update(['order_status'=>7]);
                $res2=Db::name('refund')->where('refund_id',$refund_id)->update(['refund_status'=>1]);
                //删除冻结表
                $res3=Db::name('user_freeze_log')->where('order_id',$order_id)->delete();
            }
            if(  $res && $res1 && $res2 && $res3){
                $this->success('同意退款成功','Refund/index',3);
            }else{
                $this->error('退款失败，请联系管理员');
            }
        }
        if( $type == 2){
        //    $address=Db::name('refund')->field('address')->where('order_id',$order_id)->find();
          //  var_dump( $address);die;
//            if($address ==null){
//             return   $this->error('退货地址不能为空','Refund/index',3);
//            }
            $res=Db::name('refund')->where('order_id',$order_id)->update(['is_agree'=>2]);
            if($res){
                $this->success('同意退款成功','Refund/index',3);
            }else{
                $this->error('系统错误,请联系客服');
            }

        }
    }
      //拒绝退款 订单状态改为退款前的状态
    public function refuse()
    {
        $order_id = input('order_id');
        //需要把订单状态改为以前的状态
        $first_status = Db::name('refund')->where('order_id', $order_id )->value('first_status') ;
        $res = Db::name('order')->where('order_id', $order_id )->update(['order_status'=>$first_status]);
        $res = Db::name('refund')->where('order_id', $order_id )->update(['refund_status'=>2]) ;

        if ($res) {
            $this->success('拒绝成功', 'Refund/index', 3);
        } else {
            $this->error('退款失败，请联系管理员');
        }
    }
    //同意退款
    public  function  agree(){
        $order_id = input('order_id');
        $price=input('price');
        $mobile = input('mobile');
        $refund_id=input('refund_id');
        $res=Db::name('user')->where('mobile',$mobile)->setInc('money',$price);
        if($res){
            //修改订单状态和退货状态
            $res1=Db::name('order')->where('order_id',$order_id)->update(['order_status'=>7]);
            $res2=Db::name('refund')->where('refund_id',$refund_id)->update(['refund_status'=>1]);
           //删除冻结表
            $res3=Db::name('user_freeze_log')->where('order_id',$order_id)->delete();
        }
        if(  $res && $res1 && $res2 && $res3){
            $this->success('同意退款成功','Refund/index',3);
        }else{
            $this->error('退款失败，请联系管理员');
        }
    }
   

    //查看物流信息
    public function queryExp(Request $request)
    {
        $exp_name = $request->param('exp_name');
        // var_dump($exp_name );die;
        $exp_no = $request->param('exp_no');
        $url = 'http://v.juhe.cn/exp/index?key=e923bc79255343f04e323e86bc38863f&com='.$exp_name .'&no='.$exp_no;
        $json = file_get_contents($url);
        // file_put_contents('exp.txt', $json);
        //  $json = file_get_contents('exp.txt');

        $data = json_decode($json, true);
        //var_dump($data);die;
        if ($data['error_code'] == 0) {

            $html = '<div class="delivery-detail"> <div class="inner"> <p>物流公司：' . $data['result']['company'] . '</p><p>物流单号:' . $data['result']['no'] . '</p><ul> ';

            foreach (array_reverse($data['result']['list']) as $v) {

                $html .= '<li class="newest"> <p>' . $v['remark'] . '</p>  <p>' . $v['datetime'] . '</p>     </li> ';

            }

            $html .= '</ul></div></div>';

            return json(['status' => 'success', 'html' => $html]);

        }

    }
    //添加仓库地址
    public function address()
    {
        $order_id = input('order_id');
        // var_dump($order_id);die;
        if ($this->request->isPost()) {
            $data = [
                'address' => input('address'),
            ];
            //var_dump(  $data);die;
            $res = Db::name('refund')->where('order_id', $order_id)->update($data);
            if ($res) {
                $this->success('添加地址成功', 'Order/index', 3);
            } else {
                $this->error('添加失败,请联系系统管理员 ');
            }
        } else {
            $list = Db::name('refund')->field('address')->where('order_id', $order_id)->find();
            $this->assign('list',$list);
            return $this->fetch();
        }
    }

}