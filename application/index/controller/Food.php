<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Food extends Controller{
   //早餐
    public function breakfast(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $breakfast=input('breakfast');
        if(empty($breakfast)){
            $list=Db::name('breakfast')->order('break_id desc')->find();
        }else{
            $list=Db::name('breakfast')->where('rand()')->find();
        }

        return json ($list);

    }
    //中餐

    public function lunch(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $lunch=input('lunch');
        if(empty( $lunch)){
            $list=Db::name('lunch')->order('lunch_id desc')->find();
        }else{
            $list=Db::name('lunch')->where('rand()')->find();
        }

        return json ($list);

    }
    //晚餐

    public function dinner(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $dinner=input('dinner');
        if(empty($dinner)){
            $list=Db::name('dinner')->order('dinner_id desc')->find();
        }else{
            $list=Db::name('dinner')->where('rand()')->find();
        }
        return json ($list);

    }
    //视频
    public  function  video(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $video=input('video');
        if(empty($video)){
            $list=Db::name('video')->order('video_id desc')->limit(3)->select();
        }else{
            $list=Db::name('video')->where('rand()')->limit(3)->select();
        }
        return json($list);
    }

    // 处理图片上传
    public  function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $files = $request->file('file');
       // var_dump($files);die;
        $validate = config('VALIDATE');
        $path = config('HEALTHY_PATH');
        foreach ($files as $file) {
            $res = $file->validate($validate)->move($path);
            if ($res) {
                //var_dump(    $res);die;
                $file_path.= $res->getPathname().'===';
              // return json(['status' => 1, 'info' => 'http://qingshi.natapp1.cc/' . '/' . $file_path]);
            } else {
             //  return json(['status' => 0, 'info' => $file->getError()]);
            }
        }
        return   $file_path;
    }
}