<?php
namespace  app\admin\controller;
use think\Controller;
use think\db;

class Freeexper  extends Common{
    //会员审核列表
    public  function  index(){
        $list = Db::name('freeexper')->paginate(10);

        $count = $list->total();
        $this->assign('list', $list);

        $this->assign('count', $count);

        return $this->fetch();
    }

    //删除会员免费体验

    public  function delFree(){
        $free_id = input('free_id', 0, 'intval');
        $where = array('free_id' => $free_id);
        $free_one=DB::name('freeexper')->where( $where)->find();
        //  var_dump($goods_one);die;
        if (empty( $free_one)) {
            $this->error('会员不存在');
        }

        $res = DB::name('freeexper')->where( $where)->delete();
        if ($res) {
            $this->success('会员删除成功', url('Freeexper/index'), 3);
        } else {
            $this->error('删除失败');
        }
    }
    //会员免费体验审核
    public  function check(){
        //跨域
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile=trim(input('post.mobile'));

        $res= Db::name('freeexper')->where(array('mobile'=>$mobile))->find();
        if($res){
            return json(array('status'=>0,'info'=>'您已提交申请,请勿再次提交.'));
        }else{
            //添加手机号到数据库
            $data=array(
                'mobile'=> $mobile,
                'time'=>time(),
            );
            Db::name('freeexper')->insert($data);
            return json(array('status'=>1,'info'=>'提交成功,请等待客服联系.'));
        }
    }
}