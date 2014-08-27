<?php

/*
 * 一段时间内的相关留言信息的导出~~~
 * 特定类别
 *
 */

class OutexAction extends Action {

    public function index() {
        $g = M("guestbook");
        $dDate = date("Y-m-d", strtotime("3 days ago"));
//            $SQL = "SELECT phone,province,project_id,site FROM `guestbook` WHERE `add_date` <= '2013-12-31'LIMIT $begin,$totol";
        $data = $g->where("project_id = 0 AND u_id=0 AND add_date>='" . $dDate . "'AND deal_time='0000-00-00 00:00:00'")->select();
//                 var_dump($data);
//                 die();
        $datacount = count($data);
//            echo $datacount;
//            die();
        require_once APP_PATH . 'PHPExcel/PHPExcel.php';
        require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
        $objPHPExcel = new PHPExcel();
//            echo 1;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 姓名);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 电话);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 手机);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, Email);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . 1, 行业);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . 1, 商家);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . 1, 媒体);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . 1, 省市);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . 1, 备注);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . 1, "List Id");
        for ($i = 0; $i < $datacount; $i++) {
//                echo 2;
            $j = $i + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["phone"]);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["phone"]);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $j, '');
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $lasttimes = $data[$datacount-1]['times'];
        $lasttimes = str_replace(' ','-',$lasttimes);
        $lasttimes = str_replace(':','-',$lasttimes);
//        var_dump($lasttimes);
//        var_dump($lasttimes);
//        die();
        $objWriter->save('./Outex/'.$lasttimes.'.csv');
//        var_dump($filename);
//        die();
        echo '导出成功';
//        echo '文件名为:'.;
        header("Location:./Outex/".$lasttimes.'.csv');
    }
}
