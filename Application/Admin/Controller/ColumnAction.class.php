<?php
class ColumnAction extends CommonAction{
	//主页面
	public function index(){
		//查询
	    $map = $this->_search();
	    $map['status']=array('neq',-1);

	    //排序

	    $ordermap = $this->ordermap('sort','asc');
	    //获取数据
		//$this->column=$column=$this->getlist(M('column'), $map, $ordermap);
		//p($column);die;
		$model=M($this->getActionName());
		import('Class.Category',APP_PATH);
		$this->column=$column=Category::unlimitForLevel($model->where($map)->order($ordermap)->select());
		$this->display();


	}
	public function add(){
		$column=M('column')->select();
		import('Class.Category',APP_PATH);
		$this->column=$column=Category::limitForLevel($column);
		$this->display();
	}

	public function addhandler(){
		$data=$_POST;
		$data['create_time']=$data['create_time']=time();
		$data['uri']=!empty($_POST['uri'])?$_POST['uri']:'';
		if(!M('column')->add($data)){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}

	public function status(){
		if(!$this->upstatus(I('id'),M('column'),I('type'))){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}

	public function delete(){
		if(!$this->del(M('column'),I('id'))){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}

	public function update(){
		$id=I('id',intval);
		import('Class.Category',APP_PATH);
		$this->fcolumn=$fcolumn=Category::limitForLevel(M('column')->select());
		$this->column=$result=M('column')->find($id);
		$this->display();
	}

	public function updatehandler(){
		$data=$_POST;
		//$data['create_time']=time();
		$data['uri']=!empty($_POST['uri'])?$_POST['uri']:'';
		if(!M('column')->save($data)){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}

	//搜索
	protected function _search(){
		$map=array();
		$username=I('k');
		
		$status=I('q');
		if($status>-1&&$status!=""){
			$map['status']=array('eq',$status);
		}
		
		$map['title']=array('like','%'.I('k').'%');
		$this->search=array(
			'k'=>$username,
			'q'=>$status
			);

		return $map;
	}
}