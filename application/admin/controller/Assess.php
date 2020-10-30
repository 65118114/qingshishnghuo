<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Assess extends Common{
    public  function  index(){
        $goods_assess=Db::name('goods')->order('goods_id desc')->select();

        foreach ($goods_assess  as $k=>$v){
            $assessone=Db::name('assess')->where('goods_id',$v['goods_id'])->field('content,file,mobile,create_time')->find();
            $goods_assess[$k]['content'] =  $assessone['content'];
            $goods_assess[$k]['file'] = explode('===', $assessone['file']);
            $goods_assess[$k]['mobile'] =  $assessone['mobile'];
            $goods_assess[$k]['create_time'] =  $assessone['create_time'];
        }
        // var_dump(  $goods_assess);die;
        //  $list=Db::name('assess')->order('assess_id desc')->select();
        // $count = $goods_assess->total();
        //   $this->assign('count',$count);
        $this->assign('goods_assess',$goods_assess);
        return $this->fetch();
    }
    //查看所有评价
    public  function  allAssess(){
        $goods_id=input('goods_id');
        $goods_name=Db::name('goods')->where('goods_id',$goods_id)->value('goods_name');
        $list=  Db::name('assess')->where('goods_id', $goods_id)->select();
        foreach ($list as $k=>$v){
            $list[$k]['goods_name']= $goods_name;
            $list[$k]['file']=explode('===', $list[$k]['file']);
        }

        // var_dump(  $list);die;

        $this->assign('allAssess',$list);
        //    var_dump(  $list);die;
        return $this->fetch();

    }
    //添加评价

    public function addassess(Request $request)
    {
        if ($request->isPost()) {
            $img = input('image/a');
            $img = implode('===', $img);
            $data = [
                'mobile' => input('mobile'),
                'content' => input('content'),
                'create_time' => time(),
                'goods_id' => input('goods_id'),
                'file'=> $img,
            ];
            //  var_dump($data);die;
            $res= Db::name('assess')->insert($data);
            if ($res ) {
                $this->success('添加成功','assess/index',3);
            } else {
                $this->error('添加失败');
            }
        }else{
            $goods_list=Db::name('goods')->where('goods_id','<>',100)->select();
            //   var_dump($good_list);die;
            $this->assign('goods_list',$goods_list);
            return $this->fetch();
        }

    }
    //删除评价
    public  function  delAssess(){
        $assess_id=input('assess_id');
        // var_dump( $assess_id);die;
        $res=  Db::name('assess')->where('assess_id', $assess_id)->delete();
        if( $res){
            $this->success('删除成功','Assess/index',3);
        }else{
            $this->error('删除失败');
        }
    }
    //回复评价
    public  function  recAssess(Request $request){
        $assess_id=input('assess_id');
        // var_dump( $assess_id);die;
        if($request->isPost()){
            $recinfo=input('recinfo');
            //   var_dump( $recinfo);die;
            $data=[
                'recinfo'=> $recinfo,
            ];
            $res=  Db::name('assess')->where('assess_id',$assess_id)->update($data);
            if( $res){
                $this->success('回复成功','Assess/index',3);
            }else{
                $this->error('网络错误');
            }
        }else{
            $list=  Db::name('assess')->where('assess_id', $assess_id)->find();
            $this->assign('list',$list);
            return $this->fetch();
        }


    }
}