<?php
class UserAction extends CommonAction{
	/**
	 * 用户
	 */
	public function index(){
		//查询
        $map = $this->_search_user();
        $user=M('user')->find(session('uid'));
        $map['gid']=array('neq',-1);
        //排序
        //$ordermap = $this->ordermap('sort','asc');
        $count=0;
        if($user['gid']==-1){
			$this->user=$user=$this->getlist(M('user'), $map, $ordermap);
			$count=1;

        }else{
        	$this->user=$user=M('user')->where('id='.session('uid'))->select();
        }
        $this->count=$count;
       // p($count);die;
        //获取数据	
		$this->display();
	}

	/**
	 * 添加用户
	 */
	public function add(){
		$this->display();
	}

	public function checkNameRepeat(){
		$flag=0;
		$result=M('user')->where(array('username'=>I('username')))->find();
		if($result){
			$flag=1;
		}
		echo $flag;
	}

	public function addhandler(){
		$date=time();
		$data=array(
			'username'=>I('username'),
			'password'=>I('password','',md5),
			'gid'=>I('gid'),
			'status'=>I('status'),
			'date'=>$date,
			'hash'=>base64(I('password'))
			);
		
		if(!M('user')->add($data)){
			$this->error('操作失败');
		}

		$this->redirect('index');
	}

	public function update_user(){
		$this->user=$user=M('user')->find(I('id'));
		$this->display();
	}


	public function updatehandler(){
		$date=time();
		$data=array(
			'id'=>I('id'),
			'username'=>I('username'),
			'password'=>I('password','',md5),
			'gid'=>I('gid'),
			'status'=>I('status'),
			'date'=>$date,
			'hash'=>base64(I('password'))
			);
		
		if(!M('user')->save($data)){
			$this->error('操作失败');
		}

		$this->redirect('index');
	}

	public function status_user(){
		if(!$this->upstatus(M('user'),I('id'),I('type'))){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}
	public function del(){
		if(!M('user')->delete(I('id'))){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}

	protected function _search_user(){
		$map=array();
		$username=I('k');
		$status=I('q');
		if($status>-1&&$status!=""){
			$map['status']=array('eq',$status);
		}
		
		$map['username']=array('like','%'.I('k').'%');
		$this->search=array(
			'k'=>$username,
			'q'=>$status
			);
		return $map;
	}
}