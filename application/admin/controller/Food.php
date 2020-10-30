<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Food extends Common{

    //添加食谱
    public  function  addFood(){

        if ($this->request->isPost()){
            $breakfast= input('breakfast/a');
            $breakhot = input('breakhot/a');
            //  var_dump(  $breakhot);die;
            if( $breakfast){
                foreach (   $breakfast as $k=>$v){
                    $data=[
                        'breakfast'=>$v,
                        'breakhot'=> $breakhot[$k],
                    ];
                    $res1=Db::name('breakfast')->insert($data);
                }
            }
            $lunch= input('lunch/a');
            $lunchhot = input('lunchhot/a');
            //  var_dump(  $breakhot);die;
            if($lunch){
                foreach (  $lunch as $k=>$v){
                    $data=[
                        'lunch'=>$v,
                        'lunchhot'=> $lunchhot[$k],
                    ];
                    $res2=Db::name('lunch')->insert($data);
                }
            }
            $dinner= input('dinner/a');
            $dinnerhot = input('dinnerhot/a');
            //  var_dump(  $breakhot);die;
            if( $dinner){
                foreach (  $dinner as $k=>$v){
                    $data=[
                        'dinner'=>$v,
                        'dinnerhot'=> $dinnerhot[$k],
                    ];
                    $res3=Db::name('dinner')->insert($data);
                }
            }
            if($res1 && $res3 && $res2 ){
                $this->success('添加成功','Food/index',3);
            }else{
                $this->error('添加失败');
            }
        }else{
            return $this->fetch();
        }

    }
    //晚餐
    public  function  dinner(){
        $list=Db::name('dinner')->order('dinner_id desc')->paginate('10');
        //var_dump($list);die;
        $count =$list->total();
        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }
    //早餐
    public  function  breakfast(){
        $list=Db::name('breakfast')->order('break_id desc')->paginate('10');
        //var_dump($list);die;
        $count =$list->total();
        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }
//午餐
    public  function  lunch(){
        $list=Db::name('lunch')->order('lunch_id desc')->paginate('10');
        //var_dump($list);die;
        $count =$list->total();
        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }
    public function  delLunch(){
        $lunch_id=input('lunch_id');
        // var_dump($lunch_id);die;

        $res=Db::name('lunch')->where('lunch_id',$lunch_id)->delete();
        if($res){
            $this->success('删除成功','Food/lunch',3);
        }else{
            $this->error('删除失败');
        }
    }
    public function  delbreakfast(){
        $break_id=input('break_id');
        // var_dump($lunch_id);die;
        $res=Db::name('breakfast')->where('break_id',$break_id)->delete();
        if($res){
            $this->success('删除成功','Food/breakfast',3);
        }else{
            $this->error('删除失败');
        }
    }
    public function  deldinner(){
        $dinner_id=input('dinner_id');
        // var_dump($lunch_id);die;
        $res=Db::name('dinner')->where('dinner_id',$dinner_id)->delete();
        if($res){
            $this->success('删除成功','Food/dinner',3);
        }else{
            $this->error('删除失败');
        }
    }
    //视频首页
    public function video(){
        $list=Db::name('video')->order('video_id desc')->paginate('10');
        //var_dump($list);die;
        $count =$list->total();
        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }
    //添加视频
    public  function  addvideo(){
        if($this->request->isPost()){
            $data=[
                'video'=>config('server_name').input('video'),
                'minute'=>input('minute'),
                'action'=>input('action'),
            ];
            //var_dump($video);die;
            $res=Db::name('video')->insert(  $data );
            if( $res){
                $this->success('添加成功','Food/video',3);
            }else{
                $this->error('添加失败');
            }
        }

        return $this->fetch();
    }
    //删除视频
    public  function  delVideo(){
        $video_id=input('video_id');
        //   var_dump(      $video_id);die;
        $res=Db::name('video')->where('video_id',$video_id)->delete();
        if($res){
            $this->success('删除成功','Food/video',3);
        }else{
            $this->error('删除失败');
        }
    }
    // 处理图片上传
    public  function upload(Request $request)
    {
        $files = $request->file();

        $validate=config('VIDEO_VALIDATE');
        $path = config('VIDEO_PATH');
        // var_dump($path);die;
        foreach($files as $file ){
            $res=$file->validate($validate)->move($path);
            if($res){
                $file_path=$res->getPathname();
                return json(['status'=>'success','msg'=>'/'.$file_path]);
            }else{
                return json(['status'=>'fail','msg'=>$file->getError()]);
            }
        }
    }
}