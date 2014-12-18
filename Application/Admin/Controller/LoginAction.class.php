<?php
/**
 * 后台登陆
 */
class LoginAction extends Action{
	/**
	 * 登陆视图
	 */
	public function index(){
		$this->display('Index:login');
	}
	/**
	 * 登陆处理
	 */
	public function Login(){
		if(!IS_POST) halt('页面不存在');
		
		if(I('verify','','MD5')!=session('verify')){
			$this->error('验证码错误');
		}		
		
		$pwd = I('password','','MD5');
		$username=strtolower(I('username'));
		$user=M("user")->where(array('username'=>$username))->find();
		


		if(!$user||$user['password']!=$pwd){
			$this->error("账号或者密码错误，请重试");
		}
		if($user['status']==1){
			$this->error("账号已锁定");
		}

		$data=array(
				'id'=>$user['id'],
				'last_date'=>time(),
				'last_ip'=>get_client_ip()
				);
		M("user")->save($data);
		
		Session('uid',$user['id']);
		Session('username',$user['username']);
		Session('logintime',date('Y-m-d h:i',$user['logintime']));
		Session('loginip',$user['loginip']);
	    redirect(__GROUP__);
	}
	/**
	 * 调用验证码
	 */
	public function verify(){
		import('ORG.Util.Image');
		Image::buildImageVerify(4,1);
	}
}