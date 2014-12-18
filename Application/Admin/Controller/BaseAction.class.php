<?php
namespace Admin\Controller;
use Think\Controller;

class BaseController extends BaseController{
	//主页面
	public function index(){
        //获取数据
		$this->site=$site=M('confing')->find(1);
		$this->display();
	}

	public function showlist(){
		$this->display();
	}

	public function usual(){
		$this->display();
	}

	public function flink(){
		
		$map = $this->_search();
        //排序
        $ordermap = $this->ordermap(I('sort'),I('order'));
        //获取数据
		$this->list=$arclist=$this->getlist(M('flink'), $map, $ordermap);
		$this->display();
	}
	public function addflink(){
		$this->display();
	}

	public function InsertFlink(){
		$images = $this->uploadsEditor();	//上传图片
		$images=!empty($images)?$images['ico']:I('ico');
		$data=$_POST;
		$data['uri']=(strstr($_POST['uri'],'http'))?$_POST['uri']:'http://'.$_POST['uri'];
		$data['date']=time();
		$data['ico']=$images;
		
		if(!M('flink')->add($data)){
			$this->error('添加失败');
		}
		$this->redirect('flink');
	}

	public function updateflink(){
		$this->flink=M('flink')->find(I('id','',intval));
		$this->display();
	}

	public function deleteflink(){
		if(!M('flink')->delete(I('id','',intval))){
			$this->error('删除失败');
		}
		$this->redirect('flink');
	}
	public function message(){
		$map = $this->_search();
        //排序
        $ordermap = $this->ordermap(I('sort'),I('order'));
        //获取数据
		$this->message=$arclist=$this->getlist(M('message'), $map, $ordermap);
		$this->display();
	}
	
	public function emailReply(){
		$this->message=M('message')->find(I('id'));
		$this->display();
	}

	public function sengReply(){
		$body=I('content','',htmlspecialchars_decode);
		//p($body);die;
		if(!send_email(I('email'),'管理员回复',$body)){
			$this->error('回复失败');
		}else{
			M('message')->save(array('id'=>I('id'),'isreply'=>1));
		}
		$this->success('回复成功','message',3);
	}
	public function delete(){
		if(!M('message')->delete(I('id'))){
			$this->error('操作失败');
		}
		$this->redirect('message');
	}

	public function update(){
		$this->site=$site=M('confing')->find(1);
		$this->display();
	}
	public function addhandler(){
		if(!IS_POST) halt('请求页面不存在');
		$data=array(
			'title'=>I('title',htmlspecialchars),
			'keywords'=>I('keywords',htmlspecialchars),
			'description'=>I('description',htmlspecialchars),
			'flink'=>I('flink',htmlspecialchars),
			'shard'=>I('shard',htmlspecialchars),
			'code'=>I('code',htmlspecialchars),
			'copyright'=>I('copyright',htmlspecialchars),
			'conact'=>I('conact',htmlspecialchars),
			'sum'=>I('sum',htmlspecialchars),
			'date'=>time()
			);
		if(!M('confing')->add($data)){
			$this->error('操作失败');
		}
		$this->redirect('index');
	}
	public function updateHandle(){
		if(!IS_POST) halt('请求页面不存在');

		$data=array(
			'id'=>1,
			'title'=>I('title',htmlspecialchars),
			'keywords'=>I('keywords',htmlspecialchars),
			'description'=>I('description',htmlspecialchars),
			'flink'=>I('flink',htmlspecialchars),
			'shard'=>I('shard',htmlspecialchars),
			'code'=>I('code',htmlspecialchars),
			'copyright'=>I('copyright',htmlspecialchars),
			'conact'=>I('conact',htmlspecialchars),
			'sum'=>I('sum',htmlspecialchars),
			'date'=>time()
			);
		$cofing=M('confing')->find(1);
		if($cofing){
			if(!M('confing')->save($data)){
				$this->error('操作失败');
			}
		}else{
			if(!M('confing')->add($data)){
				$this->error('操作失败');
			}
		}
		
		$this->redirect('index');
	}

	public function check(){
		$this->mess=M('message')->find(I('id'));
		$this->display();
	}

	public function commont(){
		$this->mess=M('message')->find(I('id'));
		$this->display();
	}

	public function reply(){
		$data=array(
			'email'=>I('email'),
			'attachment'=>$_FILES['attachment'],
			'name'=>'',
			'subject'=>'',
			'body'=>I('content')
			);
		
		$result=send_mail($data['email'],$data['name'],$data['subject'],$data['body'],$data['attachment']);
	}

	public function site(){
		$map = $this->_search();
        //排序
        $ordermap = $this->ordermap(I('sort'),I('order'));

		$this->list=$this->getlist(M('site'),$map,$ordermap);
		$this->display();
	}


	public function deletesite(){
		if(!M('site')->delete(I('id',intval))){
			$this->error('删除失败');
		}
		$this->redirect('site');
	}

	public function addsitehandler(){
		if(I('id',intval)){
			$data=array(
				'id'=>I('id',intval),
				'name'=>I('name'),
				'url'=>I('url')
				);
			if(!M('site')->save($data)){
				$this->error('修改失败');
			}
		}else{
			$data=array(
				'name'=>I('name'),
				'url'=>I('url')
				);
			if(!M('site')->add($data)){
				$this->error('添加失败');
			}
		}
		$this->redirect('site');
	}

	public function updatesite(){
		$this->vo=M('site')->find(I('id'));
		$this->display('addsite');
	}
}