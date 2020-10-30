<?php

namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;

class Station extends Controller
{
    // 添加驿站
    public function addStation(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $data=[
                'submit_name'=>input('submit_name'),
                'address'=>input('address'),
                'store_mobile'=>input('store_mobile'),
                'door_img'=>input('door_img'),
                'bill_img'=>input('bill_img'),
                'station_name'=>input('station_name'),
                'create_time'=>time(),

            ];
              file_put_contents('ceshi.txt',$data);
            $res = Db::name('station')->insert($data);
            if ($res){
                return json(['status' => 1, 'msg' => '添加成功,请等到系统处理']);
            }else {
                return json(['status' => 0, 'msg' => '添加失败,请联系客服处理']);
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
            $path = config('STATION_PATH');
            // var_dump($validate['ext'].'==='. $path);die;
            // var_dump($file->validate($validate));die;
            $res = $file->validate($validate)->move($path);
            //  var_dump(1);die;
            if ($res) {
                //var_dump( $res);die;
                $file_path = $res->getPathname();
                return json(['status' => 1, 'info' => config('server_name') . '/' . $file_path]);
            } else {
                return json(['status' => 0, 'info' => $file->getError()]);
            }
        }
    }
}