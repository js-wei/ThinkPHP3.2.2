<?php
return array(
	//'配置项'=>'配置值'
	//模板替换
	'TMPL_PARSE_STRING'  =>array(     
		'__PUBLIC__' => __ROOT__.'/Public', // 更改默认的/Public 替换规则     
		'__JS__'     => __ROOT__.'/Public/Js', // 增加新的JS类库路径替换规则
		'__CSS__'     => __ROOT__.'/Public/Css', // 增加新的CSS类库路径替换规则     
		'__UPLOAD__' => __ROOT__.'/Uploads', // 增加新的上传路径替换规则
		),
	//设置模板后缀
	'TMPL_TEMPLATE_SUFFIX'	=>	'.html',
	// 多个伪静态后缀设置 用|分割
	'URL_HTML_SUFFIX' => 'html|shtml|xml',
	// URL禁止访问的后缀设置
	'URL_DENY_SUFFIX' => 'pdf|ico|png|gif|jpg',
);