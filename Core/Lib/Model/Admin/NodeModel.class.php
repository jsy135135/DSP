<?php 
class NodeModel extends Model {

	//自动验证
	protected $_validate=array(
		array('title','require','菜单名称必须！',1,'',3),
		array('name','require','节点名称必须！',1,'',3),
	);

	// 获取所有节点信息
	public function getAllNode($where = '' , $order = 'sort DESC') {
		return $this->where($where)->order($order)->select();
	}

	// 获取单个节点信息
	public function getNode($where = '',$field = '*') {
		return $this->field($field)->where($where)->find();
	}

	// 删除节点
	public function delNode($where) {
		if($where){
			return $this->where($where)->delete();
		}else{
			return false;
		}
	}

	// 更新节点
	public function upNode($data) {
		if($data){
			return $this->save($data);
		}else{
			return false;
		}
	}

	// 子节点
	public function childNode($id){
		return $this->where(array('pid'=>$id))->select();
	}

}