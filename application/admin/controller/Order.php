<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\validate;
use kuaidi\kuaidi;
use think\db\Where;

class Order extends Common
{

    //订单首页
    public function index(Request $request)
    {
        if($request->isPost()){
            $goods_name = input('post.goods_name', 'n', 'strip_tags,urldecode');
            $order_status = input('post.order_status', -1, 'intval');
            $create_time_start = input('post.create_time_start', 'n', 'strip_tags');
            $create_time_end = input('post.create_time_end', 'n', 'strip_tags');
            $send_time_start = input('post.send_time_start', 'n', 'strip_tags');
            $send_time_end = input('post.send_time_end', 'n', 'strip_tags');
            $pay_type = input('post.pay_type', -1, 'strip_tags');
            $mobile = input('post.mobile', 'n', 'strip_tags,urldecode');
            //     var_dump($create_time_start);die;

        }else{
            $goods_name = input('goods_name', 'n', 'strip_tags,urldecode');
            $order_status = input('order_status', -1, 'intval');
            $create_time_start = input('send_time_start', 'n', 'strip_tags');
            $create_time_end = input('create_time_end', 'n', 'strip_tags');
            $send_time_start = input('send_time_start', 'n', 'strip_tags');
            $send_time_end = input('send_time_end', 'n', 'strip_tags');
             $pay_type = input('pay_type', -1, 'strip_tags');
            $mobile = input('mobile', 'n', 'strip_tags,urldecode');

        }
        // var_dump(   $create_time_start);die;
        //  $where = array();

        $where = new Where();
        // 下单时间
        if ($create_time_start != "n" && $create_time_start != "") {
            $create_time_start = strtotime($create_time_start ." 00:00:00");

        }
        if ($create_time_end != "n" && $create_time_end != "") {
            $create_time_end = strtotime($create_time_end . " 23:59:59");
        }
        if (isset( $create_time_start) &&  $create_time_start > 0 && isset(  $create_time_end) &&   $create_time_end > 0) {

            $where['create_time'] = array( 'between', array( $create_time_start,$create_time_end ));
            //  var_dump($where['create_time']);
        }

        //
        if ($send_time_start != "n" && $send_time_start != "") {
            $send_time_start = strtotime($send_time_start ." 00:00:00");

        }
        if ($send_time_end != "n" && $send_time_end != "") {
            $send_time_end = strtotime($send_time_end . " 23:59:59");
        }
        if (isset( $send_time_start) &&  $send_time_start > 0 && isset(  $send_time_end) &&   $send_time_end > 0) {

            $where['send_time'] = array( 'between', array( $send_time_start,$send_time_end ));
            //  var_dump($where['create_time']);
        }



        if ($goods_name != 'n' && $goods_name != '') {
            $goods_id = Db::name('goods')->where('goods_name','like', "%" . $goods_name . "%")->field('goods_id')->select();

            $a='';
            foreach($goods_id as $k=>$v){
                $a.=$v['goods_id'].'==';

            }
            $a =array_filter(explode('==',$a));

            $where['goods_id'] = array('in',$a);
        }
        if ($mobile != 'n' && $mobile != '') {
          $user_id = Db::name('user')->where('mobile','like', "%" . $mobile . "%")->value('user_id');
            // var_dump(  $goods_id);die;
            $where['user_id'] = $user_id;
        }
        //订单状态
        if ($order_status > -1) {
            $where['order_status'] = $order_status;

        }
         if ($pay_type > -1) {
            $where['pay_type'] = $pay_type;

        }
        $list = Db::name('order')->where($where)->order('order_id desc')->paginate(10,false,['query'=>request()->param()]);

        foreach ($list as $k => $v) {
            $goods = Db::name('goods')->where(array('goods_id' => $v['goods_id']))->find();

            $address = Db::name('address')->where(array('address_id' => $v['address_id']))->find();

            $gallery = Db::name('goods_gallery')->where(array('goods_id' => $v['goods_id']))->find();
            $attr = Db::name('goods_attr')->where(array('attr_id' => $v['attr_id']))->find();
            $userinfo = Db::name('user')->field('mobile')->where(array('user_id' => $v['user_id']))->find();
          //  var_dump($userinfo);die;
            if(!empty($gallery)&&!empty($attr)){
                $list[$k] = array_merge($list[$k], $goods,$address,$attr, $gallery,$userinfo);
            }

        }
        $count = $list->total();
        $this->assign('create_time_start', $create_time_start);
        $this->assign('create_time_end', $create_time_end);
        $this->assign('send_time_start', $send_time_start);
        $this->assign('send_time_end', $send_time_end);
        $this->assign('order_status', $order_status);
        $this->assign('pay_type', $pay_type);
        $this->assign('mobile', $mobile);
        $this->assign('count', $count);
        $this->assign('goods_name', $goods_name);
        $this->assign('list', $list);

        return $this->fetch();
    }
    //订单详情
    public function OrderDetail()
    {
        $order_id = input('order_id');
        $list = Db::name('order')->where('order_id', $order_id)->find();
        $address = Db::name('address')->where(array('address_id' => $list['address_id']))->find();
        $goods = Db::name('goods')->where(array('goods_id' => $list['goods_id']))->find();
        $goods_gallery = Db::name('goods_gallery')->where(array('goods_id' => $list['goods_id']))->find();
        $attr = Db::name('goods_attr')->where(array('attr_id' => $list['attr_id']))->find();
        $list = array_merge($list, $address, $goods, $goods_gallery, $attr);
        // var_dump(    $list);die;
        $this->assign('order_one', $list);
        return $this->fetch();

    }

    //物流信息
    public function express()
    {
        $order_id = input('order_id');
        if ($this->request->isPost()) {
            $data = [
                'exp_no' => input('exp_no'),
                'exp_name' => input('exp_name'),
                'send_time' =>time(),
            ];
            //  var_dump(  $data);die;
            $res = Db::name('order')->where('order_id', $order_id)->update($data);
            if ($res) {
                Db::name('order')->where('order_id', $order_id)->update(['order_status' => 2]);
                $this->success('发货成功', 'Order/index', 3);
            } else {
                $this->error('发货失败,请联系系统管理员 ');
            }
        } else {
            $list = Db::name('order')->where('order_id', $order_id)->find();
            $addlist = Db::name('address')->where('address_id', $list['address_id'])->find();
            $list = array_merge($list, $addlist);
            $this->assign('list', $list);
            return $this->fetch();

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
    //导出
    public  function  export(Request $request){
       // var_dump( 111);die;
        if($request->isPost()){
            $goods_name = input('post.goods_name', 'n', 'strip_tags,urldecode');
            $order_status = input('post.order_status', -1, 'intval');
            $create_time_start = input('post.create_time_start', 'n', 'strip_tags');
            $create_time_end = input('post.create_time_end', 'n', 'strip_tags');
            $pay_type = input('post.pay_type', -1, 'strip_tags');
            //     var_dump($create_time_start);die;

        }else{
            $goods_name = input('goods_name', 'n', 'strip_tags,urldecode');
            $order_status = input('order_status', -1, 'intval');
            $create_time_start = input('create_time_start', 'n', 'strip_tags');
            $create_time_end = input('create_time_end', 'n', 'strip_tags');
            $pay_type = input('pay_type', -1, 'strip_tags');

        }
//var_dump($order_status);die;
        $where = new Where();
        // 下单时间
        if ($create_time_start != "n" && $create_time_start != "") {
            $create_time_start = strtotime($create_time_start ." 00:00:00");

        }
        if ($create_time_end != "n" && $create_time_end != "") {
            $create_time_end = strtotime($create_time_end . " 23:59:59");
        }
        if (isset( $create_time_start) &&  $create_time_start > 0 && isset(  $create_time_end) &&   $create_time_end > 0) {

            $where['create_time'] = array( 'between', array( $create_time_start,$create_time_end ));
            //var_dump($where['create_time']);
        }

        if ($goods_name != 'n' && $goods_name != '') {
            $goods_id = Db::name('goods')->where('goods_name','like', "%" . $goods_name . "%")->field('goods_id')->select();

            $a='';
            foreach($goods_id as $k=>$v){
                $a.=$v['goods_id'].'==';

            }
            $a =array_filter(explode('==',$a));

            $where['goods_id'] = array('in',$a);
        }
        //订单状态
        if ($order_status > -1) {
            $where['order_status'] = $order_status;
        }
        if ($pay_type > -1) {
            $where['pay_type'] = $pay_type;
        }

        $list = Db('order')->where($where)->order('order_id desc')->select();

        foreach ($list as $k => $v) {
            $goods = Db::name('goods')->where(array('goods_id' => $v['goods_id']))->find();
            // var_dump($goods);
            $address = Db::name('address')->where(array('address_id' => $v['address_id']))->find();
            $attr = Db::name('goods_attr')->where(array('attr_id' => $v['attr_id']))->find();
            $userinfo = Db::name('user')->where(array('user_id' => $v['user_id']))->find();
            $list[$k]['buymobile']=$userinfo['mobile'];
            //  var_dump($list);die;
            if(!empty($address)&&!empty( $attr)){
                $list[$k] = array_merge($list[$k],$goods,$address, $attr);
            }
        }

        exp_excel($width = 1000,13,['订单号','商品','单价','规格','数量','收货信息','实付金额','支付方式','下单时间','订单状态','物流公司','单号','买家账号'], $list, $title = '订单数据');


    }
//客服导出
    public  function  kefuexport(Request $request){
        if($request->isPost()){
            $goods_name = input('post.goods_name', 'n', 'strip_tags,urldecode');
            $order_status = input('post.order_status', -1, 'intval');
            $create_time_start = input('post.create_time_start', 'n', 'strip_tags');
            $create_time_end = input('post.create_time_end', 'n', 'strip_tags');
            //     var_dump($create_time_start);die;

        }else{
            $goods_name = input('goods_name', 'n', 'strip_tags,urldecode');
            $order_status = input('order_status', -1, 'intval');
            $create_time_start = input('create_time_start', 'n', 'strip_tags');
            $create_time_end = input('create_time_end', 'n', 'strip_tags');

        }
//var_dump($order_status);die;
        $where = new Where();
        // 下单时间
        if ($create_time_start != "n" && $create_time_start != "") {
            $create_time_start = strtotime($create_time_start ." 00:00:00");

        }
        if ($create_time_end != "n" && $create_time_end != "") {
            $create_time_end = strtotime($create_time_end . " 23:59:59");
        }
        if (isset( $create_time_start) &&  $create_time_start > 0 && isset(  $create_time_end) &&   $create_time_end > 0) {

            $where['create_time'] = array( 'between', array( $create_time_start,$create_time_end ));
            //var_dump($where['create_time']);
        }

        if ($goods_name != 'n' && $goods_name != '') {
            $goods_id = Db::name('goods')->where('goods_name','like', "%" . $goods_name . "%")->value('goods_id');
            // var_dump(  $goods_id);die;
            $where['goods_id '] =  $goods_id;
        }
        //订单状态
        if ($order_status > -1) {
            $where['order_status'] = $order_status;
        }
        // var_dump( $where['order_status']);die;
        $list = Db('order')->where($where)->order('order_id desc')->select();
        foreach ($list as $k => $v) {
            $goods = Db::name('goods')->where(array('goods_id' => $v['goods_id']))->find();
            // var_dump($goods);
            $address = Db::name('address')->where(array('address_id' => $v['address_id']))->find();
            $attr = Db::name('goods_attr')->where(array('attr_id' => $v['attr_id']))->find();
            $userinfo = Db::name('user')->where(array('user_id' => $v['user_id']))->find();
            $list[$k]['buymobile']=$userinfo['mobile'];
            //  var_dump($list);die;
            if(!empty($address)&&!empty( $attr)){
                $list[$k] = array_merge($list[$k],$goods,$address, $attr);
            }
        }
//var_dump( $list);die;
        kefu_exp_excel($width = 1000,30, ['订单号','下单时间','买家账号','支付单号','付款方式','付款时间','订单备注','买家留言','运费','应付金额',
            '收货人名字','手机号','固话', '省份','城市','区县','地址','邮编','线上商品编码','商品编码',
            '商品','颜色及规格','数量','商品单价','快递公司','单号','发货时间','状态','业务员','便签|线下备注'], $list, $title = '订单数据');
    }

//修改订单
    public function editOrder()
    {
        $order_id = input('order_id');
        return '待开发';
    }
    //备注信息
    public function adminremark()
    {
        $order_id = input('order_id');
        // var_dump($order_id);die;
        if ($this->request->isPost()) {
            $data = [
                'adminremark' => input('adminremark'),

            ];
            //var_dump(  $data);die;
            $res = Db::name('order')->where('order_id', $order_id)->update($data);
            if ($res) {
                $this->success('修改成功', 'Order/index', 3);
            } else {
                $this->error('修改失败,请联系系统管理员 ');
            }
        } else {
            $list = Db::name('order')->field('adminremark')->where('order_id', $order_id)->find();
            $this->assign('list', $list);
            return $this->fetch();
        }
    }
}