<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * Time:2014/07/24
 * By siyuan
 * 获取子分类行业内的项目详细信息
 */

class HYAction extends Action {

    public function index() {
        $project = M('project');
        $data = $project->where("subCat = '女装'")->select();
        $count = count($data);
        echo '<table boder = "1">';
        for($i=0;$i<=$count;$i++){
            echo '<tr>';
            echo '<td>'.$data[$i]['projectID'].'</td>';
            echo '<td>'.$data[$i]['name'].'</td>';
            echo '<td>'.$data[$i]['status'].'</td>';
            echo '<td>'.$data[$i]['site'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

}
