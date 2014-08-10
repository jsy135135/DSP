<?php
/**
 * 后台公共模块
 * 注意：此模块没有RBAC控制
 */
 
 class PublicAction extends AdminAction{

 	//生成4位数验证码
    public function verify() {
    	import("ORG.Util.Image");	//图像操作类库
//        $type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        Image::buildImageVerify();
    }

 }