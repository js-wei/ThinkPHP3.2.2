<?php
class HomeAction extends CommonAction{
	//主页面
	public function huandeng(){
		//查询
        $map = $this->_search();
        $map['tid']=0;
        //排序
        $ordermap = $this->ordermap(I('sort'),I('order'));
        //获取数据
		$this->pic=$pic=$this->getlist(M('banner'), $map, $ordermap);
		//p($pic);die;
		$this->display();
	}

	//广告
	public function adv(){
		//查询
        $map = $this->_search();
        $map['tid']=1;
        //排序
        $ordermap = $this->ordermap(I('sort'),I('order'));
        //获取数据
		$this->adv=$adv=$this->getlist(M('ad'), $map, $ordermap);
		$this->display();
	}
	//
	public function flink(){
		$this->display();
	}
	public function add(){
		$this->display();
	}
	
	public function status_adv(){
		if(!$this->upstatus(M('ad'),I('id'),I('type'))){
			$this->error('操作失败');
		}
		$this->redirect('adv');
	}

	public function status(){
		if(!$this->upstatus(M('banner'),I('id'),I('type'))){
			$this->error('操作失败');
		}
		$this->redirect('huandeng');
	}

	public function del_adv(){
		$result=M('ad')->find(I('id'));
		if(!$this->del(M('ad'),I('id'))){
			$this->error('操作失败');
		}
		unlink($result['path']);
		$this->redirect('adv');
	}
	public function delete(){
		$result=M('banner')->find(I('id'));
		if(!$this->del(M('banner'),I('id'))){
			$this->error('操作失败');
		}
		unlink($result['path']);
		$this->redirect('huandeng');
	}

	public function update(){
		$id=I('id',intval);
		$this->pic=$result=M('banner')->find($id);
		$this->display();
		
	}
	public function update_adv(){
		$id=I('id',intval);
		$this->adv=$result=M('ad')->find($id);
		$this->display();
		
	}

	public function upload_file(){
		if(!IS_POST) halt('请求页面不存在');
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath =  './Public/Uploads/';// 设置附件上传目录
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
		}
		
		$data=array(
			'name'=>$info[0]['name'],
			'size'=>$info[0]['size'],
			'image'=>$info[0]['savepath'].=$info[0]['savename'],
			'savename'=>$info[0]['savename'],
			'description'=>I('description','','htmlspecialchars'),
			'url'=>I('link'),
			'sort'=>I('sort'),
			'create_time'=>time(),
			'tid'=>I('tid')
			);
		//p($data);die;
		if(!M('banner')->add($data)){
			$this->error('上传失败');
		}
		$this->redirect('huandeng');
		
	}

	public function upload_adv_file(){
		if(!IS_POST) halt('请求页面不存在');
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath =  './Public/Uploads/';// 设置附件上传目录
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
		}
		
		$data=array(
			'name'=>$info[0]['name'],
			'size'=>$info[0]['size'],
			'image'=>$info[0]['savepath'].=$info[0]['savename'],
			'description'=>I('description','','htmlspecialchars'),
			'url'=>I('link'),
			'sort'=>I('sort'),
			'status'=>I('status'),
			'create_time'=>time(),
			'tid'=>I('tid')
			);
		
		if(!M('ad')->add($data)){
			$this->error('上传失败');
		}
		$this->redirect('adv');
		
	}
	public function update_adv_file(){
		if(!IS_POST) halt('请求页面不存在');
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath =  './Public/Uploads/';// 设置附件上传目录
		if(!$upload->upload()) {// 上传错误提示错误信息
			//$this->error($upload->getErrorMsg());
			$data=array(
				'id'=>I('id'),
				'path'=>I('old'),
				'info'=>I('info','','htmlspecialchars'),
				'sort'=>I('sort'),
				'url'=>I('link'),
				'dates'=>time(),
				'tid'=>I('tid')
				);

		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			unlink(I('old'));
			$data=array(
				'id'=>I('id'),
				'name'=>$info[0]['name'],
				'size'=>$info[0]['size'],
				'path'=>$info[0]['savepath'].=$info[0]['savename'],
				'info'=>I('info','','htmlspecialchars'),
				'url'=>I('link'),
				'sort'=>I('sort'),
				'dates'=>time(),
				'tid'=>I('tid')
				);
		}
		//p($data);die;
		
		if(!M('ad')->save($data)){
			$this->error('上传失败');
		}
		$this->redirect('adv');
		
	}
	public function update_file(){
		if(!IS_POST) halt('请求页面不存在');
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->savePath =  './Public/Uploads/';// 设置附件上传目录
		if(!$upload->upload()) {// 上传错误提示错误信息
			//$this->error($upload->getErrorMsg());
			$data=array(
				'id'=>I('id'),
				'path'=>I('old'),
				'info'=>I('info','','htmlspecialchars'),
				'sort'=>I('sort'),
				'url'=>I('link'),
				'dates'=>time()
				);
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			unlink(I('old'));
			$data=array(
				'id'=>I('id'),
				'name'=>$info[0]['name'],
				'size'=>$info[0]['size'],
				'path'=>$info[0]['savepath'].=$info[0]['savename'],
				'info'=>I('info','','htmlspecialchars'),
				'url'=>I('link'),
				'sort'=>I('sort'),
				'dates'=>time()
				);
		}
		
		if(!M('banner')->save($data)){
			$this->error('上传失败');
		}
		$this->redirect('huandeng');
		
	}
	//搜索
	protected function _search() {
        //处理基本查询
        $map = array();
        //控制器名称
        ($title = $this->_get('title', 'trim')) && $map['title'] = array('like', '%' . $title . '%');
        //控制器中文名
        ($name = $this->_get('info ', 'trim')) && $map['info'] = array('like', '%' . $name . '%');
       
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
}