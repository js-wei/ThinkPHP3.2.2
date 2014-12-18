<?php
class CategoryAction extends CommonAction{

	//主页面
	public function index(){
		//查询
        $map = $this->_search();
        //排序
        $ordermap = $this->ordermap(I('sort'),I('order'));
        //获取数据
		$this->cate=$cate=$this->getlist(M('category'), $map, $ordermap);
		//p($pic);die;
		$this->display();
	}
	public function add(){
		$channel=M('channel')->select();
		import('Class.Category',APP_PATH);
		$this->channel=$channel=Category::limitForLevel($channel);
		$this->display();
	}
	public function addhandler(){
		p($_POST);
	}
} 