<?php
/*
* 数据执行操作公共类
*/
class OQAction extends Action{
   Public function _initialize(){
   // 初始化的时候检查用户权限
         // $this->checktest();
   		$this->checkRbac();
	}
  protected function checktest() {
    echo '测试是否调用';
  }

    // 检查用户权限(主要是检查session是否存在)
  protected function checkRbac() {
   		//判断是否存在session值,不存在重新登录
		if(!isset($_SESSION['username'])){
			session(null);
            redirect(C('cms_admin').'?s=Admin/Login');
		}
		//TP判断session是否设置
		//session('?username');
 	}
  public function _empty(){
              $this->display("Error:404");
          }

}