<?php
/**
 * 前台入口模块
 * 
 */
class IndexAction extends HomeAction {
    public function index(){
    	session_start();
    	$_SESSION['right_enter'] = 1;
    	header("Location: index.php/Admin/Login");
		$this->display();
	}
	
}