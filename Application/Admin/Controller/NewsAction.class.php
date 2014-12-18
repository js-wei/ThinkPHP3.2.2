<?php
class NewsAction extends CommonAction{
	//主页面
	public function index(){
		$map=$this->_search();
		$map1=array('fid'=>I('id'));
		$channel = M('channel')->field('id')->where($map1)->select();
		$cid=array();
		foreach ($channel as $value) {
			$cid[]=$value['id'];
		}
		if($channel){
			$map=array_merge($map,array('cid'=>array('in',$cid)));
		}else{
			$map=array_merge($map,array('cid'=>I('id')));
		}
		//p(I('id'));die;
		$this->arclist=$arclist=$this->getlist(M('article'), $map, $ordermap);
		
		$this->display();
	}
	public function add(){
		$id=I('id');
		$this->cate=$cate=M('channel')->where('fid='.$id)->select();
		$this->display();
	}

	public function update(){
		$id=I('id');
	
		$cid=M('channel')->find(I('cid'));

		if($cid['fid']>0){
			$cate=M('channel')->where('fid='.$cid['fid'])->select();
		}else{
			$cate='';
		}	
		$this->arc=$site=M('article')->find($id);
		$this->cate=$cate;
		$this->cid=$site['cid'];
		
		$this->display();
	}
	public function Insert(){
		if(!IS_POST) halt('请求页面不存在');
		$data=$this->upload_arcticle();
		$data['date']=$data['dates']=time();
		$data['cid']=I('cid');
		$p=M('channel')->find($data['cid']);

		if($p['fid']>0){
			$id=$p['fid'];
		}else{
			$id=I('cid');
		}
		
		
		if(!M('article')->add($data)){
			$this->error('操作失败');
		}

		$this->redirect('index?id='.$id);
	}
	public function updatehandler(){
		if(!IS_POST) halt('请求页面不存在');
		$img =I('image');
		$data=$this->upload_arcticle();
		if(empty($data['image'])){
			if(empty($img)){
				$data['image']='';
			}else{
				$data['image']=$img;
			}
		}
		//p($data);die;
		$data['dates']=time();
		$data['id']=I('id');
		if(!M('article')->save($data)){
			$this->error('操作失败');
		}
		$this->redirect('index?id='.I('cid'));
	}
	public function status(){
		if(!$this->upstatus(M('article'),I('id'),I('type'))){
			$this->error('操作失败');
		}
		$arc=M('article')->find(I('id'));
		$chan=M('channel')->find($arc['cid']);
		
		$this->redirect('index?id='.$chan['id']);
	}
	public function delete(){
		if(!M('article')->delete(I('id'))){
			$this->error('操作失败');
		}
		$this->redirect('index?id='.I('cid'));
	}

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