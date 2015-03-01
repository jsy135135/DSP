<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    class BBSAction extends OQAction{
        public function index(){
            $BBS = M("BBS");
            $aList = $BBS->where(1)->select();
            $this->assign("aList",$aList);
            $this->display();
        }
        public function tobeijing(){
          $d = M("data_dealed");
          $sql = "SELECT site,count(*) as total  FROM `data_dealed` WHERE status > 0 AND addDate >= '2014-04-01' AND addDate <= '2014-04-30' group by site";
          $data = $d->query($sql);
          var_dump($data);
        }
        public function shuju(){
          $g = M("guestbook");
          $sql = "select address from guestbook where add_date = '2014-11-06'";
          $ssql = "select address from guestbook where add_date = '2014-11-07'";
          $datao = $g->query($sql);
          $datan = $g->query($ssql);
          // var_dump($datao);
          $domindatao = array();
          $domindatan = array();
          foreach ($datao as  $value) {
            // var_dump($value);
            $urlinfo = parse_url($value["address"]);
            $domin = $urlinfo["host"];
            array_push($domindatao , $domin);
          }
          foreach ($datan as  $valuen) {
            // var_dump($value);
            $urlinfon = parse_url($valuen["address"]);
            $dominn = $urlinfon["host"];
            array_push($domindatan , $dominn);
          }
          $dominold = array_count_values($domindatao);
          var_dump($dominold);
          $dominold = ksort($dominold,SORT_STRING);
          var_dump($dominold);
          $dominnew = array_count_values($domindatan);
          $dominnew = ksort($dominnew);
          foreach ($dominold as $key => $value) {
            echo $key."&nbsp;&nbsp;".$value.'<br />';
          }
          echo '========================================================'.'<br />';
          foreach ($dominnew as $key => $value) {
            echo $key."&nbsp;&nbsp;".$value.'<br />';
          }
          // var_dump($dominold);
          // var_dump($domindatao);
          // $domindatao = array_unique($domindatao);
          // $domindatao = array_values($domindatao);
          // $domindatan = array_unique($domindatan);
          // $domindatan = array_values($domindatan);
          // $new = array_diff($domindatao,$domindatan);
          // $new2 = array_diff($domindatan,$domindatao);
          // var_dump($domindatao);
          // var_dump($domindatan);
          // var_dump($new);
          // var_dump($new2);
        }
    }
