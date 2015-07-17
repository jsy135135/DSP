<?php

/*
 * 400电话处理类
 * time:2015年6月23日19:49:49
 * By:siyuan
 */

class Phone400Action extends Action {

    private $call_usr = 'HDSX'; //用户名
    private $call_pwd = 'kingbone888'; //密码
    private $webservice_url = 'http://60.247.29.36/interface/apiservice.asmx?wsdl';
    /*
     * 类构造方法，可以用来初始化之后的一些参数，或者调用方法
     * time:2015年6月23日19:52:01
     * By:siyuan
     */

     function __construct(){
        parent::__construct();
        $this->lineTo400();
    }
    /*
     * 连接400数据接口，并进行初始化的一些相关操作
     * time:2015年6月23日19:45:36
     * By:siyuan
     */

    private function lineTo400() {
        $this->client = new SoapClient($this->webservice_url); //初始化soap
        $this->client->soap_defencoding = 'UTF-8'; //定义编码
        $this->client->decode_utf8 = false; //定义编码
        $this->client->xml_encoding = 'utf-8'; //定义编码 
        //定义soap头信息,密码请采用编码后字串    
        $auth = array('LoginName' => $this->call_usr, 'Pwd' => $this->call_pwd, 'RoleID' => 3);
        $authvalues = new SoapVar($auth, SOAP_ENC_OBJECT, 'Header', 'http://tempuri.org/');
        $header = new SoapHeader('http://tempuri.org/', "Header", $authvalues, true);
        $this->client->__setSoapHeaders(array($header));
    }
    //xml转化为数组
    private function _xmlToArray($simpleXmlElement){
        $simpleXmlElement = (array)$simpleXmlElement;
        foreach($simpleXmlElement as $k=>$v){
            if($v instanceof SimpleXMLElement || is_array($v)){
                $simpleXmlElement[$k] = $this->_xmlToArray($v);
            }
        }
        return $simpleXmlElement;
    }
    
    /*
     * 页面触发400电话方法，发出一个拨打400号码的请求
     * time:2015年6月23日19:47:40
     * By:siyuan
     * 
     */
    public function CallNumber($GroupGuid,$phoneNumber) {
        $parameters = array('guid' => $GroupGuid, 'callingnum' => $phoneNumber, 'exceptionInfo' => '呼叫失败');
        $result = $this->client->__call("Call", array('parameters' => $parameters));
//        $resultstr = $result->exceptionInfo;
        return $result;
    }
    /*
     * 获取商户的GUID号码，通过custid+
     * time:2015年6月24日13:47:03
     * By:siyuan
     */
    public function GetWorkGroupCus($custid){
        $parameters = array('custid'=>$custid,'status' => 1, 'exceptionInfo' => '');
        $result=$this->client->__call("GetWorkGroupCus_xml",array('parameters'=>$parameters));
        $rs = $result->GetWorkGroupCus_xmlResult;
        $str = '<?xml version="1.0" encoding="UTF-8" ?><log>'.$rs.'</log>';
        $arr = $this->_xmlToArray(simplexml_load_string($str));
        return $arr;
    }
    /*
     * 获取代理商的通话语额
     * time:2015年6月24日13:39:48
     * By:siyuan
     */
    public function GetAgentMoney() { 
        //以数组的形式赋值要调用的方法参数
        $parameters = array('MoneyType' => 1, 'exceptionInfo' => '');
        $result=$client->__call("GetAgentMoney",array('parameters'=>$parameters));
        
        var_dump($result);
    }

    /*
     * 获取商户的详细信息
     * time:2015年6月23日20:22:25
     * By:siyuan
     */

    public function GetCustomer() {  
        //以数组的形式赋值要调用的方法参数
        $parameters = array('CustID' => 0, 'exceptionInfo' => '');
        $result=$this->client->__call("GetCustomer_XML",array('parameters'=>$parameters));
        $rs = $result->GetCustomer_XMLResult;
        $str = '<?xml version="1.0" encoding="UTF-8" ?><log>'.$rs.'</log>';
        $arr = $this->_xmlToArray(simplexml_load_string($str));
        return $arr;
    }
    /*
     * 获取400电话呼叫明细（单独日期_XML）
     * time：2015年6月24日13:55:27
     * By:siyuan
     */
    public function Get400DayDetail($date){ 
        //以数组的形式赋值要调用的方法参数
        $parameters = array('dt'=>$date,'supplierID'=>'2467042','exceptionInfo' => '');
        $result=$this->client->__call("Get400DayDetail_xml",array('parameters'=>$parameters));
        $rs            = $result->Get400DayDetail_xmlResult;
        $str           = '<?xml version="1.0" encoding="UTF-8" ?><log>'.$rs.'</log>';
        $arr           = $this->_xmlToArray(simplexml_load_string($str));
        return $arr;
    }
    
    public function GetFreeDayDetail($date){ 
        //以数组的形式赋值要调用的方法参数
        $parameters = array('dt'=>$date,'startid'=>1,'supplierID'=>'2467042','exceptionInfo' => '');
        $result=$this->client->__call("GetFreeDayDetail_ID_xml",array('parameters'=>$parameters));
        $rs            = $result->GetFreeDayDetail_ID_xmlResult;
        $str           = '<?xml version="1.0" encoding="UTF-8" ?><log>'.$rs.'</log>';
        $arr           = $this->_xmlToArray(simplexml_load_string($str));
        return $arr;
    }
    public function Get400DayDetail_ID($date){ 
        //以数组的形式赋值要调用的方法参数
        $parameters = array('dt'=>$date,'startid'=>1,'supplierID'=>'2467042','exceptionInfo' => '');
        $result=$this->client->__call("Get400DayDetail_ID_xml",array('parameters'=>$parameters));
        $rs            = $result->Get400DayDetail_ID_xmlResult;
        $str           = '<?xml version="1.0" encoding="UTF-8" ?><log>'.$rs.'</log>';
        $arr           = $this->_xmlToArray(simplexml_load_string($str));
        return $arr;
    } 

}
