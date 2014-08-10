<?php
	class UpdatawebpagesAction extends OQAction{
		public function index(){
			$g = M("guestbook");
			$p = M("project");
			$arr = array(1598,1160,1749,1744,1226,505,1631,1626,183,1469);
			$arrCount = count($arr);
			for($i=0;$i<$arrCount;$i++){
				$projectID = $arr["$i"];
				$gdata = $g->where("project_id = '".$projectID."' AND site = 'zf'")->find();
				// echo '<pre>';
				// var_dump($gdata);
				$address = $gdata["address"];
				$address = 'http://'.$address;
				// echo $address.'<br />';
				$rs = $p->where("projectID = '".$projectID."' AND site = 'zf'")->setField('webPage',$address);
				if(!empty($rs)){
					echo  $projectID.' '.$address.' '.'sucess';
				}
			}
			echo 'al sucess';
		}
	}