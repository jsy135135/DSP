<?php
return array (
  'default_theme' => 'default',
  'USER_AUTH_ON' => true,
  'USER_AUTH_TYPE' => 2,
  'USER_AUTH_KEY' => 'authId',
  'ADMIN_AUTH_KEY' => 'administrator',
  'USER_AUTH_MODEL' => 'User',
  'AUTH_PWD_ENCODER' => 'md5',
  'USER_AUTH_GATEWAY' => '/Admin/Login',
  'NOT_AUTH_MODULE' => 'Login,Public',
  'REQUIRE_AUTH_MODULE' => '',
  'NOT_AUTH_ACTION' => '',
  'REQUIRE_AUTH_ACTION' => '',
  'GUEST_AUTH_ON' => false,
  'GUEST_AUTH_ID' => 0,
  'RBAC_ROLE_TABLE' => 'role',
  'RBAC_USER_TABLE' => 'role_user',
  'RBAC_ACCESS_TABLE' => 'access',
  'RBAC_NODE_TABLE' => 'node',
  'SPECIAL_USER' => 'admin',
  'cms_name' => '呼叫中心管理系统',
  'cms_url' => 'http://www.kingbone.cn',
  'cms_var' => '1.0.2',
  'cms_admin' => 'index.php',
   //邮件配置
   // 配置邮件发送服务器
    'MAIL_HOST' =>'smtp.exmail.qq.com',//smtp服务器的名称
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
    'MAIL_USERNAME' =>'732677288@qq.com',//你的邮箱名
    'MAIL_FROM' =>'732677288@qq.com',//发件人地址
    'MAIL_FROMNAME'=>'贾思远',//发件人姓名
    'MAIL_PASSWORD' =>'jsy135135',//邮箱密码
    'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
);
?>