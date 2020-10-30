<?php
namespace  app\index\model;
use think\Db;
use think\Model;

class Refund extends  Model {
    //取出一条数据
    public  function getOne($where=array(),$field="*",$order=""){
        return Db::name($this->table)->where($where)->field($field)->order($order)->find();
    }
}