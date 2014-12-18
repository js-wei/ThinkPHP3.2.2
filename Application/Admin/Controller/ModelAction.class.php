<?php
class ModelAction extends CommonAction{
	/**
	 * 控制器
	 */
	public function index(){
		//查询
        $map = $this->_search();
        $map['fid']=array('eq','0');
      
        
        //排序
        $ordermap = $this->ordermap('sort','asc');
        //获取数据
        import('Class.Category',APP_PATH);
        $this->model=$channel=Category::unlimitForLevel(M('model')->order($ordermap)->select());
		//$this->model=$model=$this->getlist(M('model'), $map, $ordermap);
		$this->display();
	}

	/**
	 * 添加控制器
	*/
	public function add(){
		$modle=M('model')->select();
		import('Class.Category',APP_PATH);
		$this->contrle=$contrle=Category::limitForLevel($modle);
		//p($contrle);die;
		$this->display();
	}

	public function addhandler(){
		if(!IS_POST) halt('请求页面不存在');
		$data=array();
		$data=$_POST;
		$data['date']=time();
		if(!M('model')->add($data)){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}
    public function update(){
    	$modle=M('model')->select();
		import('Class.Category',APP_PATH);
		$this->contrle=$contrle=Category::limitForLevel($modle);
    	$this->model=M('model')->find(I('id'));
    	$this->display();
    }
    public function updatehandler(){
    	$data=array();
		$data=$_POST;
		$data['dates']=time();
		if(!M('model')->save($data)){
			$this->error('操作失败');
		}
		$this->redirect('index');
    }
	//搜索
	protected function _search() {
        //处理基本查询
        $map = array();
        //控制器名称
        ($title = $this->_get('title', 'trim')) && $map['title'] = array('like', '%' . $title . '%');
        //控制器中文名
        ($name = $this->_get('description ', 'trim')) && $map['description '] = array('like', '%' . $name . '%');
       
        //状态（正常，禁用）
        if ($_GET['status'] == null) {
            $status = -1;
        } else {
            $status = intval($_GET['status']);
        }
        $status >= 0 && $map['status'] = array('eq', $status);
        //输出
        $this->assign('search', array(
            'title' => $title,
            'name' => $name,
            'status' => $status,
        ));
        return $map;
    }

    public function del(){
    	if(!M('model')->delete(I('id'))){
    		$this->error('操作失败');
    	}
    	$this->redirect('index');
    }
}