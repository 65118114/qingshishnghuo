<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Advert extends Common {

    //广告首页
    public  function  index(){
        $list=Db::name('advert')->order('advert_id desc')->paginate(10);

        // var_dump($list->toArray()["realstatus"]);die;
        // var_dump( $list);die;
        $count = $list->total();
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();
    }
    //添加广告
    public function addAdvert()
    {
        if($this->request->isPost()){
            $data=[
                'advpalce' => input('advpalce'),
                'advertname' => input('advertname'),
                'img' => input('img'),
                'url' => input('url'),
                'is_on' => input('is_on'),
                'goods_id' => input('goods_id'),
                'create_time' => time(),
            ];
            // var_dump( $data);die;
            $res=Db::name('advert')->insert($data);
            if( $res){
                $this->success('添加成功','Advert/index',3);
            }else{
                $this->error('添加失败!');
            }
        }else{
            return $this->fetch();
        }

    }

    //修改广告
    public  function  editAdvert(){
        $advert_id=input('advert_id');
        $list=Db::name('advert')->where('advert_id',$advert_id)->find();
        if($this->request->isPost()){
            $img =input('img');
            if(empty( $img)){
                $img =  $list['img'];
            }
            $data=[
                'advpalce' => input('advpalce'),
                'advertname' => input('advertname'),
                'img' =>  $img,
                'url' => input('url'),
                'is_on' => input('is_on'),
                'goods_id' => input('goods_id'),
            ];
            //  var_dump( $data);die;
            $res=Db::name('advert')->where('advert_id',$advert_id)->update($data);
            if( $res){
                $this->success('更新成功','Advert/index',3);
            }else{
                $this->error('更新失败!');
            }
        }else{

            // var_dump(   $list);die;
            $this->assign('list', $list);
            return $this->fetch();
        }
    }
    //删除广告
    public  function  delAdvert(){
        $advert_id=input('advert_id');
        $list= Db::name('advert')->where('advert_id',$advert_id)->find();
        if(empty($list)){
            $this->error('该广告不存在');
        }
        $res= Db::name('advert')->where('advert_id',$advert_id)->delete();
        if(  $res){
            $this->success('删除成功','Advert/index',3);
        }else{
            $this->error('删除失败，请联系管理员');
        }
    }
}