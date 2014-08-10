<?php
/*
* 空模块
* 前台模块指定错误时调用
*/
class EmptyAction extends Action{

    public function _initialize(){
    	$this->display(C('ERROR_PAGE'));
		die;
    }
	
}