<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Share extends Common {

    //订单分润首页
    public  function  index(){
        $list=Db::name('order')->whereNotIn('order_status',[0,5])->order('order_id desc')->paginate('15');
        // $list= $list->toArray()['data'];
        // var_dump( $list);die;
        foreach ( $list  as $k=>$v){
            $order_list=Db::name('order')->where('order_id',$v['order_id'])->find();
            $user_list=Db::name('user')->where('user_id',$v['user_id'])->find();
            // var_dump(  $user_list);die;
            //  $pidlist=Db::name('user')->where('pid', $user_list['pid'])->find();
            $goods_info=Db::name('goods')->where('goods_id', $order_list['goods_id'])->find();
            //$list[$k]['goods_name']= $goods_info;
            // $list['items'][$k]['trade_no'] =   $order_list['trade_no'];
            // $list[$k]['mobile'] =  $user_list['mobile'];
            //  $list[$k]['pid'] =  $user_list['pid'];
            $list[$k] =  array_merge( $list[$k],  $user_list, $goods_info,  $order_list);
        }
          //var_dump( $list);die;
        $count = $list->total();
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();
    }
    //分润详情
    public function shareDetail()
    {
        $order_id=input('order_id');
        $order_list=Db::name('order')->where('order_id',$order_id)->find();
//  var_dump( $order_list);die;
        $address_list=Db::name('address')->where('address_id',$order_list['address_id'])->find();
        $create_mobile=Db::name('user')->where('user_id',$order_list['user_id'])->value('mobile');
        $freeze_list=Db::name('user_freeze_log')->where('order_id', $order_id )->select();
        foreach ($freeze_list as $k=>$v ){
            $userone=Db::name('user')->where('user_id',$v['user_id'])->find();
            $freeze_list[$k]['mobile']=$userone['mobile'];
            $freeze_list[$k]['fashion_level']=$userone['fashion_level'];
            $freeze_list[$k]['take_time']= $order_list['take_time'];
        }

//var_dump($freeze_list);
        $this->assign('order_list',$order_list);
        $this->assign('create_mobile',$create_mobile);
        $this->assign('address_list',$address_list);
        $this->assign('freeze_list',$freeze_list);

        return $this->fetch();

    }
    //提现首页
    public  function  withdraw(){
        $withdraw_list=Db::name('withdraw')->order('withdraw_id desc')->select();
        foreach (  $withdraw_list as $k=>$v){
            $user_list=Db::name('user')->field('mobile,bankaddress,bankzh')->where('user_id',$v['user_id'])->find();
            $really_list=Db::name('really')->field('name')->where('user_id',$v['user_id'])->find();
            if( $user_list && $really_list){
                $withdraw_list[$k]=array_merge(  $withdraw_list[$k],$user_list, $really_list);
            }
        }
        //  var_dump(  $withdraw_list);die;
        $this->assign('withdraw_list',$withdraw_list);
        return $this->fetch();
    }
    //提现审核
    public  function  checkwith(){
        $withdraw_id=input('withdraw_id');
        $user_id=input('user_id');
        $money=input('money');
        if($this->request->isPost()){
            $data=[
                'status'=>input('status'),
                'remark'=>input('remark'),
                'check_time'=>time(),
            ];
            $status=Db::name('withdraw')->where('withdraw_id',$withdraw_id)->value('status');
           if($data['status']==2   && $status!=2){
              $res2=Db::name('user')->where('user_id',$user_id)->setInc('money',$money);
           }
            $res=Db::name('withdraw')->where('withdraw_id',$withdraw_id)->update($data);
            if($res){
                $this->success('审核成功','Share/withdraw',3);
            }else{
                $this->error('审核失败,请联系管理员!');
            }
        }else{
            $list=Db::name('withdraw')->field('money,user_id,status,remark')->where('withdraw_id',$withdraw_id)->find();
            $userlist=Db::name('user')->field('money')->where('user_id', $list['user_id'])->find();
            $this->assign('userlist', $userlist);
            $this->assign('list', $list);
            return $this->fetch();
        }
    }
}