<?php

//通过PHPRPC方式发送到各媒体平台的方法
class SendDataModel extends Model {

    //发送到连锁网
    public function sendToLS($aData, $sanfang = 0) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $client = new PHPRPC_Client ();
        $client->useService('http://www.liansuo.com/index.php?opt=gbinf'); //接口地址
        #ls网和52系统对应的参数
        $ls_manager_user = array(
            array('uid' => '197', 'trueName' => '韦韬'),
            array('uid' => '198', 'trueName' => '801'),
            array('uid' => '199', 'trueName' => '802'),
            array('uid' => '200', 'trueName' => '803'),
            array('uid' => '201', 'trueName' => '805'),
            array('uid' => '202', 'trueName' => '806'),
            array('uid' => '203', 'trueName' => '807'),
            array('uid' => '204', 'trueName' => '808'),
            array('uid' => '205', 'trueName' => '809'),
            array('uid' => '206', 'trueName' => '810'),
            array('uid' => '207', 'trueName' => '811'),
            array('uid' => '208', 'trueName' => '812'),
            array('uid' => '209', 'trueName' => '813'),
            array('uid' => '210', 'trueName' => '814'),
            array('uid' => '211', 'trueName' => '815'),
            array('uid' => '212', 'trueName' => '816'),
            array('uid' => '213', 'trueName' => '817'),
            array('uid' => '214', 'trueName' => '818'),
            array('uid' => '215', 'trueName' => '819'),
            array('uid' => '216', 'trueName' => '820'),
            array('uid' => '217', 'trueName' => '821'),
            array('uid' => '218', 'trueName' => '822'),
            array('uid' => '219', 'trueName' => '823'),
            array('uid' => '220', 'trueName' => '824'),
            array('uid' => '221', 'trueName' => '825'),
            array('uid' => '222', 'trueName' => '826'),
            array('uid' => '223', 'trueName' => '827'),
            array('uid' => '224', 'trueName' => '828'),
            array('uid' => '225', 'trueName' => '829'),
            array('uid' => '226', 'trueName' => '830'),
            array('uid' => '243', 'trueName' => '804')
        );
        //进行数组遍历，对应相应的用户ID
        foreach ($ls_manager_user as $value) {
            if ($aData['uid'] == $value['trueName']) {
                $owner = $value['uid'];
                break;
            }
        }
        $aNewData = array(
            // 结构如下
            'typeofcontact' => 1, //1为留言2为400电话，直接触发企业电话
            'memberid' => $aData["project_id"], //项目id [必填]
            'trueName' => $aData["user_name"], //真是姓名[必填]
            'mobile' => $aData["phone"], //手机号码[必填]
            'ip' => $aData["ips"], //IP地址[必填]
            'content' => $aData["content"], //留言内容 [必填]
            'owner' => $owner, //
            'address' => $aData["address"],
        );
        sendAgain:
        if ($sanfang == 1) {
            $state_new = $client->clientSend($aNewData, 'utf-8', 'calltoliansuo', 'liansuo67867587');
        } else {
            $state_new = $client->clientSend($aNewData, 'utf-8', 'callfrommobile ', 'call$%^mobile');
        }
        #进行记录，查看报错信息的格式
        $sFileName = "./Log/ls_cuowu.txt";
        $fp = fopen($sFileName, "a+");
        fwrite($fp, date("Y-m-d H:i:s") . "#" . "项目ID：" . $aData["project_id"] . "#" . $aData["phone"] . "#" . $aData["uid"] . "#" . $state_new . "\n");
        fclose($fp);
//        if (in_array($state_new, array('1:Illegal HTTP server.', '500:Internal Server Error'))) {
        if(!is_numeric($state_new)){
            goto sendAgain;
        }
        return $state_new;
    }

    //发送到28商机网
    public function sendTo28($aData) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $rpc_client = new PHPRPC_Client();
        $rpc_client->setProxy(NULL);
        $rpc_client->useService('http://super.28.com/soap/server.php');  //接口地址
        $rpc_client->setKeyLength(1024);
        $rpc_client->setEncryptMode(3);

        // 推荐使用以上方法，可以减少出错的频率
        // 结构如下 telephone mobile可相同,email postcode可为空，时间请取准确时间
        $bData = array(
            'projectID' => $aData["project_id"],
            'trueName' => $aData["user_name"],
            'email' => '',
            'telephone' => $aData["phone"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'postcode' => '',
            'addDate' => date("Y-m-d"),
            'addtime' => date("Y-m-d H:i:s"),
            'addHour' => date("H"),
            'content' => '对项目感兴趣' . $aData["content"],
            'ip' => $aData["ips"]
        );
        $state_new = $rpc_client->clientSend($bData, 'utf-8', 'xiancom', 'A342992b735');
        return $state_new;
    }

    //发送到致富网
    public function sendToZf($aData) {
        $bData = array(
            'projectID' => $aData["project_id"],
            'trueName' => $aData["user_name"],
            'email' => '',
            'telephone' => $aData["phone"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'postcode' => '',
            'addDate' => date("Y-m-d"),
            'addtime' => date("Y-m-d H:i:s"),
            'addHour' => date("H"),
            'content' => '对项目感兴趣' . $aData["content"],
            'ip' => $aData["ips"]
        );
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $rpc_client = new PHPRPC_Client();
        $rpc_client->setProxy(NULL);
        $rpc_client->useService('http://saas.zhifuwang.cn/soap/server1.php'); //接口地址
        //$rpc_client->setKeyLength(1024);
        //$rpc_client->setEncryptMode(3);
        // 推荐使用以上方法，可以减少出错的频率
        // 结构如下 telephone mobile可相同,email postcode可为空，时间请取准确时间
        $state_new = $rpc_client->clientSend($bData, 'utf-8', 'liansuotozfw', 'zfwLianSuo2012@$^'); //向致富发送
        return $state_new;
    }

    //发送到致富网(属于转接类型)
    public function sendToZF_t($aData) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $client = new PHPRPC_Client('http://saas.zhifuwang.cn/soap/server_dhb.php');
        $aData = array(
            // 结构如下
            'projectID' => $aData["project_id"],
            'trueName' => $aData["user_name"],
            'email' => '',
            'telephone' => $aData["phone"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'postcode' => '',
            'addDate' => date("Y-m-d"),
            'addtime' => date("Y-m-d H:i:s"),
            'addHour' => date("H"),
            'content' => '已经电话联系' . $aData["content"],
            'ip' => $aData["ips"]
        );
        $state_new = ($client->clientSend($aData, 'utf-8', 'liansuotozfw', 'zfwLianSuo2012@$^'));
        return $state_new;
    }

    //发送到王牌28网
    public function sendTowp($aData) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $bData = array(
            // 结构如下
            'projectID' => $aData["project_id"],
            'trueName' => $aData["user_name"],
            'email' => '',
            'telephone' => $aData["phone"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'postcode' => '',
            'addDate' => date("Y-m-d"),
            'addtime' => date("Y-m-d H:i:s"),
            'addHour' => date("H"),
            'content' => '对项目感兴趣' . $aData["content"],
            'ip' => $aData["ips"]
        );
        $client = new PHPRPC_Client();
        $client->useService('http://super.wp28.com/soap/server.php'); //接口地址
        $state_new = $client->clientSend($bData, 'utf-8', 'dl_dsp', 'ef2084a02f69a8');
        return $state_new;
    }

    //发送到91加盟网
    public function sendTo91($aData) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $client = new PHPRPC_Client();
        $client->setProxy(NULL);
        $client->useService('http://800.91jmw.com/index.php/welcome/pRpc');
//         $client->useService('http://192.168.200.61/kingboneguestbook/index.php/welcome/pRpc');
        $client->setKeyLength(1000);
        $client->setEncryptMode(3);
        $client->setCharset('UTF-8');
        $client->setTimeout(20);
        $bData = array(
            'p' => $aData["project_id"],
            'name' => $aData["user_name"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'content' => '对项目感兴趣' . $aData["content"],
            'ip' => $aData["ips"],
//        's_url' => 'dsp',
        );
//         var_dump($bData);
        //                die();
        $Rarray = $client->hi('2014KB', $bData);
        $state_new = $Rarray["result"];
//        return $Rarray['msg'];
        //                var_dump($state_new);
        return $state_new;
    }

}
