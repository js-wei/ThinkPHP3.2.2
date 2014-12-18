<?php
class ShoppingAction extends CommonAction{
	public function _initialize(){
		parent::_initialize();
		$map=$this->_search();
		$order='id desc,sort asc';
	}

	public function index(){
		$map=$this->_search();
        $order=$this->ordermap('id','asc');
      
        $model=M('order');
        $this->list=$list=$this->getlist($model,$map,$order);
       
        $this->display();
	}

	public function send(){
		$this->list=$list=$this->getlist(M('send'),$map,$order);
		$this->display();
	}

	public function add_good(){
		$this->express=M('express')->order('cooperate desc')->select();
		$this->display();
	}

	public function delete(){
		if(!M('order')->delete(I('id',intval))){
			$this->error('删除失败');
		}
		$this->redirect('index');
	}

	public function del(){
		if(!M('send')->delete(I('id',intval))){
			$this->error('删除失败');
		}
		$this->redirect('send');
	}

	public function send_notice(){
		$id=I('id','',intval);
		$order=M('order')->find($id);
		$member=M('member')->find($order['mid']);
		$good=M('article')->find($order['goodsid']);

		$body='尊敬的'.$member['username'].',您商城兑换的《'.$good['title'].'》已经发货请注意查收，请在查收之后给我们一个回复，以便我们更好的开展工作，谢谢合作。<br/>'.
			  '回复地址:'.'<a href="'.C('BaseSite.url').'home/index/revel.html?reid='.$id.'">回复我们</a>。注意系统会在发货10天后，会认为您已经收到物品，如未收到请在10天内联系我们。联系方式：'.'<a href="'.C('BaseSite.url').'service/index/revel.html">联系我们</a>';
		$mail=send_email("524314430@qq.com",'发货通知',$body,'51游戏官网-51Game');
		if (!$mail) {
			$this->error('发送失败');
		}else{
			M('order')->save(array('id'=>$id,'issend'=>1,'send_time'=>time()));
			$this->success('发送成功','index',5);
		}
	}

	public function revel(){
		$reid=$_GET['reid'];
		$order=M('order')->find($reid);
		$send=M('send')->where(array('orderid'=>$order['orderid']))->find();
		if(M('send')->save(array(
			'id'=>$send['id'],
			'isrec'=>1,
			'recdate'=>time()
			))){
			$this->success("谢谢您的回复",C('BaseSite.url'),5);
		}else{
			$this->success("您已经回复了，请勿重复回复",C('BaseSite.url'),5);
		}
	}

	public function add_send(){
		$this->order=M('order')->query('SELECT * FROM think_order as a INNER JOIN think_send as b ON a.orderid =b.orderid where isrec=0');
		$this->express=M('express')->order('cooperate desc,id asc,sort asc')->select();
		$this->display();
	}

	public function addsendhandler(){
		$data=$_POST;
		$data['date']=time();
		
		if(!M('send')->add($data)){
			$this->error('添加失败');
		}
		M('order')->where(array('orderid'=>$data['orderid']))->save(array('issend'=>1));
		$this->redirect('send');
	}
}