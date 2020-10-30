<?php
namespace  app\admin\model;
use think\Model;
use think\Db;
class Common extends Model{
    protected  $table;
    public function __construct($data)
    {
        parent::__construct($data);
    }
    //添加数据
    public  function addData($data=array()){
        return Db::name($this->table)->insert($data,0,1);
    }
    //编辑数据
    public function saveData($where=array(),$data=array()){
        return Db::name($this->table)->where($where)->update($data);
    }
    //删除数据
    public function delData($where){
        return Db::name($this->table)->where($where)->delete();
    }
    //取出一个字段数据
    public function getValue($where,$filed="*"){
        return Db::name($this->table)->where($where)->value($filed);
    }
    //取出多个字段 指定数组键值
    public  function getFiled($where,$field,$key){
        return Db::name($this->table)->where($where)->column($field,$key);
    }
    //取出一条数据
    public  function getOne($where=array(),$field="*",$order=""){
        return Db::name($this->table)->where($where)->field($field)->order($order)->find();
    }
    //设计字段自增
    public function setIncData($where=array(),$filed="*",$num=1){
        return Db::name($this->table)->where($where)->setInc($filed,$num);
    }
    //设置自减
    public  function setDecData($where=array(),$filed="*",$num=1){
        return Db::name($this->table)->where($where)->setDec($filed,$num);
    }
    /**插入数据
     * @param $table
     * @param $data
     * @return int|string
     */
    public function tableAddData ($table, $data)
    {
        return Db::name($table)->insert($data, 0, 1);
    }
    /**编辑数据
     * @param array $where
     * @param array $data
     */
    public function tableSaveData ($table, $where = array(), $data = array())
    {
        return Db::name($table)->where($where)->update($data);
    }
    /**取出一条数据 根据表名
     * @param array $where
     * @param string $field
     * @param string $order
     * @return array|null|\PDOStatement|string|Model
     */
    public function tableGetOne ($table, $where = array(), $field = "*", $order = "")
    {
        return Db::name($table)->where($where)->field($field)->order($order)->find();
    }
    /**根据表删除数据
     * @param $table
     * @param $where
     * @return int
     */
    public function tableDelData ($table, $where)
    {
        return Db::name($table)->where($where)->delete();
    }
    /**根据表更新数据
     * @param $table
     * @param $where
     * @return int
     */
    public function tableUpdateData ($table, $where)
    {
        return Db::name($table)->where($where)->update(['is_on'=>0]);
    }

}
