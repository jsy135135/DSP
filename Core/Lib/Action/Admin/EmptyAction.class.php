<?php

 class EmptyAction extends Action{
        public function index(){
            // $cityName = MODULE_NAME;
            $this->to404($cityName);
        }
        protected function to404(){
            $this->display("Error:404");
//            echo '<script type="text/javascript" src="http://www.qq.com/404/search_children.js" charset="utf-8" homePageUrl="http://192.168.200.52" homePageName="回到我的主页"></script>';
//            redirect('./', 3, '此页面不存在！为您跳转到首页O(∩_∩)O~');
        }
    }