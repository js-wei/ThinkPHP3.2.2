<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class IndexController extends BaseController {   	
   	public function index(){
   		$show=new \Home\Tool\Show();	//使用Tool
   		$show->alert('您好');/**/
   	}
}