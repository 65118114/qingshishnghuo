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
    //�������
    public  function addData($data=array()){
        return Db::name($this->table)->insert($data,0,1);
    }
    //�༭����
    public function saveData($where=array(),$data=array()){
        return Db::name($this->table)->where($where)->update($data);
    }
    //ɾ������
    public function delData($where){
        return Db::name($this->table)->where($where)->delete();
    }
    //ȡ��һ���ֶ�����
    public function getValue($where,$filed="*"){
        return Db::name($this->table)->where($where)->value($filed);
    }
    //ȡ������ֶ� ָ�������ֵ
    public  function getFiled($where,$field,$key){
        return Db::name($this->table)->where($where)->column($field,$key);
    }
    //ȡ��һ������
    public  function getOne($where=array(),$field="*",$order=""){
        return Db::name($this->table)->where($where)->field($field)->order($order)->find();
    }
    //����ֶ�����
    public function setIncData($where=array(),$filed="*",$num=1){
        return Db::name($this->table)->where($where)->setInc($filed,$num);
    }
    //�����Լ�
    public  function setDecData($where=array(),$filed="*",$num=1){
        return Db::name($this->table)->where($where)->setDec($filed,$num);
    }
    /**��������
     * @param $table
     * @param $data
     * @return int|string
     */
    public function tableAddData ($table, $data)
    {
        return Db::name($table)->insert($data, 0, 1);
    }
    /**�༭����
     * @param array $where
     * @param array $data
     */
    public function tableSaveData ($table, $where = array(), $data = array())
    {
        return Db::name($table)->where($where)->update($data);
    }
    /**ȡ��һ������ ���ݱ���
     * @param array $where
     * @param string $field
     * @param string $order
     * @return array|null|\PDOStatement|string|Model
     */
    public function tableGetOne ($table, $where = array(), $field = "*", $order = "")
    {
        return Db::name($table)->where($where)->field($field)->order($order)->find();
    }
    /**���ݱ�ɾ������
     * @param $table
     * @param $where
     * @return int
     */
    public function tableDelData ($table, $where)
    {
        return Db::name($table)->where($where)->delete();
    }
    /**���ݱ��������
     * @param $table
     * @param $where
     * @return int
     */
    public function tableUpdateData ($table, $where)
    {
        return Db::name($table)->where($where)->update(['is_on'=>0]);
    }

}
