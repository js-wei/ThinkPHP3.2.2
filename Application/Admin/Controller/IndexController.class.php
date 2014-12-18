<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 
 */
class IndexController extends BaseController {
  	/**
	 * [index 首页]
	 * @return [type] [description]
	 */
    public function index(){
    	//p($this->_constant(2));die;
    	$model=M('article');
    	$map['column_id']=3;
    	$order='create_time desc,id desc';
    	$this->list=$list=$this->getlist($model,$map,$order,1);
    	$this->display();
    } 
    //导航
	public function nav(){
		$this->display();
	}
	//欢迎
	public function main(){
		$this->display();
	}
	public function loginOut(){
		session_unset();
		session_decode();
		$this->redirect(GROUP_NAME.'/Index/login');
	}
}