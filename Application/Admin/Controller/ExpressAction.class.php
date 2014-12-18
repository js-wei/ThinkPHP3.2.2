<?php
class ExpressAction extends CommonAction{
	
	public function index(){
		$map=$this->_search();
		$order='cooperate desc,id asc,sort asc';
		$this->list=$list=$this->getlist(M('express'),$map,$order);
		$this->display();
	}
	public function check(){
		$order='cooperate desc,id asc,sort asc';
		$this->list=$list=$this->getlist(M('send'),$map,$order);
		$this->express=M('express')->order($order)->select();
		$this->display();
	}

	public function getline(){
		$name=I('expid');
		$no=I('no');

		echo $this->get_express_line($name,$no);
		
	}

	public function sendExpress(){
		$email=I('email');
		$expid=I('expid');
		$no=I('no');
		$send=M('send')->where('expno='.$expid)->find();
		$order=M('order')->where('expno='.$send['orderid'])->find();
		$data='你好，您在本商城购买的物品现已发货：<br/>具体信息如下：<br/>';
		$data .= $this->get_express_line($expid,$no,'html');
		if(send_email($email,'商家快件信息信息推送',$data)){
			$this->ajaxReturn(array('status'=>1,'msg'=>'邮件发送成功'));
		}else{
			$this->ajaxReturn(array('status'=>0,'msg'=>'邮件发送失败'));
		}
	}

	public function add(){
		$this->display();
	}

	public function cooperate(){
		$msg=empty($_GET['t'])?'取消成功':'设置成功';
		$data=array(
			'id'=>I('id','',intval),
			'cooperate'=>I('t')
			);
		if(!M('express')->save($data)){
			$this->error('设置失败');
		}
		$this->success($msg,U('index'),3);
	}
	public function save(){
		if(!M('express')->add($_POST)){
			$this->error('添加失败');
		}
		$this->success('添加成功',U('index'),3);
	}
	public function delete(){
		if(!$this->del(I('id','',intval))){
			$this->error('删除失败');
		}
		$this->success('删除成功',U('index'),3);
	}
}