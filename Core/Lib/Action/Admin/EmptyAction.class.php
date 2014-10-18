<?php

 class EmptyAction extends Action{
        public function index(){
             $cityName = MODULE_NAME;
            $this->to404($cityName);
        }
        protected function to404($name){
            redirect('./Login', 3, '此页面不存在！为您跳转到首页O(∩_∩)O~');
        }
    }