<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/6
 * Time: 9:04
 */
if(!function_exists('node_merge')){
    function node_merge($node,$access=null,$pid=0){
        $arr=array();
        foreach ($node as $v){
            if(is_array($access)){
                $v['access']=in_array($v['id'],$access)?1:0;
            }
            if($v['pid'] == $pid){
                $v['child']=node_merge($node,$access,$v['id']);
                $arr[]=$v;
            }
        }
        return $arr;
    }
}