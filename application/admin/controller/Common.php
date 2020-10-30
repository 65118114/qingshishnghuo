<?php
namespace app\admin\controller;
use think\Request;
use think\Controller;
use Org\Util\Rbac;

class Common extends Controller
{
    public function initialize ()
    {
        //判断session id是否存在
        if (session('uid') == "") {
            $this->redirect("Login/index");
        }
        define('MODULE_NAME', $this->request->module());
        define('CONTROLLER_NAME', $this->request->controller());
        define('ACTION_NAME', $this->request->action());
        //无需验证的控制器和无需验证的方法
        $notAuth = in_array(CONTROLLER_NAME, explode(',', config('rbac.NOT_AUTH_MODULE'))) || in_array(ACTION_NAME, explode(',', config('rbac.NOT_AUTH_ACTION')));
        //验证权限
        if (config('rbac.USER_AUTH_ON') && !$notAuth) {
            //读取用户权限
            //	var_dump(Rbac::AccessDecision());die;
            $res = Rbac::AccessDecision();

            if ($res == 0) {
                $this->error('亲,没有操作权限');
            }
        }

    }
}
