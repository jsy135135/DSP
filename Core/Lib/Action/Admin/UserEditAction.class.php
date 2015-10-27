<?php
/*
 * 用户管理模块
 * 
 */
    class UserEditAction extends OQAction{
        public function index(){
            import('ORG.Util.Page');// 导入分页类
            $role = M('Role')->getField('id,name');
            $map = array();
            $UserDB = D('User');
            $count = $UserDB->where($map)->count();
            $Page       = new Page($count);// 实例化分页类 传入总记录数
            // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
            $nowPage = isset($_GET['p'])?$_GET['p']:1;
            $show       = $Page->show();// 分页显示输出
            $list = $UserDB->where($map)->order('id ASC')->page($nowPage.','.C('web_admin_pagenum'))->select();
            $this->assign('role',$role);
            $this->assign('list',$list);
            $this->assign('page',$show);// 赋值分页输出
            $this->display();
        }
        public function edit(){
            $UserDB = D("User");
           if(isset($_POST['dosubmit'])) {
               $username = $_POST['username'];
               $password = $_POST['password'];
               $repassword = $_POST['repassword'];
               if(!empty($password) || !empty($repassword)){
                   if($password != $repassword){
                       $this->error('两次输入密码不一致！');
                   }
                   $_POST['password'] = md5($password);
               }

               if(empty($password) && empty($repassword)) unset($_POST['password']);   //不填写密码不修改

               //根据表单提交的POST数据创建数据对象
               if($UserDB->create()){
                   if($UserDB->save()){
                       $where['user_id'] = $_POST['id'];
                       $data['role_id'] = $_POST['role'];
                       M("RoleUser")->where($where)->save($data);
                       $this->assign("jumpUrl",U('/Admin/User/index'));
                       $this->success('编辑成功！');
                   }else{
                        $this->error('编辑失败!');
                   }
               }else{
                   $this->error($UserDB->getError());
               }
           }else{
               $id = $this->_get('id','intval',0);
               if(!$id)$this->error('参数错误!');
               $role = D('Role')->getAllRole(array('status'=>1),'sort DESC');
               $info = $UserDB->getUser(array('id'=>$id));
               $this->assign('tpltitle','编辑');
               $this->assign('role',$role);
               $this->assign('info',$info);
               $this->display('add');
           }
        }
    }
