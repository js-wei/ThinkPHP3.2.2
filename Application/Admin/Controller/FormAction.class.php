<?php
class FormAction extends CommonAction{
	
	public function index(){
		
		$cid=I('coloumn_id');
		if($cid){
			//查询
	        $map = $this->_search();
	        $map['status']=array('neq',-1);

	        //排序
			$model=M($this->getActionName());
	        $ordermap = $this->ordermap('sort','asc');
	        //获取数据
			$this->list=$list=$this->getlist($model, $map, $ordermap);
			p($list);die;
			$this->display();

		}
	}
}