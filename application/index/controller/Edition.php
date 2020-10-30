<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Edition extends Controller{
   //版本升级
    public function index(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list=Db::name('edition')->where('edition_id',1)->find();
        return json ($list);
    }
}