<?php

/*
 * 一段时间内的相关留言信息的导出~~~
 * 特定类别
 *
 */

class ExAction extends Action {

    public function index() {
        $project = M("guestbook");
        for ($a = 0; $a < 30; $a++) {
            if ($a == 0) {
                $begin = 0;
            } else {
                $begin = ($a * 10000);
            }
            $totol = 10000;
            $shu = ($a + 1);
            echo $shu;
            $SQL = "SELECT phone,province,project_id,site FROM `guestbook` WHERE `add_date` <= '2013-12-31'LIMIT $begin,$totol";
            $data = $project->query($SQL);
//                 var_dump($data);
//                 die();
            require_once APP_PATH . 'PHPExcel/PHPExcel.php';
            require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 号码);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 省市);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 项目ID);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, 来源网站);
            for ($i = 0; $i < $datacount; $i++) {
                $j = $i + 2;
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["phone"]);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["province"]);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["project_id"]);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $data[$i]["site"]);
            }
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save("./Ex/ex" . $shu . ".xls");
            echo '导出成功';
            $datacount = count($data);
            echo $datacount;
            echo '输出文件：' . $shu . '<br />';
        }
    }

    public function JSY() {
        $g = M("guestbook");
        //星期六儿童乐园的来源网址页面
        $address = array('http://wap.kxjh88.com/195_6_7207.html','http://m.liansuo.com/p/index/102480.html',)
        $data = $g->query("SELECT site,count(site) as t  FROM `guestbook` WHERE `add_date` <= '2013-12-31' group by site");
//        echo '<pre>';
//        var_dump($data);
        $datacount = count($data);
        for($i=0;$i<$datacount;$i++){
           echo $data[$i]['site'].'&nbsp&nbsp&nbsp&nbsp'.$data[$i]['t'].'<br />';
        }
    }
    #特殊客户的数据导出
        public function phone() {
            $project = M("guestbook");
                $SQL = "SELECT phone,address,project_id,site FROM `guestbook` WHERE `add_date` >= '2014-11-12' AND `deal_date` = '0000-00-00' ";
                $data = $project->query($SQL);

                require_once APP_PATH . 'PHPExcel/PHPExcel.php';
                require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 号码);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 省市);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 项目ID);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, 来源网站);
                for ($i = 0; $i < $datacount; $i++) {
                    $j = $i + 2;
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["phone"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["province"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["project_id"]);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $data[$i]["site"]);
                }
                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                $objWriter->save("./pro/ex".".xls");
                echo '导出成功';
                $datacount = count($data);
                echo $datacount;
                echo '输出文件：' . $shu . '<br />';
            }
        }

}
