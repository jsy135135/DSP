<?php

class ChouchaAction extends OQAction {

    public function index($date = 'date("Y-m-d")') {
        $date = $_REQUEST["date"] == "" ? date("Y-m-d") : $_REQUEST["date"];
        $datefile = str_replace("-", "", $date);
        // var_dump($_REQUEST);
        // echo $date;
        $uid = $_REQUEST["uid"] == "" ? $uid = 827 : $_REQUEST["uid"];
        // echo $uid;
        $dealdata = M("data_dealed");
        $project = M("project");
        $guestbook = M("guestbook");
        $list = $dealdata->where("addDate = '" . $date . "' AND u_id = '" . $uid . "' AND status > 0")->select();
        $listcount = count($list);
        for ($i = 0; $i < $listcount; $i++) {
            $list[$i]["projectname"] = $project->where("projectID = '" . $list[$i]["projectID"] . "'")->getField('name');
            if ($list[$i]["projectname"] == null) {

                $list[$i]["projectID"] = $guestbook->where("phone = '" . $list[$i]["phone"] . "'")->getField('project_id');
                $list[$i]["projectname"] = $project->where("projectID = '" . $list[$i]["projectID"] . "'")->getField('name');
            }
            $list[$i]["webpage"] = $project->where("projectID = '" . $list[$i]["projectID"] . "'")->getField('webPage');
            if ($list[$i]["projectname"] == null) {
                $list[$i]["webpage"] = $guestbook->where("phone = '" . $list[$i]["phone"] . "'")->getField('address');
            }
            if (strpos($list[$i]["webpage"], "http://") === false && strpos($list[$i]["webPage"], "liansuo") === false) {
                $list[$i]["webpage"] = 'http://tj.28.com' . $list[$i]["webpage"];
            }
            // $list[$i]["projectID"] = $projectID;
            // $list[$i]["webpage"] = $webpage;
            // $list[$i]["projectname"] = $projectname;
            $phone = $list[$i]["phone"];
            // $list[$i]["yuyin"] = "http://192.168.200.87/REC201405/$datefile/48--B-$phonefile---20140530095741.wav";
            $list[$i]["yuyin"] = "/ftp_tst.php?date=$date&&tel=$phone";
        }
        // echo '<pre>';
        // var_dump($list);
//			echo $datefile;
        $this->assign('date', $date);
        $this->assign('uid', $uid);
        $this->assign('listcount', $listcount);
        $this->assign('list', $list);
        $this->display();
    }

    public function lswx() {
        $dDate = $_REQUEST["date"] == "" ? $dDate = date("Y-m-d") : $_REQUEST["date"];
        $d = M("data_dealed");
        $Api = new ApiAction();
        $data = $Api->lsdspwx($dDate);
        $statuslist = $data["$dDate"];
        $statuslistcount = count($statuslist);
//        var_dump($statuslist);
        $uidlist = array();
        $lswx = M("lswx");
        for ($i = 0; $i < $statuslistcount; $i++) {
            $info = $d->where("status <> 0 AND status = '" . $statuslist[$i] . "' AND site = 'ls'")->select();
            $Info = $info[0];
            $lswx->add($Info);
            $uidlist[$i] = $Info;
        }
//        echo '<pre>';
//        var_dump($uidlist);
        $this->assign('count',$statuslistcount);
        $this->assign('date', $dDate);
        $this->assign('list', $uidlist);
        $this->display();
    }

}
