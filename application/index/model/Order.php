<?php
namespace  app\index\model;
use think\Db;
use think\Model;

class Order extends  Model {
  //添加订单
    public function addOrder($array){
         return Db::name('order')-> insert($array);
    }
}