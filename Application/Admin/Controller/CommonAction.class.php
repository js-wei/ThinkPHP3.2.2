<?php
class CommonAction extends Action{
	protected function _initialize(){
		header('Content-type:text/html;charset=utf-8;');
		set_time_limit(0);
        $this->check_priv();        //判断是否登录
		$nav = $this->authlist();	//导航
		$this->assign('nav', $nav);
		$this->_setSend();    //更新发货清单
		$site= M('site_conf')->order('id desc')->find();
		$site['logo']=color_txt('OwnCMS',40,'bolder');
	    $site['edit']='请点击&nbsp;<i><a href="__URL__/addSite">编辑</a></i>&nbsp;添加统计代码';
		$this->site =$site;
		$this->singel=$singel=$this->getsingle();
		$this->nav_column=$this->get_column();

		
	}

    public function index(){
        $map=$this->_search();
        $order=$this->ordermap('id','asc');
        $name=$this->getActionName();
        $model=M($name);
        $this->list=$this->getlist($model,$map,$order);
        $this->display();
    }
	/**
	 * [get_column 得到导航栏目栏目]
	 * @return [type]
	 */
	protected function get_column(){
		$where=array('status'=>0,'type'=>array('lt',3));

		$column=M("column")->where($where)->order('id asc')->select();
        
		import('Class.Category',APP_PATH);
		$column=Category::unlimitedForLevel($column);
		return $column;
	}
	/**
     * 获取排序
     * @param type $field排序的字段名（支持数组）
     * @param type $range排序方法
     * @return string
    */
    protected function ordermap($field = '', $range = '') {
        if ($field && $range) {
            if (is_array($field)) {
                for ($i = 0; $i < count($field); $i++) {
                    $arr[] = $field[$i] . " " . $range;
                }
                $ordermap = implode(',', $arr);
            } else {
                $ordermap = $field . " " . $range;
            }
        } else {
            $ordermap = null;
        }
        return $ordermap;
    }

    /**
	 * 获取数据列表
	 * @param type $model模型名
	 * @param type $map条件
	 * @param type $order排序
	 * @param type $field需要查询的字段，默认全部
	 * @param type $pagination为每页显示的数量，默认为配置中的值
	 * @return type返回结果数组
	 */
    protected function getlist($model = '', $map = '', $order = '', $field = '*', $pagination = '') {
        import('ORG.Util.Page');
        $count = $model->where($map)->count('*');
        $pagination = $pagination ? $pagination : C('PAGE_SIZE');
        $page = new Page($count, $pagination);
        $page->setConfig('header', '');
        $page->setConfig('prev','上一页');
        $page->setConfig('next', '下一页');
        $show = $page->show();
        $this->assign('page', $show);
        $res = $model->where($map)->field($field)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
       
        return $res;
    }
    //得到单页面
    protected function getsingle(){
    	return M('column')->where('type=3')->select();
    }
  
	/**
	 * [check_priv 是否登录]
	 * @return [type]
	 */
	protected function check_priv() {
		if(!isset($_SESSION['uid'])){
			$this->redirect(GROUP_NAME . '/Login/index');
		}
	}
	/**
	 * [authlist 获取权限列表]
	 * @return [type]
	 */
	protected function authlist(){
		/*$pwer=M('auth_user_access')->where(array('uid'=>$_SESSION['uid']))->select();
		$group=array();	//获取所在群组的id
		foreach ($pwer as $v) {
			$group = array_merge($group,$v);
		}
		//$where=array('in'=>$group['gid']);
		$map  =array('id'=>array('in',$group['gid']));
		$map['status']=array('eq','0');
		$list = M('user_group')->where($map)->select();
		
		$authkeylist='';
		foreach ($list as $k => $v) {
			if(!empty($v['rules'])){
				$authkeylist .= $v['rules'].',';
			}else{
				$authkeylist = substr($authkeylist,0,strlen($authkeylist)-1);
			}		
		}
		
		$where =array('id'=>array('in',$authkeylist));
		$model=M('model')->where($where)->order('sort asc')->select();
		$nav=array();
		foreach ($model as $k => $v) {
			$v['child']=M('model')->where('fid = '.$v['id'])->order('sort asc')->select();
			$nav[]=$v;

		}*/
		$nav=array();
		$model = M('model')->where('fid=0')->select();
		foreach ($model as  $v) {
			$map['fid']=$v['id'];
			$map['status']=0;
			$child=M('model')->where($map)->order('sort asc')->select();
			$v['child']=$child;
			$nav[]=$v;
		}

		return $nav;
	}
	//改变状态
	protected function upstatus($id,$model,$type=1){
        $model=!empty($model)?$model:M($this->getActionName());
		$data=array(
			'id'=>$id,
			'status'=>$type?1:0
			);
		
		return $model->save($data);
	}
	//删除文件
	protected function del($id,$model){
        $model=!empty($model)?$model:M($this->getActionName());
		return $model->delete($id);
	}
	/**
	 * [_search description]
	 * @return [type]
	 */
	protected function _search() {
        //处理基本查询
        $map = array();
        //控制器名称
        ($title = $this->_get('title', 'trim')) && $map['title'] = array('like', '%' . $title . '%');
        //控制器中文名
        ($name = $this->_get('name ','trim')) && $map['name'] = array('like', '%' . $name . '%');
        //
        ($title = $this->_get('k', 'trim')) && $map['title'] = array('like', '%' . $title . '%');
        ($name = $this->_get('k', 'trim')) && $map['name'] = array('like', '%' . $title . '%');
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
            'k' => I('k'),
            'status' => $status,
        ));
        return $map;
    }

   
	/**
	 * [uploadUEditor 上传图片]
	 * @return [type]
	 */
	public function uploadsEditor(){
       	import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->subType ='date' ;
        $upload->dateFormat ='ymd' ;
        $upload->savePath =  './Uploads/ueditor/';// 设置附件上传目录
       	// p($_FILES);die;
      
    	if($upload->upload()){
            $info =  $upload->getUploadFileInfo();
            //p($info);die;
            //echo json_encode(array(
            //         'url'=>$info[0]['savename'],
            //         'title'=>htmlspecialchars($_POST['pictitle'], ENT_QUOTES),
            //         'original'=>$info[0]['name'],
            //         'state'=>'SUCCESS'
            // )
            //$image[$info[0]['key']]=$info[0]['savename'];
            foreach ($info as $k => $v) {
            	
            	if($v['key']=='image'){
					$image1.=$v['savename'].',';
            	}else{
					$image[$v['key']]=$v['savename'];
            	}
            }

          	$d=array('image'=>substr($image1,0,-1));
          	
          	if(is_array($image)){
				$data=array_merge($image,$d);
          	}else{
				$data=$d;
          	}
            return $data;
        }
     }
  
    /**
	 * [uploadUEditor 上传文件]
	 * @return [type]
	 */
	public function UploadsFile(){
       	import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 52428800 ;// 设置附件上传大小50M
		$upload->allowExts  = array('zip', 'rar', 'pdf', 'xls','doc','docx','exe');// 设置附件上传类型
        $upload->subType ='date' ;
        $upload->dateFormat ='ymd' ;
        $upload->savePath =  './Uploads/file/';// 设置附件上传目录
        if($upload->upload()){
            $info =  $upload->getUploadFileInfo();
            //echo json_encode(array(
            //         'url'=>$info[0]['savename'],
            //         'title'=>htmlspecialchars($_POST['pictitle'], ENT_QUOTES),
            //         'original'=>$info[0]['name'],
            //         'state'=>'SUCCESS'
            // )
            $file[$info[0]['key']]=$info[0]['savename'];
            
            return $file;
        }else{
            return array('state'=>$upload->getErrorMsg());
        }
    }
    /**
     * [makeAttr 重置文章属性]
     * @param  [array] $resetAttr 重置的属性
     * @return [array] 返回重置的属性	
     */
    protected function makeAttr($resetAttr){
    	$attr=array('com'=>0,'new'=>0,'head'=>0,'top'=>0,'img'=>0,'hot'=>0);
    	foreach ($resetAttr as $k => $v) {
    		$attr[$k]=1;
    	}
    	return $attr;
    }
    /**
     * [import_class 加载共用类]
     * @return [type] [description]
     */
    protected function import_class(){
        vendor('Snake.ServerDefine');
        vendor('Snake.CSResultID');
        vendor('Snake.PlayerCommonInfo');
        vendor('Snake.WebQunData');
        vendor('Snake.VipData');
        vendor('Snake.Birthday');
        vendor('Snake.Service');
        vendor('Snake.PlayerCommonInfo');
        vendor('Snake.PlayerCommonInfo');

        
        vendor('Snake.CRequestGetUserDetailInfo');
        vendor('Snake.SocketEngine');
        vendor('Snake.CSHead');
        vendor('Snake.CResponseGetUserDetailInfo');
        vendor('Snake.CodeEngine');
        vendor('Snake.BigEndianBuffer');
        vendor('Snake.BigEndianBytesBuffer');
        vendor('Snake.BigEndianBytesBuffer');
    }

    /**
     * [delImage 删除图片]
     * @param  [string] $path 图片路径
     * @return [string] 删除结果
     */
    public function delImage($path){
    	$path=!empty($path)?$path:I('path');
    	
    	if(!empty($path)){
    		$id=I('id','',intval);
            $index=I('index','',intval);
            $result=M('Article')->find($id);

            $image=array_filter(explode(',', $result['image']));
            unset($image[$index]); 
            $image=implode(',', $image);
         
    		$data=array('id'=>$id,'image'=>$image);
    		$result=M('Article')->save($data);

    		if(!unlink('./Uploads/ueditor/'.$path) || !$result){
    			if(!$result){
    				echo 1;
    			}else{
    				echo 2;
    			}
    		}else{
    			echo 0;
    		}
    	}
    }
    /**
     * [delFile 删除文件]
     * @return [int] [返回结果]
     */
    public function delFile(){
        $id=I('id','',intval);
        
        $file=!empty($_POST['file'])?$_POST['file']:'';
       
        if(!unlink('./Uploads/file/'.$file)){  
            echo 0;
        }else{
            $data=array('id'=>$id,'file'=>'');
            $result=M('Article')->save($data);
            echo 1;
        }
    }

    /**
     * [get_express_line 获取快件路线]
     * @param  [type] $name [快递公司]
     * @param  [type] $no   [快件单号]
     * @return [type]       [description]
     */
    protected function get_express_line($name,$no,$type){
        import('Class.Express',APP_PATH);
        $exp=new Express();
        $data=$exp->express($name,$no,$type);
        $data=iconv('gbk','utf-8',$data);
        return $data;
    }
    /**
     * [_setSend 定时执行更新发货操作]
     * @param integer $number [间隔时间]
     */
    protected function _setSend($number=10){
        $after=time()- $number*24*60*60;
        $order=M('send')->where(array('date'=>array('lt',$after)))->select();
        if(!empty($order)){
            foreach ($order as $v) {
                M('send')->save(
                    array(
                        'id'=>$v['id'],
                        'isrec'=>1,
                        'recdate'=>time()
                        )
                    );
            }
        }
    }


    
}