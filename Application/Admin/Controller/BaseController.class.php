<?php
namespace Admin\Controller;
use Think\Controller;
/**
 *基类 
 */
class BaseController extends Controller {
	protected function _initialize(){
		header('Content-type:text/html;charset=utf-8;');
		set_time_limit(0);
		$this->check_priv();        //判断是否登录
		$nav = $this->authlist();	//导航
		$this->assign('nav', $nav);
		
		$site= M('site_conf')->order('id desc')->find();
		$site['logo']=color_txt('OwnCMS',40,'bolder');
	    $site['edit']='请点击&nbsp;<i><a href="__URL__/addSite">编辑</a></i>&nbsp;添加统计代码';
		$this->site =$site;
		$this->singel=$singel=$this->getsingle();
		$this->nav_column=$this->get_column(); 
	}
	/**
	 * [index 首页]
	 * @return [type] [description]
	 */
    public function index(){
    	$this->list=$this->getlist();
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
		$column= \Category::unlimitedForLevel($column);
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
			//$this->redirect('/Login/index');
		}
	}
	/**
	 * [authlist 获取权限列表]
	 * @return [type]
	 */
	protected function authlist(){
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
	* 获取分页数据
	* @param type $model模型名(默认获取当前model)
	* @param type $map条件
	* @param type $order排序
	* @param type $field需要查询的字段，默认全部
	* @param type $pagination为每页显示的数量，默认为配置中的值
	* @return type返回结果数组
	*/
    protected function getlist($model = '', $map = '', $order = '', $pagination = '', $field = '*') {
        import('Class.Page',APP_PATH);
        $model=!empty($model)?$model:M($this->getActionName());
        $count = $model->where($map)->count('*');
        $pagination = $pagination ? $pagination : C('PAGE_SIZE');
        $page = new \Page($count, $pagination);
        $page->setConfig('header', '');
        $page->setConfig('prev','上一页');
        $page->setConfig('next', '下一页');
        $show = $page->show();
        $this->assign('page', $show);
        $res = $model->where($map)->field($field)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();  
        return $res;
    }
    /**
    * [delImage 删除图片]
    * @param  [string] $path 图片路径
    * @return [string] 删除结果
    */
    protected function delImage($path){
    	$path=!empty($path)?$path:I('path');
    	
    	if(!empty($path)){
    		$id=I('id','',intval);
            $index=I('index','',intval);
            $result=M('Article')->find($id);

            $image=array_filter(explode(',', $result['image']));	
            unset($image[$index]); //截取数组，去除空数组
            $image=implode(',', $image);
         
    		$data=array('id'=>$id,'image'=>$image);
    		$result=M('Article')->save($data);

    		if(!unlink(C('DEFAULT_UPLOAD_PATH.IMAGES').$path) || !$result){
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
    public function delFile($id=0){
        $id=$id?$id:I('id','',intval);
        $file=!empty($_POST['file'])?$_POST['file']:'';
       
        if(!unlink(C('DEFAULT_UPLOAD_PATH.FILES').$file)){  
            echo 0;
        }else{
            $data=array('id'=>$id,'file'=>'');
            $result=M('Article')->save($data);
            echo 1;
        }
    }
  	/**
  	* [_setDel 定时删除]
    * @param integer $time [间隔]
    * @param string  $model [模型]
    * @param string  $type [跨度]
  	*/
    protected function _setDel($time=10,$model='',$type='day'){	
        switch ($type) {
        	case 'day':
        		$after=time()- $time*24*60*60;
        		break;
        	case 'week':
        		$after=time()- $time*24*60*60*7;
        		break;
        	case 'hour':
        		$after=time()- $time*60*60;
        		break;
        	default:
        		$after=time()- $time*24*60*60;
        		break;
        }
        
        $name=!empty($model)?$model:$this->getActionName();
        $model=M($name);
        $where['create_time']=array('lt',$after);
        $result=$model->where($where)->delete(); 
        return $result;
    }
    /**
    * [_param 获取参数信息]
    * @param  string $param [参数]
    * @return [type]        [description]
    */
    protected function _param($param=''){
        if(empty($param)){
            foreach ($_REQUEST as $k => $v) {
                if($k!='_URL_'){
                    $param[$k]=$v;
                }
            }
        }
        return $param;
    }
    /**
     * [_constant 输出系统常量]
     * @param  integer $type [输出类型：1所有、2系统、3路径、4请求]
     * @return [type]        [description]
     */
    protected function _constant($type=1){
    	$url=array(
    		'URL_TYPE'=>array(
    			array(
	    		'URL_COMMON'=>URL_COMMON,
	    		'information'=>'普通模式(0)'
	    		),
	    		array(
	    		'URL_PATHINFO'=>URL_PATHINFO,
	    		'information'=>'PATHINFO(1)'
	    		),
	    		array(
	    		'URL_REWRITE'=>URL_REWRITE,
	    		'information'=>'REWRITE(2)'
	    		),
	    		array(
	    		'URL_COMPAT'=>URL_COMPAT,
	    		'information'=>'兼容模式(3)'
	    		),
    		)
    	);
    	$path=array(
    		'PATH_TYPE'=>array(
    			array(
	    		'THINK_PATH'=>THINK_PATH,
	    		'information'=>'框架系统目录'
	    		),
	    		array(
	    		'APP_PATH'=>APP_PATH,
	    		'information'=>'应用目录（默认为入口文件所在目录)'
	    		),
	    		array(
	    		'LIB_PATH'=>LIB_PATH,
	    		'information'=>"系统核心类库目录（默认为 THINK_PATH.'Think/'）"
	    		),
	    		array(
	    		'MODE_PATH'=>LIB_PATH,
	    		'information'=>"系统应用模式目录（默认为 THINK_PATH.'Mode/'）"
	    		),
	    		array(
	    		'BEHAVIOR_PATH'=>LIB_PATH,
	    		'information'=>"行为目录（默认为 THINK_PATH.'Behavior/'）"
	    		),
	    		array(
	    		'COMMON_PATH'=>LIB_PATH,
	    		'information'=>"公共模块目录（默认为 THINK_PATH.'Common/'）"
	    		),
	    		array(
	    		'VENDOR_PATH'=>LIB_PATH,
	    		'information'=>"第三方类库目录（默认为 THINK_PATH.'Vendor/'）"
	    		),
	    		array(
	    		'RUNTIME_PATH'=>RUNTIME_PATH,
	    		'information'=>"应用运行时目录（默认为 THINK_PATH.'Runtime/'）"
	    		),
	    		array(
	    		'HTML_PATH'=>HTML_PATH,
	    		'information'=>"应用静态缓存目录（默认为 THINK_PATH.'Html/'）"
	    		),
	    		array(
	    		'CONF_PATH'=>CONF_PATH,
	    		'information'=>"应用公共配置目录（默认为 COMMON_PATH.'Conf/'）"
	    		),
	    		array(
	    		'LANG_PATH'=>LANG_PATH,
	    		'information'=>"公共语言包目录（默认为 COMMON_PATH.'Lang/'）"
	    		),
	    		array(
	    		'LOG_PATH'=>LOG_PATH,
	    		'information'=>"应用日志目录（默认为 RUNTIME_PATH.'Logs/'）"
	    		),
	    		array(
	    		'CACHE_PATH'=>CACHE_PATH,
	    		'information'=>"项目模板缓存目录（默认为 RUNTIME_PATH.'Cache/'）"
	    		),
	    		array(
	    		'TEMP_PATH'=>TEMP_PATH,
	    		'information'=>"应用缓存目录（默认为 RUNTIME_PATH.'Temp/'）"
	    		),
	    		array(
	    		'DATA_PATH'=>DATA_PATH,
	    		'information'=>"应用缓存目录（默认为 RUNTIME_PATH.'Data/'）"
	    		),
    		)
    	);
		
		$system=array(
			'system'=>array(
				array(
				'IS_CGI'=>IS_CGI,
	    		'information'=>"是否属于 CGI模式"
				),
				array(
				'IS_WIN'=>IS_WIN,
	    		'information'=>"是否属于Windows 环境"
				),
				array(
				'IS_CLI'=>IS_CLI,
	    		'information'=>"是否属于命令行模式"
				),
				array(
				'__ROOT__'=>__ROOT__,
	    		'information'=>"网站根目录地址"
				),
				array(
				'__APP__'=>__APP__,
	    		'information'=>"当前应用（入口文件）地址"
				),
				array(
				'__MODULE__'=>__MODULE__,
	    		'information'=>"当前模块的URL地址"
				),
				array(
				'__CONTROLLER__'=>__CONTROLLER__,
	    		'information'=>"当前控制器的URL地址"
				),
				array(
				'__ACTION__'=>__ACTION__,
	    		'information'=>"当前操作的URL地址"
				),
				array(
				'__SELF__'=>__SELF__,
	    		'information'=>"当前URL地址"
				),
				array(
				'__INFO__'=>__INFO__,
	    		'information'=>"当前的PATH_INFO字符串"
				),
				array(
				'__EXT__'=>__EXT__,
	    		'information'=>"当前URL地址的扩展名"
				),
				array(
				'MODULE_PATH'=>MODULE_PATH,
	    		'information'=>"当前模块路径"
				),
				array(
				'CONTROLLER_NAME'=>CONTROLLER_NAME,
	    		'information'=>"当前控制器名"
				),
				array(
				'ACTION_NAME'=>ACTION_NAME,
	    		'information'=>"当前操作名"
				),
				array(
				'APP_DEBUG'=>APP_DEBUG,
	    		'information'=>"是否开启调试模式"
				),
				array(
				'APP_MODE'=>APP_MODE,
	    		'information'=>"当前应用模式名称"
				),
				array(
				'APP_STATUS'=>APP_STATUS,
	    		'information'=>"当前应用状态"
				),
				array(
				'STORAGE_TYPE'=>STORAGE_TYPE,
	    		'information'=>"当前存储类型"
				),
				array(
				'MODULE_PATHINFO_DEPR'=>MODULE_PATHINFO_DEPR,
	    		'information'=>"模块的PATHINFO分割符"
				),
				array(
				'MEMORY_LIMIT_ON'=>MEMORY_LIMIT_ON,
	    		'information'=>"系统内存统计支持"
				),
				array(
				'RUNTIME_FILE'=>RUNTIME_FILE,
	    		'information'=>"项目编译缓存文件名"
				),
				array(
				'THEME_NAME'=>THEME_NAME,
	    		'information'=>"当前主题名称"
				),
				array(
				'THEME_PATH'=>THEME_PATH,
	    		'information'=>"当前模板主题路径"
				),
				array(
				'LANG_SET'=>LANG_SET,
	    		'information'=>"当前浏览器语言"
				),
				array(
				'MAGIC_QUOTES_GPC'=>MAGIC_QUOTES_GPC,
	    		'information'=>"MAGIC_QUOTES_GPC"
				),
				array(
				'NOW_TIME'=>NOW_TIME,
	    		'information'=>"当前请求时间（时间戳）"
				),
				array(
				'REQUEST_METHOD'=>REQUEST_METHOD,
	    		'information'=>"当前请求类型"
				),
				array(
				'IS_GET'=>IS_GET,
	    		'information'=>"当前是否GET请求"
				),
				array(
				'REQUEST_METHOD'=>REQUEST_METHOD,
	    		'information'=>"当前请求类型"
				),
				array(
				'IS_POST'=>IS_POST,
	    		'information'=>"当前是否POST请求"
				),
				array(
				'IS_PUT'=>IS_PUT,
	    		'information'=>"当前是否PUT请求"
				),
				array(
				'IS_DELETE'=>IS_DELETE,
	    		'information'=>"当前是否DELETE请求"
				),
				array(
				'IS_AJAX'=>IS_AJAX,
	    		'information'=>"当前是否AJAX请求"
				),
				array(
				'BIND_MODULE'=>BIND_MODULE,
	    		'information'=>"当前绑定的模块（3.2.1新增）"
				),
				array(
				'BIND_CONTROLLER'=>BIND_CONTROLLER,
	    		'information'=>"当前绑定的控制器（3.2.1新增）"
				),
				array(
				'BIND_ACTION'=>BIND_ACTION,
	    		'information'=>"当前绑定的操作（3.2.1新增）"
				),
				array(
				'CONF_EXT'=>CONF_EXT,
	    		'information'=>"配置文件后缀（3.2.2新增）"
				),
				array(
				'CONF_PARSE'=>CONF_PARSE,
	    		'information'=>"配置文件解析方法（3.2.2新增，暂无实现）"
				),
				array(
				'TMPL_PATH'=>TMPL_PATH,
	    		'information'=>"用于改变全局视图目录（3.2.3新增，暂无实现）"
				),
			)
			);
		$constant=array_merge($system,$path,$url);
		switch ($type) {
			case '1':
				return $constant;
				break;
			case '2':
				return $system;
				break;
			case '3':
				return $path;
				 break;
			case '4':
				return $url;	
				break;
			default:
				return $constant;
				break;
		}
    	
    }

}