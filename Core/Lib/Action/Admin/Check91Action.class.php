<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Check91Action extends Action {
    public function index(){
        $d = M("data_dealed");
        $startDate = $_REQUEST["startDate"] == "" ? date("Y-m-d") : $_REQUEST["startDate"];
        $endDate = $_REQUEST["endDate"] == "" ? date("Y-m-d") : $_REQUEST["endDate"];
        $sql = "SELECT d.projectID,count(*) as count,p.name FROM `data_dealed` as d left join project as p on d.projectID = p.projectID AND d.site = p.site WHERE d.site LIKE '91' AND d.check = 1 AND d.regular = 1 AND d.addDate <= '".$endDate."' AND d.addDate >= '".$startDate."' group by d.projectID";
        $data = $d->query($sql);
//        var_dump($data);
        $this->assign("startDate" , $startDate);
        $this->assign("endDate" , $endDate);
        $this->assign("data" , $data);
        $this->display();
    }
}
