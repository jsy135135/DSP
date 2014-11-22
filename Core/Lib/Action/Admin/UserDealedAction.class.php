<?php
//话务信息的处理结果
class UserDealedAction extends OQAction{
    #已经排重
	public function index($date = 'date("Y-m-d")'){
		// $uID = session("username");
		// if($uID == "admin")
		// 	$uID = 826; //默认外呼标记为826
		// var_dump($_REQUEST);
		$date = $_REQUEST["date"] == "" ?  date("Y-m-d") : $_REQUEST["date"];
	    // $date = $_REQUEST["_URL_"]["3"];
		$dealed = D("data_dealed");
                $lswx = D("lswx");
		$again = D("data_again");
		$guestbook = D("guestbook");
		$user = D("user");
		// $date = date("Y-m-d", strtotime("1 days ago"));//前一天的数据
		//循环出多个用户名，然后依次进行数据的查询
		//选择当天的参与的UID
		$aUserList = $dealed->where("addDate =  '".$date."'AND u_id <> 0 AND u_id <>10086")->field(" DISTINCT u_id as uid")->order('u_id asc')->select();
		// echo count($aUserList);
		// var_dump($aUserList);
		// var_dump($aUserList[0]['uid']);
		//当天参与UID对应的客服姓名(remark)
		$aUserListcount = count($aUserList)-1;
		for ($j=0; $j<=$aUserListcount; $j++) {
			$i = $aUserList[$j]["uid"];
			$aList[$i]["uid"] = $i;
			// echo $i.'<br />';
			$aList[$i]["username"] = $user->where("username=".$i)->getField("remark");
                        $aList[$i]["section"] = $user->where("username=".$i)->getField("section");
                        $aList[$i]["aim"] = $user->where("username=".$i)->getField("aim");
			$aList[$i]["yunyin"] = $user->where("username=".$i)->getField("");
			// echo $aList[$i]["username"];
			//$aList[$i]["firstCallNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i")->count();	//首次外呼处理数
			$aList[$i]["firstCallNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id= '".$i."'")->count("DISTINCT phone");	//首次外呼处理数
			$aList[$i]["firstSendNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i")->count("DISTINCT phone");	//首次提交数
      $aList[$i]["transfer"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND transfer = '1' ")->count("DISTINCT phone"); //28转接数量
      $aList[$i]["transferOKNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND transfer = '1' AND regular = '1' ")->count("DISTINCT phone"); //28转接审核有效数
			$aList[$i]["firstSendOKNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND status>0")->count("DISTINCT phone");	//首次提交成功数
			$aList[$i]["firstSendOKRatio"] = round( $aList[$i]["firstSendOKNum"] / $aList[$i]["firstSendNum"],4) *100;	//首次提交成功比
			/**
			$aList[$i]["secondCallNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND again=1 ")->count();	//二次外呼处理数
			$aList[$i]["secondSendNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status <> ''  AND again=1")->count();	//二次提交数
			$aList[$i]["secondSendOKNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status>0  AND again=1")->count();	//二次提交成功数
			$aList[$i]["secondSendOKRatio"] = round( $aList[$i]["secondSendOKNum"] / $aList[$i]["secondSendNum"],4) *100;	//二次提交成功比
			 * **/
			$aList[$i]["againCallNum"] = $again->where("add_date =  '".$date."' AND u_id=$i")->count("DISTINCT data_id");
			$aList[$i]["againSubmitNum"] = $again->where("add_date =  '".$date."' AND u_id=$i")->count("DISTINCT  data_id");
			$aList[$i]["againSubmitOkNum"] = $again->where("add_date =  '".$date."' AND u_id=$i AND status > 0")->count("DISTINCT data_id");
                        $aList[$i]["lsSunbmitNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND status>0 AND site = 'ls'")->count("DISTINCT phone");
                        $aList[$i]["lswx"] = $lswx->where("addDate =  '".$date."' AND u_id=$i")->count();
                        if($aList[$i]["lsws"] == 0){
                            $aList[$i]["lswx"] == "没有数据";
                        }
                        $aList[$i]["lsSunbmitOK"] = round(($aList[$i]["lswx"]/$aList[$i]["lsSunbmitNum"]),4) * 100;
			//$aList[$i]["specialOkNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND status > 0 AND domain='wap.28.com'")->count();


		}
                $aList = array_values($aList);
                $aListjson = json_encode($aList);
//                echo $aListjson;
		 $aListcount = count($aList);
		// print_r($aList);
//                var_dump($aList);
                $this->assign('aListcount',$aListcount);
		$this->assign('username',$username);
		$this->assign('date',$date);
		$this->assign('aListjson',$aListjson);
		$this->display();
	}
        #没有排重
        public function Dindex($date = 'date("Y-m-d")'){
		// $uID = session("username");
		// if($uID == "admin")
		// 	$uID = 826; //默认外呼标记为826
		// var_dump($_REQUEST);
		$date = $_REQUEST["date"] == "" ?  date("Y-m-d") : $_REQUEST["date"];
	    // $date = $_REQUEST["_URL_"]["3"];
		$dealed = D("data_dealed");
                $lswx = D("lswx");
		$again = D("data_again");
		$guestbook = D("guestbook");
		$user = D("user");
		// $date = date("Y-m-d", strtotime("1 days ago"));//前一天的数据
		//循环出多个用户名，然后依次进行数据的查询
		//选择当天的参与的UID
		$aUserList = $dealed->where("addDate =  '".$date."'AND u_id <> 0 AND u_id <>10086")->field(" DISTINCT u_id as uid")->order('u_id asc')->select();
		// echo count($aUserList);
		// var_dump($aUserList);
		// var_dump($aUserList[0]['uid']);
		//当天参与UID对应的客服姓名(remark)
		$aUserListcount = count($aUserList)-1;
		for ($j=0; $j<=$aUserListcount; $j++) {
			$i = $aUserList[$j]["uid"];
			$aList[$i]["uid"] = $i;
			// echo $i.'<br />';
			$aList[$i]["username"] = $user->where("username=".$i)->getField("remark");
                        $aList[$i]["aim"] = $user->where("username=".$i)->getField("aim");
			$aList[$i]["yunyin"] = $user->where("username=".$i)->getField("");
			// echo $aList[$i]["username"];
			//$aList[$i]["firstCallNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i")->count();	//首次外呼处理数
			$aList[$i]["firstCallNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id= '".$i."'")->count("phone");	//首次外呼处理数
			$aList[$i]["firstSendNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i")->count("phone");	//首次提交数
			$aList[$i]["firstSendOKNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND status>0")->count("phone");	//首次提交成功数
			$aList[$i]["firstSendOKRatio"] = round( $aList[$i]["firstSendOKNum"] / $aList[$i]["firstSendNum"],4) *100;	//首次提交成功比
			/**
			$aList[$i]["secondCallNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND again=1 ")->count();	//二次外呼处理数
			$aList[$i]["secondSendNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status <> ''  AND again=1")->count();	//二次提交数
			$aList[$i]["secondSendOKNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status>0  AND again=1")->count();	//二次提交成功数
			$aList[$i]["secondSendOKRatio"] = round( $aList[$i]["secondSendOKNum"] / $aList[$i]["secondSendNum"],4) *100;	//二次提交成功比
			 * **/
			$aList[$i]["againCallNum"] = $again->where("add_date =  '".$date."' AND u_id=$i")->count("data_id");
			$aList[$i]["againSubmitNum"] = $again->where("add_date =  '".$date."' AND u_id=$i")->count(" data_id");
			$aList[$i]["againSubmitOkNum"] = $again->where("add_date =  '".$date."' AND u_id=$i AND status > 0")->count("data_id");
                        $aList[$i]["lsSunbmitNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND status>0 AND site = 'ls'")->count("phone");
                        $aList[$i]["lswx"] = $lswx->where("addDate =  '".$date."' AND u_id=$i")->count();
                        if($aList[$i]["lsws"] == 0){
                            $aList[$i]["lswx"] == "没有数据";
                        }
                        $aList[$i]["lsSunbmitOK"] = round(($aList[$i]["lswx"]/$aList[$i]["lsSunbmitNum"]),4) * 100;
			//$aList[$i]["specialOkNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND status > 0 AND domain='wap.28.com'")->count();


		}
		 $aListcount = count($aList);
		// print_r($aList);
                $this->assign('aListcount',$aListcount);
		$this->assign('username',$username);
		$this->assign('date',$date);
		$this->assign('aList',$aList);
		$this->display();
	}


	public function index_bak($date = 'date("Y-m-d")'){
		// $uID = session("username");
		// if($uID == "admin")
		// 	$uID = 826; //默认外呼标记为826
		// var_dump($_REQUEST);
		$date = $_REQUEST["date"] == "" ?  date("Y-m-d") : $_REQUEST["date"];
		// $date = $_REQUEST["_URL_"]["3"];
		$dealed = D("data_dealed");
		$again = D("data_again");
		$guestbook = D("guestbook");
		// $date = date("Y-m-d", strtotime("1 days ago"));//前一天的数据
		//循环出多个用户名，然后依次进行数据的查询
		//选择当天的参与的UID
		$aUserList = $guestbook->where("deal_date =  '".$date."' ")->field(" DISTINCT u_id as uid")->select();
		//print_r($aUserList);

		for ($j=0; $j<=count($aUserList); $j++) {
			$i = $aUserList[$j]["uid"];
			$aList[$i]["uid"] = $i;
			$aList[$i]["firstCallNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i")->count();	//首次外呼处理数
			$aList[$i]["firstSendNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status <> ''")->count();	//首次提交数
			$aList[$i]["firstSendOKNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status>0")->count();	//首次提交成功数
			$aList[$i]["firstSendOKRatio"] = round( $aList[$i]["firstSendOKNum"] / $aList[$i]["firstSendNum"],4) *100;	//首次提交成功比
			$aList[$i]["secondCallNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND again=1 ")->count();	//二次外呼处理数
			$aList[$i]["secondSendNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status <> ''  AND again=1")->count();	//二次提交数
			$aList[$i]["secondSendOKNum"] = $guestbook->where("deal_date =  '".$date."' AND u_id=$i AND send_status>0  AND again=1")->count();	//二次提交成功数
			$aList[$i]["secondSendOKRatio"] = round( $aList[$i]["secondSendOKNum"] / $aList[$i]["secondSendNum"],4) *100;	//二次提交成功比
			$aList[$i]["againCallNum"] = $again->where("add_date =  '".$date."' AND u_id=$i")->count();//二次外呼处理数
			$aList[$i]["againSubmitNum"] = $again->where("add_date =  '".$date."' AND u_id=$i")->count();//二次提交数
			$aList[$i]["againSubmitOkNum"] = $again->where("add_date =  '".$date."' AND u_id=$i AND status > 0")->count();//二次提交成功数
			$aList[$i]["specialOkNum"] = $dealed->where("addDate =  '".$date."' AND u_id=$i AND status > 0 AND domain='wap.28.com'")->count();//特殊通道处理数
		}

		//echo '<pre>';
		// print_r($aList);
		$this->assign('date',$date);
		$this->assign('aList',$aList);
		$this->display();
	}

	public function test(){
		$dDate= "2014-04-23";
		$aL = D("Guestbook")->where("deal_date='".$dDate."' AND send_status <> ''")->select();
		$aD = D("DataDealed")->where("addDate='".$dDate."'")->select();
		for($i=0;$i<count($aL);$i++){
			echo $aL[$i]["ids"]."\t".$aL[$i]["phone"]."\t".$aL[$i]["send_status"]."\t".$aL[$i]["u_id"]."----".$aD[$i]["phone"]."\t".$aD[$i]["sendStatus"]."\t".$aD[$i]["u_id"]."<br/>";
		}
		$this->display("index");
	}

}