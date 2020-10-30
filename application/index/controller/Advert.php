<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Advert extends Controller{
    //广告
    public function index(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
       $list=Db::name('advert')->where('is_on',1)->select();
       return json($list);
    }
}