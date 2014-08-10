<?php
class LogAction extends OQAction {
	public function index(){
		echo "data";
	}
	
	public function log(){
		ini_set("memory_limit","-1");
		set_time_limit(0);
		//$fName = "C:/xampp/htdocs/m/m/Data/tj-2013-12-15.txt";
		$dDate = trim($_REQUEST["date"]);
		if(empty($dDate))
			$dDate = date("Y-m-d", strtotime("1 days ago"));  //昨天
		//$dDate = $_REQUEST["date"];
		$log = M ("log");
		$log->where(" add_date='".$dDate."'")->delete();
		$fName = "http://tongji.28.com/2013/tj-".$dDate.".txt";
		echo $fName;
		$fp = file($fName);
		for($i=0; $i<count($fp);$i++){
			$this->oneLog($fp[$i]);
			echo $i."\n";
		}
	}
	
	
	
	
	/**
	 * 
	 * @param unknown $sLog
	 */
	private function oneLog($sLog){
		$aLog = explode("#", $sLog);
		$aUrl = parse_url(trim($aLog[3]));
		if(in_array($aUrl["host"], C("ls")))
		{
			if( $aUrl["host"] == "wap.liansuo.com"){
				$aPath = explode("/", $aUrl["path"]);
				$iPID = $aPath[3];
			} else{
				$aPath = explode("/", $aUrl["path"]);
				$iPID = str_replace(".html","", $aPath[3]);
			}
			$site="ls";
			
		} elseif(in_array($aUrl["host"], C("zf"))) {
			$iPID = substr($aUrl["query"], strpos($aUrl["query"], "=")+1);
			$site="zf";
		} else {
			$iPID = str_replace(".html","", substr($aUrl["path"],strrpos($aUrl["path"],"_")+1));
			$site="28";
		}

		$aData["log_date"] 	= date("Y-m-d", strtotime($aLog[0]));
		$aData["ip"]				= $aLog[2];
		$aData["host"]			= $aUrl["host"];
		$aData["site"]			= $site;
		//$aData["project_id"] = intval($iPID); 
		$aData["project_id"] = $iPID;
		$aData["original_time"] = $aLog[0];
		$aData["original_url"] = $aLog[3];
		$log = M("Log");
		$log->add($aData);
		//$this->display();
	}
	
	function dataByMonth(){
		$iMonth = $_REQUEST["month"];
		$this->assign("month",$iMonth);
		$this->display();
	}
	
	/**
	 * 
	 */
	function getDataByMonth(){
		set_time_limit(0);
		$iMonth = $_REQUEST["month"];
		$iYear = "2013";
		//$iDays = date("t",$iYear.'-'.$iMonth."-00");
		$iDays= 20;
		$aList = array();
		for($i=0; $i< $iDays;$i++){
			$aList[$i]["day"] = date("Y-m-d", strtotime($iYear.'-'.$iMonth.'-'.$i));
			$aList[$i]["get_total"] = $this->getCountByDay($aList[$i]["day"],"total");
			$aList[$i]["visit_total"] = $this->getVisitByDay($aList[$i]["day"],"total");
			$aList[$i]["ratio_total"] = round($aList[$i]["get_total"] / $aList[$i]["visit_total"], 2) *100 ."%";
			$aList[$i]["visit_28"] = $this->getVisitByDay($aList[$i]["day"],"28");
			$aList[$i]["get_28"] = $this->getCountByDay($aList[$i]["day"],"28");
			$aList[$i]["ratio_28"] = round($aList[$i]["get_28"] / $aList[$i]["visit_28"], 2) * 100 ."%";
			$aList[$i]["visit_zf"] = $this->getVisitByDay($aList[$i]["day"],"zf");
			$aList[$i]["get_zf"] = $this->getCountByDay($aList[$i]["day"],"zf");
			$aList[$i]["ratio_zf"] = round($aList[$i]["get_zf"] / $aList[$i]["visit_zf"], 2) * 100 ."%";
			
			/**
			$aList[$i]["ls"] = $this->getCountByDay("ls",$aList[$i]["day"]);
			$aList[$i]["zf"] = $this->getCountByDay("zf",$aList[$i]["day"]);
			$aList[$i]["link"] = "<a href=/m/index.php/log/detail/id>查看</a>";**/
		}
		echo json_encode($aList);
	}
	
/**
 * 
 * @param unknown $dDate  时间
 * @param string $sMedia  来源媒体 total表示全部
 * @param number $sType  类型，2表示全部 1表示1vs1 0 表示1vs多
 * @return unknown
 */
	Public function getCountByDay( $dDate, $sMedia="total", $sType=2){
		if($sMedia <> "total")
			$sM = "site='".$sMedia."'";
		else $sM = "1";
		switch ($sType) {
			case 2:
				$sM .= "";
				break;
			case 1:
				$sM .= " AND project_id > 0";
				break;
			case 0:
				$sM .= " AND project_id =0";
				break;
			default:
				$sM .= "";
				break;
		}
		$gb = M ("guestbook");
		$iCount = $gb->where($sM." AND add_date= '".$dDate."'")->count("ids");
		return $iCount;
	}
	
	function getVisitByDay($dDate, $sMedia="total", $sType=2){
	if($sMedia <> "total")
			$sM = "site='".$sMedia."'";
		else $sM = "1";
		switch ($sType) {
			case 2:
				$sM .= "";
				break;
			case 1:
				$sM .= " AND project_id > 0";
				break;
			case 0:
				$sM .= " AND project_id =0";
				break;
			default:
				$sM .= "";
				break;
		}
		$log = M ("log");
		$iCount = $log->where($sM." AND log_date= '".$dDate."'")->count("id");
		return $iCount;
	}
	
	
	function getByDay(){
		$dDate = $_REQUEST["date"];
		//print_r($aList);
		$this->assign("dDate", $dDate);
		$this->assign("aList", $aList);
		$this->display();
	}
	
	function getByJson(){
		set_time_limit(0);
		$dDate = $_REQUEST["date"];
		$aPList = $this->getProjectList();
		$log = M ("Log");
		$gb = M ("Guestbook");
		$sSQL = "SELECT DISTINCT project_id,COUNT(project_id) AS num FROM log where log_date='$dDate' GROUP BY project_id";
		$aList =$log->query($sSQL);
		for ($i=0; $i< count($aList); $i++) {
			if ($aList[$i]["project_id"] > 0) {
				$k = $gb->where(" add_date = '.$dDate.' AND project_id=".$aList[$i]["project_id"])->count("ids");
				$aList[$i]["mobile"] = $k;
				$aList[$i]["projectName"] = $aPList[$aList[$i]["project_id"]]["name"];
				$aList[$i]["catName"] = $aPList[$aList[$i]["project_id"]]["catName"];
				$aList[$i]["mobile"] = $k;
				$aList[$i]["mobile"] = $k;
				$aList[$i]["ratio"] = round($k/$aList[$i]["num"], 4) * 100 ."%";
				
				unset($k);
			}
		}
		echo json_encode($aList);

	}
	
	function getProjectList() {
		header("Content-type: text/html; charset=utf-8") ;
		
		import("xmltoarray","Lib/Widget/",".php");
		$xml = new XmlToArray();
		$sXml = file_get_contents("C:/xampp/htdocs/m/m/Data/28.xml");
		$xml->setXml($sXml);
		$r = $xml->parseXml();
		$t = $r["log"]["fields"];
		$aNew = array();
		for($i=0; $i < count($t);$i++) {
			$iKey = $t[$i]["projectID"]["content"];
			$aNew[$iKey]["name"] =  $t[$i]["projectName"]["content"];
			$aNew[$iKey]["catName"] =  $t[$i]["catName"]["content"];
		}
		return $aNew;
		//print_r($aNew);
	}
	
	/**
	 * 
	 */
	function gbMapLog(){
		$dDate = "2013-12-15";
		$gb = M ("Guestbook");
		$log = M ("Log");
		$aList = $gb->where("add_date LIKE '%$dDate%'")->limit(10)->select();
		for($i=0; $i<=count($aList);$i++) {
			$ip = $aList[$i]["ips"];
			$url = "http://".$aList[$i]["address"];
			$vistTime = $aList[$i]["times"];
			$aData["status"] = 1;
			$aLog = $log->where("ip LIKE '%$ip%' AND original_url LIKE '%$url%'")->save($aData);
			//print_r($aLog);
		}
		print_r($aList);
		$this->display("test");
	}
	
	function test(){
		$this->oneLog("2013-12-11 00:00:00#Mozilla/5.0 (Linux; U; Android 4.1.1; zh-cn; X909 Build/JRO03L) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1 # 123.151.12.152#http://wap.mysj88.com/0_6_3996.html
				");
	}
	
	function tt(){
		echo "1";
	}
	
	
	public function log_temp(){

		ini_set("memory_limit","-1");
		set_time_limit(0);
		$fName = "D:/log/tj-2013-12-25.txt";
		$fp = file($fName);
		for($i=0; $i<count($fp);$i++){
			echo $this->showLog($fp[$i]);
			//echo $i."\n";
		}
	}
	
	
	private function showLog($sLog){
		$aLog = explode("#", $sLog);
		$aUrl = parse_url(trim($aLog[3]));
		if(in_array($aUrl["host"], C("ls")))
		{
			if( $aUrl["host"] == "wap.liansuo.com"){
				$aPath = explode("/", $aUrl["path"]);
				$iPID = $aPath[3];
			} else{
				$aPath = explode("/", $aUrl["path"]);
				$iPID = str_replace(".html","", $aPath[3]);
			}
			$site="ls";
				
		} elseif(in_array($aUrl["host"], C("zf"))) {
			$iPID = substr($aUrl["query"], strpos($aUrl["query"], "=")+1);
			$site="zf";
		} else {
			$iPID = str_replace(".html","", substr($aUrl["path"],strrpos($aUrl["path"],"_")+1));
			$site="28";
		}
		
		if(trim($aLog[3]) <=0)
		{
			echo $aLog[0]."#".$aLog[3]."<br>";
		}
		
	}
	
	
	
}