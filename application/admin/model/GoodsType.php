<?php
namespace  app\admin\model;
use think\Db;
class GoodsType extends  Common{
    protected  $table;
    public function __construct($data=[])
    {
        parent::__construct($data);
        $this->table="goods_class";
    }
    public function pageData($param=array(),$range='total'){
        $where=array();
        //父级id
        if(isset($param['pid']) && $param['pid']>0){
            $where[]=array('pid','=',$param['pid']);
        }
        //判断是否展示
        if(isset($param['is_show']) && $param['is_show']>-1){
            $where[]=array('is_show','=',$param['is_show']);
        }
        //判断level
        if(isset($param['level']) && $param['level']>0){
            $where[]=array('level','=',$param['level']);
        }
        //求总数
        if($range=='total'){
            return Db::name($this->table)->where($where)->count();
        }else{
            //分页
            $limit=isset($param['limit'])?(int)$param['limit']:0;
            if($limit>0){
                $page=isset($param['page'])?(int)$param['page']:1;
                $limit_start=($page-1)*$limit;
                $limit=$limit_start.",".$limit;
            }else{
                $limit=0;
            }
            //字段
            $field=isset($param['field'])?$param['field']:"";
            //排序
            $order=isset($param['order'])?$param['order']:'id asc';
            return Db::name($this->table)->where($where)->field($field)->limit($limit)->order($order)->select();

        }
    }
}
