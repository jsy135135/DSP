<?php
	class TestdAction extends OQAction{
		public function index(){
			$date = '2014-05-19';
			$i = 820;
			$dealed = D("data_dealed");
			$guestbook = D("guestbook");
		$Dlist = $dealed->where("addDate = '".$date."' AND u_id=$i")->order('phone')->select();
		$Dcount = $dealed->where("addDate = '".$date."' AND u_id=$i")->count();
		echo '<pre>';
		echo $_SESSION['kfname'];
		echo $_SESSION['username'];
		echo session("username");
		// var_dump($Dlist);
		// var_dump($Dcount);
		$this->assign('count',$Dcount);
		$this->assign('date',$date);
		$this->assign('DList',$Dlist);
		$this->display();
		}
	}