<?php
class SourceAction extends OQAction{	
	public function index(){
		
	}
	
	
	public function analysis(){
		
		$source = D("Source");
		$iMaxID = $source->getField("MAX(id) as num");

		$aGBList = D("Guestbook")->where("ids >".$iMaxID)->limit(100000)->getField("ids,address,add_date",null);
		echo $iMaxID;
		$i=0;
		foreach ($aGBList as $r) {
			$i++;
			if(substr($r["address"],0,4) <> "http")
				$r["address"] = "http://".$r["address"];
			$aUrl = parse_url($r["address"]);
			$aD["id"]	= $r["ids"];
			$aD["source"]	= $aUrl["host"];
			$aD["addDate"] = $r["add_date"];
			$source->add($aD);
			unset($aD);
			echo $i."\n";
		}
		
		
		if($iMaxID < 1022000) {
			echo "OK, refresh at 2 seconds later......";
			//sleep(2);
			//redirect('/tpcms/index.php/Admin/Source/analysis');
			//EXIT();
			
		}
		
		$this->display();
	}
}

?>