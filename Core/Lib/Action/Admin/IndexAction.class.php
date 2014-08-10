<?php
/**
 * 系统管理后台入口
 * 
 */
class IndexAction extends AdminAction {
    public function _initialize() {
        parent::_initialize();  //RBAC 验证接口初始化
    }

    public function index(){
        $username = session('username');    // 用户名
        $kfname = session('kfname');//客服的姓名
        $roleid   = session('roleid');      // 角色ID
        if($username == C('SPECIAL_USER')){     //如果是无视权限限制的用户，则获取所有主菜单
            $sql = 'SELECT `id`,`title` FROM `node` WHERE ( `status` =1 AND `display`=1 AND `level`<>1 ) ORDER BY sort DESC';
        }else{  //更具角色权限设置，获取可显示的主菜单
            $sql = "SELECT `node`.`id` as id,`node`.`title` as title FROM `node`,`access` WHERE `node`.id=`access`.node_id AND `access`.role_id=$roleid  AND  `node`.`status` =1 AND `node`.`display`=1 AND (`node`.`level` =0 OR `node`.`level` =2)  ORDER BY `node`.sort DESC";
        }
        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
        $main_menu = $Model->query($sql);
        $this->assign('main_menu',$main_menu);
        $this->display();
    }
    
    public function left(){
        $pid = $this->_get('id',intval,0);    //选择子菜单
        $NodeDB = D('Node');
        $datas = $this->left_child_menu($pid);
        $parent_info = $NodeDB->getNode(array('id'=>$pid),'title');
        $sub_menu_html = '<dl>'; 
        foreach($datas as $key => $_value) {
            $sub_array = $this->left_child_menu($_value['id']);
            $sub_menu_html .= "<dt><a target='_self' href='#' onclick=\"showHide('{$key}');\">{$_value[title]}</a></dt><dd><ul id='items{$key}'>";
            if(is_array($sub_array)){
                foreach ($sub_array as $value) {
                    $href = empty($value['data']) ? 'javascript:void(0)' : $value['data'];
                    $sub_menu_html .= "<li><a id='a_{$value[id]}' onClick='sub_title({$value[id]})' href='{$href}'>{$value[title]}</a></li>";
                }
            }
          $sub_menu_html .=  '</ul></dd>';
        }
        $sub_menu_html .= '</dl>';

        $this->assign('sub_menu_title',$parent_info['title']);
        $this->assign('sub_menu_html',$sub_menu_html);
        $this->display();

    }

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID  
     * @param integer $with_self  是否包括他自己
     */
    private function left_child_menu($pid, $with_self = 0) {
        $pid = intval($pid);

        $username = session('username');    // 用户名
        $roleid   = session('roleid');      // 角色ID
        if($username == C('SPECIAL_USER')){     //如果是无视权限限制的用户，则获取所有主菜单
            $sql = "SELECT `id`,`data`,`title` FROM `node` WHERE ( `status` =1 AND `display`=2 AND `level` <>1 AND `pid`=$pid ) ORDER BY sort DESC";
        }else{
            $sql = "SELECT `node`.`id` as `id` , `node`.`data` as `data`, `node`.`title` as `title` FROM `node`,`access` WHERE `node`.id = `access`.node_id AND `access`.role_id = $roleid AND `node`.`pid` =$pid AND `node`.`status` =1 AND `node`.`display` =2 AND `node`.`level` <>1 ORDER BY `node`.sort DESC";
        }
        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
        $result = $Model->query($sql);

        if($with_self) {
            $NodeDB = D('Node');
            $result2[] = $NodeDB->getNode(array('id'=>$pid),`id`,`data`,`title`);
            $result = array_merge($result2,$result);
        }
        return $result;
    }
    
    public function top(){
        $this->display();
    }   
    
    public function main(){
        $this->display();
    }

    public function footer(){
        $this->display();
    }
	
}