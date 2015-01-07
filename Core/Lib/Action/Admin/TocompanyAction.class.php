<meta charset="utf-8"/>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TocompanyAction extends Action {

    public function import() {
        $industry = M('industry', 'vtiger_', 'mysql://root:anlaigz@192.168.200.144:3306/crm_default');
        $company = M('company', 'vtiger_', 'mysql://root:anlaigz@192.168.200.144:3306/crm_default');
        require_once APP_PATH . 'PHPExcel/PHPExcel.php';
        require_once APP_PATH . 'PHPExcel/PHPExcel/IOFactory.php';
        $file = "./ls.xls";
        $data = excelToArray($file);
        $data = array_values($data);
//        var_dump($data);
        $datacount = count($data);
//        $ndata = array();
        for($i=0;$i<$datacount;$i++){
//            echo $data[$i][2];
            $hdata["industry"] = $data[$i][1];
            $hdata["presence"] = 1;
            $rs = $industry->add($hdata);
            $cdata["industryid"] = $rs;
            $cdata["company"] = $data[$i][1];
            $cdata["presence"] = 1;
//            $cdata[$i]["proname"] = $data[$i][1];
            preg_match("/\d+/", $data[$i][2],$match);
            $cdata["phone1"] = $match[0];
            $company->add($cdata);
        }
//              var_dump($cdata);
        echo "end";
    }
}
