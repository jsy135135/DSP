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
    }
