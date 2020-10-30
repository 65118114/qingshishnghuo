<?php
namespace  app\admin\model;
use think\Db;
class Admin extends  Common{
    protected  $table;
    public function __construct($data=[])
    {
        parent::__construct($data);
        $this->table="admin";
    }
    /**
     * 求管理员职位
     */
    public function getRole($where,$field){
        return Db::name('role')->alias('R')->join(config('database.prefix').'role_user RU','RU.role_id = R.id','LEFT')->where($where)->field($field)->select();
    }
}
