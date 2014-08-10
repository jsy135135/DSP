<?php
//客户信息的处理
class GuestbookAction extends OQAction{
	#一次外呼的数据展示
	function index(){
		//判断是否存在session值,不存在重新登录
		if(!isset($_SESSION['username'])){
			session(null);
            redirect(C('cms_admin').'?s=Admin/Login');
		}
		//TP判断session是否设置
		//session('?username');
		$uID = session("username");
		if($uID == "admin")
			$uID = 826; //默认外呼标记为826
		$dDate = date("Y-m-d", strtotime("2 days ago"));  //昨天1 days ago
		$TheDate = date("Y-m-d");
                $Thetime = date("Y-m-d H:i:s");
		$gb = M("guestbook");
		#查询未分配的数量
        $iNoAssignCount = $gb->where("project_id > 0 AND u_id=0 AND  add_date>='".$dDate."'")->count();
//                echo $iNoAssignCount;
		#查询此人当前已经分配条数
		$iAlreadyCount = $gb->where("project_id > 0 AND u_id = $uID AND add_date = '$TheDate'")->count();
		// $iAlreadyCount = 150;
		//判断此人还有没有未处理的留言
		$iHaveNotDeal = $gb->where("deal_status = 1 AND project_id > 0 AND u_id= '".$uID."' AND add_date>='".$dDate."'  AND deal_time='0000-00-00 00:00:00'")->count();
//                echo $iHaveNotDeal;
		//判断此人今天已经超过分配限额，则不再分配数据
		if($iAlreadyCount >= 100){
			$iNowNum = "已经达到今日分配限额";
                }else{
                    if ($iHaveNotDeal > 0) {
                            $aList = $gb->where("deal_status = 1 AND project_id > 0 AND u_id= ".$uID." AND add_date>='".$dDate."'  AND deal_time='0000-00-00 00:00:00'")->order("ids desc")->select();
                            $iNowNum = $iHaveNotDeal."有未处理信息，不再分配";
                    } else {
                            //本次分配的条数
                            $iNowNum = 30;
                            $aList = $gb->Distinct(true)->field('phone','ids')->where("project_id > 0  AND add_date>='".$dDate."' AND u_id=0")->limit($iNowNum)->order("ids asc")->select();
                            $idArray = "0";
                            $aListcount = count($aList);
                            for($i=0;$i<$aListcount;$i++){
                                    $idArray = $idArray.",".$aList[$i]["ids"];
                            }
                            //记录数据分配情况
                            $dDate = date("Y-m-d");
                            $sFileName = "./Log/FP-".$dDate.".txt";
                            $fp = fopen($sFileName, "a+");
                            fwrite($fp, date("Y-m-d H:i:s")."#"."分配到的数量：".$aListcount."#".$uID."#".$idArray."\n");
                            fclose($fp);
                            #对分配过的数据在guestbook表中进行标注
                            $aData["deal_status"] = 1;
                            $aData["u_id"] = $uID;
                            #记录每条数据的分配时间
                            $aData["Thetime"] = $Thetime;
                            $aData["Thedate"] = $TheDate;
                            $gb->where("ids in ($idArray)")->save($aData);
                            $aList = $gb->where( "deal_status = 1 AND project_id > 0 AND u_id= ".$uID." AND add_date>='".$dDate."' AND deal_time='0000-00-00 00:00:00'")->order("ids desc")->select();
                            $iNowNum = "本次分配".$aListcount."条";
                    }
                }
		$this->assign("uID", $uID);
		$this->assign("iNoAssignCount", $iNoAssignCount);
		$this->assign("nowAssignNum", $iNowNum);
		$this->assign("aList", $aList);
		$this->display();
	}

	/**
	 * 二次处理数据
	 */
	function callAgain(){
		$uID = session("username");
		if($uID == "admin")
			$uID = 826; //默认外呼标记为826
		//$dDate = date("Y-m-d", strtotime("2 days ago"));  //昨天1 days ago
		$dDate = $_REQUEST["date"] == "" ?  date("Y-m-d") : $_REQUEST["date"];
		// $dDate = "2014-05-01";
		$gb = M("guestbook");
		//所有的处理过，但返回状态不为0和7的，7表示不需要
		$iNoAssignCount = $gb->where("u_id=$uID AND deal_status<>0 and deal_status <>7 AND  add_date='".$dDate."' AND send_status ='' again=0")->count();
		//if($iNoAssignCount > 0)
			$aList = $gb->where("u_id=$uID AND deal_status<>0 and deal_status <>7 AND  add_date='".$dDate."' AND again=0 AND  send_status =''")->order(" ids asc ")->limit(100)->select();
		//else {
			//$gb->where("deal_status<>0 and deal_status <>7 AND  add_date<='".$dDate."' AND again=0 AND send_status<0")->limit(100)->setField("u_id",$uID);
			//$aList = $gb->where("u_id=$uID AND deal_status<>0 and deal_status <>7 AND  add_date>='".$dDate."' AND again=0 AND  send_status =''")->limit(100)->select();
		//}
		$this->assign("dDate",$dDate);
		$this->assign("uID", $uID);
		$this->assign("iNoAssignCount", $iNoAssignCount);
		$this->assign("nowAssignNum", $iNowNum);
		$this->assign("aList", $aList);
		$this->display();
	}


	/*
	 * 获取到单个项目的抓取数量
	 */
	function getNumByPID($iPID,$dDate,$sSite = "28"){
		$gb = M ("Guestbook");
		$iCount = $gb->where("project_id=".$iPID." AND site='".$sSite."' AND add_date='".$dDate."'")->count();
		return  $iCount;
	}



	/**
	 * 单独提交留言的form
	 */
	function gbForm(){
		$this->assign("isHF", $_REQUEST["hefei"]);
		$this->display();
	}

	/**
	 * 临时程序，更新deal_date
	 */
	function temp(){
		$gb = M("Guestbook");
		$aList = $gb->where("deal_date='0000-00-00' AND deal_time <>'0000-00-00 00:00:00'")->limit(1000)->getField("ids,deal_time");
		$i=0;
		foreach ($aList as $k => $r){
			$a = array();
			//$a["ids"] = $k;
			//$aData['deal_date'] = date("Y-m-d", strtotime($r));
			//echo "UPDATE `guestbook` SET `deal_date`='".date("Y-m-d", strtotime($r))."' WHERE ( ids=$k )" ;
			$gb->execute("UPDATE `guestbook` SET `deal_date`='".date("Y-m-d", strtotime($r))."' WHERE ( ids=$k ) ");
		//$ss = $gb->where("ids=".$k)->data($aData)->save();
			echo $i."--".$k."---".$r."--".$aData["deal_date"];
			$i++;
		}
		echo "完毕";
		// $this->display("");
	}
	/**
	 * 获取今天发送留言并且返回状态为负值的数据
	 * @return Ambigous <mixed, NULL, multitype:Ambigous <unknown, string> unknown , multitype:>
	 */
	function getDenyProjectList(){
		$gb = M ("Guestbook");
		$sBeginTime 	= date("Y-m-d")." 00:00:00";
		$sEndTime		= date("Y-m-d")." 23:59:59";
		$aList = $gb->where("deal_time BETWEEN '".$sBeginTime."' AND '".$sEndTime."' AND send_status < 0")->select();
		//此处用select方法，主要是getField方法没办法用好
		return $aList;
	}

	/**
	 * 根据电话号码选择信息
	 */
	function getByMobile(){
		$sMobile = $_REQUEST["mobile"];
		$gb = M("guestbook");
		$aInfo = $gb->where("phone = '$sMobile' AND project_id >0")->limit(1)->order("ids desc")->find();
//                echo '<pre>';
//                var_dump($aInfo);
		$sUrl = $aInfo["address"];
		if(substr($sUrl,0,4) <> "http")
			$sUrl = "http://".$sUrl;
		$aUrl = parse_url($sUrl);

		if(in_array($aUrl["host"], C("ls")))
		{
			if( $aUrl["host"] == "wap.liansuo.com"){
				$aPath = explode("/", $aUrl["path"]);
				$iPID = $aPath[3];
			} else{
				$aPath = explode("/", $aUrl["path"]);
				$iPID = str_replace(".html","", $aPath[3]);
			}
		}elseif(in_array($aUrl["host"], C("zf"))){
			$iPID = substr($aUrl["query"], strpos($aUrl["query"], "=")+1);
		}else{

			$iPID = str_replace(".html","", substr($aUrl["path"],strrpos($aUrl["path"],"_")+1));
		}
//			echo json_encode(array($aInfo["ips"],$iPID,$aUrl["host"]));
                        echo json_encode(array($aInfo["ips"],$iPID,$aInfo["site"]));
	}


	/**
	 * 处理url中的信息
	 * @param unknown $sUrl
	 */
	public function dealUrl(){
		$sUrl = $_REQUEST["url"];
		if(substr($sUrl,0,4) <> "http")
			$sUrl = "http://".$sUrl;
		$aUrl = parse_url($sUrl);
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
		echo json_encode(array($aUrl["host"], $iPID));
	}

	/**
	 * 内部发送数据，发送新鲜数据
	 */
	function sendDataByInter(){
		//判断是否存在session值,不存在重新登录
		if(!isset($_SESSION['username'])){
			session(null);
            redirect(C('cms_admin').'?s=Admin/Login');
		}
		//TP判断session是否设置
		//session('?username');
		$aData = array();
		$aData["user_name"] 	= $_REQUEST["username"];
		$aData["project_id"] 	= $_REQUEST["projectID"];
		$aData["phone"] 			= $_REQUEST["phone"];
		$aData["content"]       = $_REQUEST["content"];
		$aData["ips"] 				= $_REQUEST["ip"];
		$aData["address"] 		= $_REQUEST["address"];

		$sSite = $_REQUEST["website"];

		if($sSite == "ls"){
			$sSiteName = "ls";
			$iReturnID = $this->sendToLS($aData);
		} elseif($sSite == "zf"){
			$sSiteName = "zf";
			$iReturnID = $this->sendToZf($aData);
		}else
		{
			$sSiteName = "28";
			$iReturnID = $this->sendTo28($aData);
		}

		echo $sSiteName."\n".$iReturnID;
		$aD = array();
		//$aD["g_id"]			= $_REQUEST["guestbook_id"];
		$aD["name"] 		= $_REQUEST["username"];
		$aD["phone"]		= $_REQUEST["phone"];
		$aD["content"]      = $_REQUEST["content"];
		$aD["address"]	= $_REQUEST["address"];
		$aD["domain"]	= $_REQUEST["website"];
		$aD["projectID"]	= $_REQUEST["projectID"];
		$aD["site"]			= $sSiteName;
		$aD["ip"]				= $_REQUEST["ip"];
		$aD["status"]			= $iReturnID;
		$aD["addDate"]		= date("Y-m-d");
		$aD["u_id"]		= session("username");
                $ad["Thetime"]          = date("Y-m-d H:i:s");
		D("DataDealed")->add($aD);
//                    $aPprojectID = 7364;
//                    $aPsite = '28';
                #每日项目需求的计数及更改 计数BEGIN
//                if($iReturnID > 0){
//                    $aPprojectID = $_REQUEST["projectID"];
//                    $aPsite = $sSiteName;
//                    D("project")->where("projectID = '".$aPprojectID."' AND site = ".$aPsite."")->setDec('numbers');
//                    echo '所需数量减1';
//                #计数END
//                }
		// var_dump($_REQUEST);
		// echo '<br />';
		// var_dump($aD);
		// die();
		if($_REQUEST["again"]== 1) {
			$aT["ids"]	= $_REQUEST["guestbook_id"];
			$aT["again"] = 1;
			D("Guestbook")->save($aT);
		} else {
			$aData = array();
			$aData["send_status"] = $iReturnID;
			$aData["deal_status"] = 0;
			$aData["ids"] = $_REQUEST["guestbook_id"];
			$aData["deal_time"] = date("Y-m-d H:i:s");
			$aData["deal_date"] = date("Y-m-d");
			$guestbook = M("guestbook");
			$guestbook->save($aData);
		}
			//更新项目的状态，如果返回值为负值的话，更新发送状态为0，表示不能发送
		if($iReturnID < 0) {
			D("Project")->where("projectID=".$_REQUEST["projectID"]." AND site='".$sSiteName."'")->setField("sendStatus","0");
		}


	}

	/**
	 * 被拒绝的数据重新发送一次
	 */
	function sendDataByDeny(){
		//判断是否存在session值,不存在重新登录
		if(!isset($_SESSION['username'])){
			session(null);
            redirect(C('cms_admin').'?s=Admin/Login');
		}
		//TP判断session是否设置
		//session('?username');
		$aData = array();
		$aData["user_name"] 	= $_REQUEST["username"];
		$aData["project_id"] 	= $_REQUEST["projectID"];
		$aData["phone"] 			= $_REQUEST["phone"];
		$aData["ips"] 				= $_REQUEST["ip"];
		$aData["address"] 		= $_REQUEST["address"];


		echo "28\n";
		$iReturnID = $this->sendTo28($aData);
		echo $iReturnID;

		$aD["data_id"] 	= $_REQUEST["id"];
		$aD["project_id"]			= $_REQUEST["projectID"];
		$aD["site"]			= $_REQUEST["website"];
		$aD["status"]		= $iReturnID;
		$aD["add_date"]	= date("Y-m-d");
		$aD["add_time"]	= date("Y-m-d H:i:s");
		$aD["u_id"]		= session("username");
		if($aD["u_id"] <= 0)
			$aD["u_id"] = 100;
		$d = D("DataAgain");
		$d->add($aD);

		//修改本数据为已经处理
		$aT["id"]	= $_REQUEST["id"];
		$aT["again"] = 1;
		D("DataDealed")->save($aT);

		//更新项目的状态，如果返回值为负值的话，更新发送状态为0，表示不能发送
		if($iReturnID < 0) {
			D("Project")->where("projectID=".$_REQUEST["projectID"]." AND site='28'")->setField("sendStatus","0");
		}

	}

	/**
	 *
	 * @param unknown $aData
	 */
	function sendToLS($aData){
		import("phprpc_client","Core/Lib/Widget/",".php");
		$client = new PHPRPC_Client ();
		$client->useService ( 'http://www.liansuo.com/index.php?opt=gbinf' ); //接口地址
		$aNewData = array(
				// 结构如下
				'memberid' => $aData["project_id"],   //项目id [必填]
				'trueName'	=> $aData["user_name"],    //真是姓名[必填]
				'mobile'	=> $aData["phone"],//手机号码[必填]
				'ip'		=> $aData["ips"] ,//IP地址[必填]
		);
		$state_new = $client->clientSend($aNewData,'utf-8','callfrommobile ','call$%^mobile');
		return $state_new;
	}

	/**
	 *
	 * @param unknown $aData
	 */
	function sendTo28($aData) {
		import("phprpc_client","Core/Lib/Widget/",".php");
		$rpc_client = new PHPRPC_Client();
		$rpc_client->setProxy(NULL);
		$rpc_client->useService('http://super.28.com/soap/server.php');		//接口地址
		$rpc_client->setKeyLength(1024);
		$rpc_client->setEncryptMode(3);

		// 推荐使用以上方法，可以减少出错的频率

		// 结构如下 telephone mobile可相同,email postcode可为空，时间请取准确时间
		$bData = array(
				'projectID' => $aData["project_id"],
				'trueName'	=> $aData["user_name"],
				'email'		=> '',
				'telephone'	=> $aData["phone"],
				'mobile'	=> $aData["phone"],
				'address'	=> $aData["address"],
				'postcode'	=> '',
				'addDate'	=> date("Y-m-d"),
				'addtime'	=> date("Y-m-d H:i:s"),
				'addHour'	=> date("H"),
				'content'	=> '对项目感兴趣'.$aData["content"],
				'ip'		=> $aData["ips"]
		);
		$state_new = $rpc_client->clientSend($bData,'utf-8','xiancom','A342992b735');
		return $state_new;
	}

	/**
	 *
	 * @param unknown $aData
	 */
	function sendToZf($aData) {
		import("phprpc_client","Core/Lib/Widget/",".php");
		$rpc_client = new PHPRPC_Client();
		$rpc_client->setProxy(NULL);
		$rpc_client->useService ( 'http://saas.zhifuwang.cn/soap/server1.php' ); //接口地址
		//$rpc_client->setKeyLength(1024);
		//$rpc_client->setEncryptMode(3);

		// 推荐使用以上方法，可以减少出错的频率

		// 结构如下 telephone mobile可相同,email postcode可为空，时间请取准确时间
		$bData = array(
				'projectID' => $aData["project_id"],
				'trueName'	=> $aData["user_name"],
				'email'		=> '',
				'telephone'	=> $aData["phone"],
				'mobile'	=> $aData["phone"],
				'address'	=> $aData["address"],
				'postcode'	=> '',
				'addDate'	=> date("Y-m-d"),
				'addtime'	=> date("Y-m-d H:i:s"),
				'addHour'	=> date("H"),
				'content'	=> '对项目感兴趣'.$aData["content"],
				'ip'		=> $aData["ips"]
		);

		$state_new = $rpc_client->clientSend ( $bData, 'utf-8', 'liansuotozfw', 'zfwLianSuo2012@$^' ); //向致富发送
		return $state_new;
	}


	/**
	 *
	 * @return number
	 */
	function dealStatus(){
		if($_GET["again"] == 1){
			$aData = array();
			$aData["ids"] = $_GET["id"];
			$aData["again"] = 1;
			return D("Guestbook")->save($aData);
		} else {
			$aData = array();
			$aData["deal_status"] = $_GET["s"];
			$aData["ids"] = $_GET["id"];
			$aData["deal_time"] = date("Y-m-d H:i:s");
			return D("Guestbook")->save($aData);
		}
	}


	function analysis(){
		$gb = M("guestbook");
		$iStartDate = strtotime("2013-12-08 00:00:00");
		$iEndDate = strtotime(date("Y-m-d H:i:s"));
		$j = 0;
		for($i = $iEndDate; $i>$iStartDate;){
			$dDate = date("Y-m-d",$i);
			$aList[$j]["date"] = $dDate;
			$aList[$j]["all"] = $gb->where("times LIKE '%$dDate%' ")->count("ids");
			$aList[$j]["28"] = $gb->where("times LIKE '%$dDate%' AND address LIKE  '%28.com%'")->count("ids");
			$aList[$j]["13828"] = $gb->where("times LIKE '%$dDate%' AND address LIKE  '%1382828.com%'")->count("ids");
			$aList[$j]["liansuo"] = $gb->where("times LIKE '%$dDate%' AND address LIKE  '%liansuo.com%'")->count("ids");
			$i = $i -3600*24;
			$j++;
		}
		$this->assign("aList", $aList);
		$this->display();
	}

	/**
	 * 系统定时自动发送数据到指定的服务器
	 */
	function autoSendData(){
		$dDate = "2014-04-27";

		//先检查今天的发送数据量，如果数据超过1000，则什么也不执行
		$dealed = M("DataDealed");
		$iCount = $dealed->where(" addDate = '".$dDate."'")->count();
		//echo $iCount;
		if($iCount <= 1000 && date("Y-m-d")== $dDate) {
			$aInfo = D("DataDealed")->where(" status <0 AND again=0")->order(" RAND()")->find();	//随机取一条数据吧@
			var_dump($aInfo);
			//$iNewProjectID = 7292;
			//获取新id的方法
			$pInfo = D("Project")->where(" projectID=".$aInfo["projectID"]." AND site='".$aInfo["site"]."'")->find();
			$iNewProjectID = D("Project")->where("pid=".$pInfo["pid"]." AND cid=".$pInfo["cid"]." AND status=1 AND sendStatus=1 AND projectID<>".$aInfo["projectID"]." AND site='28' ")->order(" RAND() ")->limit(1)->getField("projectID");
			$sPostStr = "id=".$aInfo["id"]."&username=".$aInfo["name"]."&projectID=".$iNewProjectID."&phone=".$aInfo["phone"]."&ip=".$aInfo["ip"]."&address=".$aInfo["address"]."&website=28";
			if($iNewProjectID > 0)
				echo request_by_curl("http://192.168.200.52/tpcms/index.php?s=/Admin/Guestbook/sendDataByDeny", $sPostStr);
			else {
				$iNewProjectID = D("Project")->where("pid=".$pInfo["pid"]." AND status=1 AND sendStatus=1 AND projectID<>".$aInfo["projectID"]." AND site='28' ")->order(" RAND() ")->limit(1)->getField("projectID");
				$sPostStr = "id=".$aInfo["id"]."&username=".$aInfo["name"]."&projectID=".$iNewProjectID."&phone=".$aInfo["phone"]."&ip=".$aInfo["ip"]."&address=".$aInfo["address"]."&website=28";
				if($iNewProjectID > 0)
				echo request_by_curl("http://192.168.200.52/tpcms/index.php?s=/Admin/Guestbook/sendDataByDeny", $sPostStr);
			}
		}
		$this->display("blank");
	}
}