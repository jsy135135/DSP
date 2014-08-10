<?php
	class TestgAction extends OQAction{
		public function index(){
			$date = '2014-05-19';
			$i = 820;
			$dealed = D("data_dealed");
			$guestbook = D("guestbook");
		$Glist = $guestbook->where("deal_date =  '".$date."' AND u_id=$i")->order('phone')->select();
		$Gcount = $guestbook->where("deal_date =  '".$date."' AND u_id=$i")->count();
		// echo '<pre>';
		// var_dump($Glist);
		// var_dump($Gcount);
		$this->assign('count',$Gcount);
		$this->assign('date',$date);
		$this->assign('GList',$Glist);
		$this->display();
		}
	}