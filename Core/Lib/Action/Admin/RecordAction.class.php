<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    class RecordAction extends OQAction{
        public function index($date = null,$site = null){
//            var_dump($_REQUEST);
//            die();
            $date = $_REQUEST["date"] == "" ?  date("Y-m-d") : $_REQUEST["date"];
            $site = $_REQUEST["site"] == "" ?  28 : $_REQUEST["site"];
            $D = M("data_dealed");
            $list = $D->query("select project.name as pname,data_dealed.* from data_dealed left join project on data_dealed.projectID = project.projectID AND data_dealed.site = project.site where data_dealed.addDate= '".$date."' AND data_dealed.site = '".$site."' AND data_dealed.status > 0");
            $listcount = count($list);
//            echo '<pre>';
//            var_dump($list);
            for($i=0;$i<$listcount;$i++){
                $phone = $list[$i]["phone"];
                $list[$i]["yuyin"] = "/file_path.php?date=$date&&tel=$phone";
            }
            $this->assign("date",$date);
            $this->assign("listcount",$listcount);
            $this->assign("list",$list);
            $this->display();
        }
    }
