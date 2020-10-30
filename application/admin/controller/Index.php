<?php
namespace app\admin\controller;

class Index extends Common
{
    public function index()
    {

        return $this->fetch();
    }
    //版权
    public  function copy(){
        return $this->fetch();
    }

}
