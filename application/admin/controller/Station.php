<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Station extends Common{

    //驿站首页
    public  function  index(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list=Db::name('station')->order('station_id desc')->paginate(10);
        $count = $list->total();
        //  var_dump( $list);die;
        $data = $list->toArray()['data'];
        $this->assign('data', $data);
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();

    }


}