<?php

return array(
	//'配置项'=>'配置值'
	
	//允许访问的模块列表
	'MODULE_ALLOW_LIST'    	=>  array('Home','Admin'),
	// 设置禁止访问的模块列表
	'MODULE_DENY_LIST'      =>  array('Common','Runtime','Class'),
	//默认模块
	'DEFAULT_MODULE'     	=> 'Home',

	//URL模式
    'URL_MODEL'          	=> '2',
	//是否开启session
    'SESSION_AUTO_START' 	=> true,
	//不区分大小写
	'URL_CASE_INSENSITIVE'  =>  true,	

	'PAGE_SIZE'				=>20,
	//自动加载函数文件
	'LOAD_EXT_FILE'=>'',
	/*
	//自动加载自定义类文件（针对非命名空间定义类库）
	'APP_AUTOLOAD_PATH'=>'@.Tool',
	*/
	//加载扩展配置文件
	'LOAD_EXT_CONFIG' =>'db',
	// 开启子域名或者IP配置
	'APP_SUB_DOMAIN_DEPLOY'   =>    1, 
	//子域名配置
	'APP_SUB_DOMAIN_RULES'    =>    array(    
		/* 域名部署配置     
		*格式1: '子域名或泛域名或IP'=> '模块名[/控制器名]';     
		*格式2: '子域名或泛域名或IP'=> array('模块名[/控制器名]','var1=a&var2=b&var3=*');     
		*/ 
		'22.56.78.9'  => 'Home',  // 22.56.78.9指向Home模块
		),
	//加载自定义类库(带命名空间)
	'AUTOLOAD_NAMESPACE' =>array(
		'Space'=>'@.Tool',
		),
	//资源列表
	'TMPL_PARSE_STRING'=>array(
		'__UPLOADS__' => __ROOT__.'/Uploads', // 增加新的上传路径替换规则
		'__FILES__' => __ROOT__.'/Uploads/files', // 增加新的上传路径替换规则
		'__IMAGES__' => __ROOT__.'/Uploads/images', // 增加新的上传路径替换规则
	),
	//默认上传路径
	'DEFAULT_UPLOAD_PATH'=>array(
		'IMAGES' => __ROOT__.'/Uploads/images', 	// 增加新的图片上传路径
		'FILES' => __ROOT__.'/Uploads/files', 		// 增加新的文件上传路径
		),
	/*
	//开启表单验证
	$User = M("User"); // 实例化User对象
	 // 手动进行令牌验证
	 if (!$User->autoCheckToken($_POST)){
	 	// 令牌验证错误
	 }*/
	'TOKEN_ON'      =>    true,  			// 是否开启令牌验证 默认关闭
	'TOKEN_NAME'    =>    '__hash__',    	// 令牌验证的表单隐藏字段名称，默认为__hash__
	'TOKEN_TYPE'    =>    'md5',  			//令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'   =>    true,  			//令牌验证出错后是否重置令牌 默认为true
	// 关闭字段缓存
	'DB_FIELDS_CACHE'		=>false,

	/*
	//D方法模型层名称
	数据层：Home\Model\UserModel 用于定义数据相关的自动验证和自动完成和数据存取接口
	逻辑层：Home\Logic\UserLogic 用于定义用户相关的业务逻辑
	服务层：Home\Service\UserService 用于定义用户相关的服务接口等
	实例化：D('User','Logic');
	*/
	'DEFAULT_M_LAYER'       =>  'Logic', 
);
