<?php

namespace app\admin\model;

use think\Db;

class Role extends Common
{
    protected $table;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->table = "role";
    }

    public function pageData($param = array(), $range = 'total')
    {
        $where = array();

        //判断是否开启
        if (isset($param['status']) && $param['is_show'] > -1) {
            $where[] = array('status', '=', $param['status']);
        }
        //根据角色中文名称查询
        if (isset($param['remark']) && $param['remark'] !== "") {
            $where[] = array('remark', 'like', '%' . $param['remark'] . '%');
        }
        //求总数
        if ($range == 'total') {
            return Db::name($this->table)->where($where)->count();
        } else {
            //分页
            $limit = isset($param['limit']) ? (int)$param['limit'] : 0;
            if ($limit > 0) {
                $page = isset($param['page']) ? (int)$param['page'] : 1;
                $limit_start = ($page - 1) * $limit;
                $limit = $limit_start . "," . $limit;
            } else {
                $limit = 0;
            }
            //字段
            $field = isset($param['field']) ? $param['field'] : "";
            //排序
            $order = isset($param['order']) ? $param['order'] : 'id asc';
            return Db::name($this->table)->where($where)->field($field)->limit($limit)->order($order)->select();

        }
    }
    //删除数据
    //  public function delData($role_id)
    //{
    //          //权限表
    //         Db::name('access')->where(array('role_id'=>$role_id))->delete();
    //角色表
    //        return  Db::name('role')->where(array('id'=>$role_id))->delete();
    //  }
    //当前角色的权限
    public function accessGetField($where,$field){
        return Db::name('access')->where($where)->column($field);
    }
    //删除权限
    public  function accessDelData($where){
        return Db::name('access')->where($where)->delete();
    }
    //添加权限
    public function accessAddData($data){
        return Db::name('access')->insertAll($data);
    }
    //添加用户与角色中间表
    public function userRoleAddData($data){
        return Db::name('role_user')->insertAll($data);
    }
}
