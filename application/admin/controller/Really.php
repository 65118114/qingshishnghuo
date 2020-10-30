<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Really extends Controller{

    //实名认证首页
    public  function  index(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list=Db::name('really')->order('really_id desc')->paginate(10);
        foreach ( $list as $k=>$v){
            $user=Db::name('user')->where(array('user_id'=>$v['user_id']))->find();
            //  print_r( $goods[$k]);die;
            $list[$k] = array_merge($list[$k],$user);
        }
        // var_dump($list->toArray()["realstatus"]);die;
     //   var_dump( $list);die;
        $count = $list->total();
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();
    }
    //审核
    public function checkReally()
    {
        $user_id=input('user_id');
        $really_id=input('really_id');
        //var_dump($user_id);die;
        if($this->request->isPost()){
            $data=[
                'name' => input('name'),
                'card' => input('card'),
                'pic1' => input('pic1'),
                'pic2' => input('pic2'),
                'pic3' => input('pic3'),
                'realstatus'=>input('realstatus'),
                'reason'=>input('reason'),
            ];
            // var_dump( $user_id);die;
            $res=Db::name('really')->where('really_id', $really_id)->update($data);
            $realstatus=Db::name('really')->where('really_id', $really_id)->value('realstatus');
            //var_dump(  $realstatus);die;
            if($realstatus ==1){
                $reall=Db::name('user')->where('user_id', $user_id)->update(['is_real'=>1]);
            }
            if($realstatus ==3){
                $rea22=Db::name('user')->where('user_id', $user_id)->update(['is_real'=>3]);
            }
            if($res){
                $this->success('审核成功','Really/index',3);
            }else{
                $this->error('无更新内容!');
            }
        }else{
            $list=   Db::name('really')->where('really_id',$really_id)->field(array('name','card','pic1','pic2','pic3','realstatus','reason'))->find();
            // var_dump( $list);die;
            $this->assign('list', $list);
            return $this->fetch();
        }

    }
}