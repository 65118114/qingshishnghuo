<?php
return array(
	//权限认证
	'RBAC_SUPERADMIN' => 'admin',//指定超级管理员
	'ADMIN_AUTH_KEY' => 'admin',//指定超级管理员识别号
	'USER_AUTH_ON'  => true,
	'USER_AUTH_TYPE' => 1, //验证类型(1:登录验证 2:实时验证);
	'USER_AUTH_KEY' => 'uid', //用户认证识别号
	'NOT_AUTH_MODULE' => 'Index',//无需认证的控制器
	'NOT_AUTH_ACTION' => 'copy',//无需认证的方法
	'RBAC_ROLE_TABLE' => 'gongsishop_role',//角色表名称,
	'RBAC_USER_TABLE' =>'gongsishop_role_user',//角色与用户的中间表
	'RBAC_ACCESS_TABLE' => 'gongsishop_access',//权限表
	'RBAC_NODE_TABLE' =>'gongsishop_node',//节点表
	'USER_AUTH_MODUEL' => 'gongsishop_admin',//管理员表
);