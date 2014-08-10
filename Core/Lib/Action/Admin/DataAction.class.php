<?php
class DataAction extends OQAction {
	/**
	 * 按月显示当月数据
	 */
	public function index(){
		$iMonth = $_REQUEST["month"] > 0 ? $_REQUEST["month"] : date("m");
		$aSite = C("site");
		$this->assign("site", $aSite);
		$this->assign("month",$iMonth);
		$this->assign("title", "data report");
		$this->display();
	}
	
	/**
	 * 数据概况
	 */
	public function main(){
		$gb = D ("Guestbook");
		$uID = session("username");
		if($uID == "admin"){
			$uID = 826; //默认外呼标记为826
		}
		//数据库总量
		$data["all"] = $gb->count();
		//当前可以处理的数据量
		$data["nowCan"] = $gb->where("project_id >0 AND u_id=0 AND add_date>='2014-04-16'")->count();
		if($uID > 0)
		{
			//我的所有数据
			$data["myDataAll"] = $gb->where("u_id=".$uID)->count();
			//我的未处理数据
			$data["myDataNot"] = $gb->where("u_id=".$uID." AND deal_status=1")->count();
			//我的已经处理数据
			$data["myDataYes"] = $gb->where("u_id=".$uID." AND deal_status<>1")->count();
			$data["dataAgain"] = D("DataDealed")->where("again=0 AND status <0")->count();
			$data["myDataAgain"] = D("DataDealed")->where("again=0 AND status <0 AND u_id=".$uID)->count();
		} else {
			$data["myDataAll"] = "该用户没有数据";
			$data["myDataNot"] = "该用户没有数据";
			$data["myDataYes"] = "该用户没有数据";
			$data["dataAgain"] = "该用户没有数据";
			$data["myDataAgain"] = "该用户没有数据";
		}
		$this->assign("data", $data);
		$this->display();
	}
	
	/**
	 * json方式获取当月的数据，读取的是缓存表
	 * 
	 */
	function getDataByMonth() {
		set_time_limit(0);
		$iMonth = $_REQUEST["month"] > 0 ? $_REQUEST["month"] : date("m");
		$iYear = "2014";
		$iDays = date("t",$iYear.'-'.$iMonth);
		$aList = array();
		$aSite = C("site");
		for($i=0; $i< $iDays;$i++){
			$dThisDay = date("Y-m-d", strtotime($iYear.'-'.$iMonth.'-'.$i));
			$aList[$i]["day"] = '<a href="'.__ROOT__.'/index.php/Admin/Data/detail/date/'.$dThisDay.'" target="_blank">'.$dThisDay.'</a>';
			$aTemp = $this->getDataByDay($dThisDay);
			//var_dump($aTemp);
			//print_r($aTemp);
			for ($j=0;$j<count($aSite);$j++)	{
				$key = $aSite[$j];
				for($k=0;$k<count($aTemp);$k++) {
					if($aTemp[$k]["source"] == $key)
					{
						$aList[$i][$key."_get_num"] = $aTemp[$k]["get_num"];
						$aList[$i][$key."_get_one"] = $aTemp[$k]["get_one"];
						$aList[$i][$key."_get_more"] = $aTemp[$k]["get_more"];
						$aList[$i][$key."_visit"] = $aTemp[$k]["visit"];
						$aList[$i][$key."_get_ratio"] =  round($aTemp[$k]["get_num"] / $aTemp[$k]["visit"],3) *100 ."%";
						$aList[$i][$key."_one_ratio"] =  round($aTemp[$k]["get_one"] / $aTemp[$k]["get_num"], 3) *100 ."%";
					}
				}	
			}
		}
		echo json_encode($aList);
	}
	
	/**
	 * 获取当天的数据
	 */
	function detail() {
		$dDate = $_REQUEST["date"];
		$aTotalList = $this->getDataByDay($dDate);
		//print_r($aTotalList);
		$this->assign("date", $dDate);
		$this->assign("totalList", $aTotalList);
		$this->assign("siteList", C("site"));
		$this->assign("title", "Detail Of Day:".$dDate);
		$this->display();
	}

	
	
	/**
	 * 获取1对1的数据
	 */
	function getOneToOne() {
		$dDate = $_REQUEST["date"];
		$sSite = $_REQUEST["site"];
		echo json_encode($this->getOneToX($dDate, "one", $sSite));
	}
	
	/**
	 *  获取一对多的数据
	 */
	function getOneToMore(){
		$dDate = $_REQUEST["date"];
		$sSite = $_REQUEST["site"];
		$aList = $this->getOneToX($dDate, "more", $sSite);
		foreach ($aList as &$r){
			$r["deal"] = '<a href="__ROOT__ /index.php/Admin/Data/dealMore/id/'.$r["ids"].'">view</a>';
		}
		echo json_encode($aList);
	}
	
	/**
	 *  根据参数获取相应的数据，一般为一对一，或者一对多
	 * @param unknown $dDate  时间
	 * @param string $sType 参数类型 all 为全部  one 为一对一，more为一对多
	 * @return Ambigous <mixed, boolean, NULL, string, unknown, multitype:, multitype:multitype: , void, object>
	 */
	private function getOneToX($dDate, $sType = "all", $sSite = "total") {
		$sSql = " add_date ='".$dDate."'";
		if($sType == "all")
			$sSql .= " AND 1";
		elseif($sType == "one")
			$sSql .= " AND project_id > 0";
		elseif ($sType == "more")
			$sSql .= " AND project_id = 0";
		if($sSite == "total")
			$sSql .= " AND 1";
		else 
			$sSql .= " AND site='".$sSite."'";
		$sSql .= " AND deal_status='0'";
		$gb = M ("Guestbook");
		$aList = $gb->where($sSql)->field("ids,phone,project_id,ips,add_date")->limit(100)->select();
		//$aList = $gb->where($sSql)->select();
		//print_r($aList);
		//$this->display("a");
		return $aList;
	}
	
	
	/**
	 * 处理一对多的数据
	 */
	function dealMore() {;
		$iID = $_REQUEST["id"];
		$gb = M ("Guestbook");
		$log = M ("log");
		$aUData["ids"] = $iID;
		$aUData["deal_status"] = 1;
		$gb->save($aUData);
		$aInfo = $gb->where(" ids=".$iID)->find();
		//查询这个人的浏览轨迹，根据电话和IP区分，认为一小时以内都是他吧
		$dStartTime = date("Y-m-d H:i:s", strtotime($aInfo["times"]) - 30*60);
		$dEndTime = date("Y-m-d H:i:s", strtotime($aInfo["times"]) + 30*60);
		
		$aList = $log->where(" project_id >0 AND site='".$aInfo["site"]."' AND ip LIKE '%".$aInfo["ips"]."%' AND log_date='".$aInfo["add_date"]."' AND original_time between '".$dStartTime."' AND '".$dEndTime."'")->select();
		//print_r($aList);
		
		$this->assign("aList",$aList);
		$this->assign("aInfo", $aInfo);
		$this->display();
	}
	
	
	function getDataByDay($dDate) {
		$data = M ("Data");
		$aList = $data->where(" date = '".$dDate."'")->select();
		return $aList;
	}
	
	function log_visit(){
		$dDate = trim($_REQUEST["date"]);
		if(empty($dDate))
			$dDate = date("Y-m-d", strtotime("1 days ago"));  //昨天
		$data = M ("Data");
		$data->where(" date='".$dDate."'")->delete();
		$log = A ("Log");
		$d = array();
		$d["date"] = $dDate;
		$aSite = C("site");	
		for($i=0;$i<count($aSite);$i++)
		{
			$d["source"] = $aSite[$i];
			$d["get_num"] = $log->getCountByDay($dDate,$aSite[$i],2);
			$d["get_one"] = $log->getCountByDay($dDate,$aSite[$i],1);
			$d["get_more"] = $log->getCountByDay($dDate,$aSite[$i],0);
			$d["visit"] = $log->getVisitByDay($dDate,$aSite[$i],2);
			$data->add($d);
		}
		echo "OK";
		//$this->display("a");
	}
	
	
	
	function TelData(){
		header("Content-type:text/html;charset=utf-8");
		$dDate = date("Y-m-d");
		$sFileName = "./Log/tel_".$dDate.".txt";
		$fp = file($sFileName);
		$j=0;
		for($i=0; $i<count($fp);$i++){
			$r = explode("#", $fp[$i]);
			if($r[2] <= 0) {
				$j++;
				$a = unserialize($r[3]);
				$site = $r[1];
				if(in_array($site,C("ls")))
					$siteName = "ls";
				elseif (in_array($site,C("zf")))
				$siteName = "zf";
				else
					$siteName = "28";
				echo $i."----".$siteName."----".$r[1]."----( ".$r[2]." )----".$a["user_name"]."----".$a["phone"]."----".$a["address"]."<br/>";
			}
		}
		echo $j ." / ".count($fp);
	}
}