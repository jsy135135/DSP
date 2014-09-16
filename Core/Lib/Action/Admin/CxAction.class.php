<?php
//输出header头
header("content-type:text/html;charset=utf-8");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CxAction extends Action {

    public function index() {
        $g = M("guestbook");
        $data = $g->query("SELECT *  FROM `guestbook` WHERE `add_date` >='2014-08-01' AND `project_id` = 7583 AND `site`='28'");
//        $count = count($Data);
//        echo $count;
//        $data = $g->where("site = 28 AND project_id = 7302 AND add_date = '2014-08-01'")->select();
//        var_dump($data);
        $datacount = count($data);
        for ($i = 0; $i < $datacount; $i++) {
            echo $data[$i]["phone"] . '&nbsp&nbsp&nbsp&nbsp&nbsp' . $data[$i]["add_date"] . '<br />';
        }
    }
/*
 * 导出每天28成功发送的数据，详细信息，for buttom
 * TIME:2014-8-29 15:10:18
 * BY：siyuan
 * 
 */
    public function tel() {
        $d = M("data_dealed");
        $data = $d->query("SELECT p.catName,p.subCat,d.name,d.phone,d.Thetime FROM `data_dealed` as d left join project as p on d.projectID = p.projectID AND d.site = p.site WHERE d.site LIKE '28' AND d.status > '0' AND d.addDate >= '2014-08-27' order by p.catName asc,p.subCat asc,d.Thetime asc");
//        var_dump($data);
        $datacount = count($data);
        echo '<table>';
        for ($i = 0; $i < $datacount; $i++) {
            echo '<tr>';
            echo '<td>' . $data[$i]["catName"] . '</td><td>' . $data[$i]["subCat"] . '</td><td>' . $data[$i]["name"] . '</td><td>' . $data[$i]["phone"] . '</td><td>' . $data[$i]["Thetime"] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

}
