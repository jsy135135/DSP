<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CxAction extends OQAction {

    public function index() {
        $g = M("guestbook");
        $data = $g->query("SELECT *  FROM `guestbook` WHERE `add_date` >='2014-08-01' AND `project_id` = 7583 AND `site`='28'");
//        $count = count($Data);
//        echo $count;
//        $data = $g->where("site = 28 AND project_id = 7302 AND add_date = '2014-08-01'")->select();
//        var_dump($data);
        $datacount = count($data);
        for ($i = 0; $i < $datacount; $i++) {
            echo $data[$i]["phone"].'&nbsp&nbsp&nbsp&nbsp&nbsp'.$data[$i]["add_date"].'<br />';
        }
    }

}
