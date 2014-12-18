<?php
	/**
	 * 打印函数
	 * @param array $array
	 */
	function p($array){
		dump($array,1,'<pre>',0);	
	}
	/*
	* 得到客户端ip
	* @param $type 
	*/
	function get_browse_ip($type = 0) {
	    $type       =  $type ? 1 : 0;
	    static $ip  =   NULL;
	    if ($ip !== NULL) return $ip[$type];
	    if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实IP
	        $ip=$_SERVER['HTTP_X_REAL_IP'];     
	    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
	        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
	    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
	        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	        $pos    =   array_search('unknown',$arr);
	        if(false !== $pos) unset($arr[$pos]);
	        $ip     =   trim($arr[0]);
	    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
	        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
	    }else{
	        $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    // IP地址合法验证
	    $long = sprintf("%u",ip2long($ip));
	    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	    return $ip[$type];
	}
	/**
	 * 转换彩虹字
	 * @param string $str
	 * @param int $size
	 * @param bool $bold
	 * @return string
	 */
	function color_txt($str,$size=20,$bold=false){
		$len = mb_strlen($str);
		$colorTxt   = '';
		if($bold){
			$bold="bolder";
			$bolder="font-weight:".$bold;
		}
		for($i=0; $i<$len; $i++) {
			$colorTxt .=  '<span style="font-size:'.$size.'px;'.$bolder.'; color:'.rand_color().'">'.mb_substr($str,$i,1,'utf-8').'</span>';
		}
		return $colorTxt;
	}
	
	function rand_color(){
		return '#'.sprintf("%02X",mt_rand(0,255)).sprintf("%02X",mt_rand(0,255)).sprintf("%02X",mt_rand(0,255));
	}
	/**
	 * 替换表情
	 * @param string $content
	 * @return string
	 */
	function replace_phiz($content){
		preg_match_all('/\[.*?\]/is', $content, $arr);
		/**
		 * 替换表情
		 */
		if($arr[0]){
			$phiz=F('phiz','','./data/');
			foreach ($arr[0] as $v){
				foreach ($phiz as $key =>$value){
					if($v=='['.$value.']'){
						$content=str_repeat($v, '<img src="'.__ROOT__.'/Public/Images/phiz/'.$key.'.gif"/>',$content);
						break;
					}
				}
			}
			return $content;
		}
	}
	/**
	 * 截取字符串
	 * @param string $str
	 * @param int $start
	 * @param int $length
	 * @param string $charset
	 * @param bool $suffix
	 * @return string|string
	 */
	function sub_str($str,$start=0,$length,$charset="utf-8",$suffix=true){
		$l=strlen($str);

		if(function_exists("mb_substr"))
			return 	!$suffix?mb_substr($str,$start,$length,$charset):mb_substr($str,$start,$length,$charset)."…";
		else if(function_exists('iconv_substr')){
			return  !$suffix?iconv_substr($str,$start,$length,$charset):iconv_substr($str,$start,$length,$charset)."…";
		}
		$re['utf-8']="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312']="/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']="/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']="/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset],$str,$match);
		$slice = join("",array_slice($match[0],$start,$length));

		if($suffix){
			if($l>$length){
				return $slice."…";
			}else{
				return $slice;
			}
		} 
	}

	function fbanner($arr){
		$str='';
		foreach ($arr as  $v) {
			//./Public/Uploads/
			//$str .='box.add({"url":"'.__ROOT__.'/Public/Uploads/'.$v['path'].'","href":"'.$v['url'].'","title":"'.$v['info'].'"});';
			$str .='box.add({"url":"'.$v['path'].'","href":"'.$v['url'].'","title":"'.$v['info'].'"});';
		}
		return str_replace('./Public/Uploads/','/Public/Uploads/', $str);
	}
	//获取img
	function get_images($str){
		/*preg_match_all('/\s+src\s?\=\s?[\'|"]([^\'|"]*)/is', $str, $match);
		//print_r( );*/
		$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/"; 
		preg_match_all($pattern,$str,$match); 
		return $match; 
	}
	//高亮关键词
	function heigLine($key,$content){
		return preg_replace('/'.$key.'/i', '<font color="red"><b>'.$key.'</b></font>', $content);
	}
	//激活当前导航
	function nav_now($id){
		$str="";
		if(Cookie('cid')==$id){
			$str='nav-item-current';
		}
		return $str;
	}
	//得到当前的栏目
	function get_channel($cid){
		$channel=M('channel')->find($cid);
		
		return $channel['title'];
	}
	function option($arr){
		
		$str='<option value="'.$arr['id'].'">'.$arr['title'].'</option>';
		return $str;
	}
	function get_img($src){
		$str="";
		$img=split(',',$src);
		for ($i=0; $i <= count($img)-2; $i++) { 
			$str.= '<img style="margin-left:10px;" height="50" src="'.$img[$i].'">';
		}
		return $str;
	}
	/**
	 * [get_image 得到图片]
	 * @param  [type] $img [图片资源字符串]
	 * @return [type]      [description]
	 */
	function get_image($img){
		
		$arr=explode(',', $img);
		$str="";
		for ($i=0; $i <=count($arr)-1; $i++) { 
			$str.='<img src="__IMAGE__/'.$arr[$i].'">';
		}
		
		return $str;
	}
	function get_first($img){
		
		$arr=explode(',', $img);
		$str=$arr[0];
		return $str;
	}
	
	function reg($str){		 
		return  _strip_tags(array("p", "br"),$str); 
	}
  
	/**   
	* PHP去掉特定的html标签
	* @param array $string   
	* @param bool $str  
	* @return string
	*/  
	function _strip_tags($tagsArr,$str) {   
	    foreach ($tagsArr as $tag) {  
	        $p[]="/(<(?:\/".$tag."|".$tag.")[^>]*>)/i";  
	    }  
	    $return_str = preg_replace($p,"",$str);  
	    return $return_str;  
	}  
	/**
	 * [tag 截取字符串]
	 * @param  [type] $资源字符串
	 * @param  [type] $开始位置
	 * @param  [type] $截取长度
	 * @return [type] 结果字符串
	 */
	function tagstr($str,$start=0,$length=250){	
		$str=strip_tags(htmlspecialchars_decode($str));
		$temp=mb_substr($str,$start,$length,'utf-8');
		//return (strlen($str)>$length*1.5)?$temp.'...':$temp;
		return $temp;
	}

	/**
	 * [SplitWord 分词]
	 * @param [type] $str [description]
	 */
	function SplitWord($str){
		vendor('SplitWord/SplitWord'); 
		$split=new SplitWord();
		$data=$split->SplitRMM($str);
		p($data);
		$split->Clear();
		return $data;
	}
	/*
     * 邮件发送
     * @param string $to 收件人邮箱，多个邮箱用,分开
     * @param string $title 标题
     * @param string $content 内容
     */

    function send_email($to,$title,$content,$webname="官方网站"){
        import('Class.Mail',APP_PATH);
        //邮件相关变量
        $cfg_smtp_server = 'smtp.163.com';
        $cfg_ask_guestview = '8';
        $cfg_smtp_port = '25';
        $cfg_ask_guestanswer = '8';
        $cfg_smtp_usermail = 'js_weiwei_100@163.com';//你的邮箱
        $cfg_smtp_user = 'js_weiwei_100@163.com';//你的邮箱号
        $cfg_smtp_password = 'wei110120';//你的邮箱密码

        $smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
        $smtp->debug = false;
        
        $cfg_webname=$webname;
        $mailtitle=$title;//邮件标题
        $mailbody=$content;//邮件内容 
                //$to 多个邮箱用,分隔
        $mailtype='html';
        return $smtp->sendmail($to,$cfg_webname,$cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
    }
    /**
     * [NoRand 不重复随机数]
     * @param integer $begin [description]
     * @param integer $end   [description]
     * @param integer $limit [description]
     */
    function NoRand($begin=0,$end=20,$limit=4){
		$rand_array=range($begin,$end);
		shuffle($rand_array);//调用现成的数组随机排列函数
		return implode('',array_slice($rand_array,0,$limit));//截取前$limit个
	}
	/**
	 * [zeroize 数字补足]
	 * @param  int $num    		[带补足数字]
	 * @param  int $length 		[补足长度]
	 * @param  string $fill   	[补足字符]
	 * @param  int $fill   	  	[补足字符]
	 * @return [type]         	[description]
	 */
	function zeroize($num,$length=10,$type=1,$fill='0'){
		$type=$type?STR_PAD_LEFT:STR_PAD_RIGHT;
		return str_pad($num,$length,$fill,$type);
	}

    /**
     * [getKey 根据value得到数组key]
     * @param  [type] $arr   [数组]
     * @param  [type] $value [值]
     * @return [type]        [description]
     */
    function getKey($arr,$value) {
	 	if(!is_array($arr)) return null;
			foreach($arr as $k =>$v) {
			  $return = getKey($v, $value);
			  if($v == $value){
			   	return $k;
			  }
			  if(!is_null($return)){
			   return $return;
			}
		}
	}	
	/**
	 * [php2class 转换成Think默认命名规则类]
	 * e.g:
	 * 修改文件夹下所有的php文件:.php --> .class.php
	 * php2class(__FILE__,'Action\MemberAction.class.php','Tool');
	 * @param [type] $path     		[文件夹路劲]
	 * @param [type] $reg_path 		[要替换文件夹]
	 * @param [type] $sea_path 		[待替换文件夹]
	 * @param  boolean $print    	[是否输出]
	 * @return [type]            	[description]
	 */
	function php2class($path,$reg_path,$sea_path,$print=false){
		$hostdir=!empty($path)?$path:__FILE__;

        if(!empty($reg_path) && !empty($sea_path)){
        	 $hostdir=str_replace($reg_path,$sea_path,$hostdir);
        } 

        $filesnames = scandir($hostdir);
        foreach ($filesnames as $k => $v) {
            if($k>1){ //修改类名
                if(strpos($v,'class')===false){
                    $temp=explode('.', $v);
                    $n=$hostdir.'\\'.$temp[0].'.class.php';
                    $o=$hostdir.'\\'.$v;
                    rename($o,$n);
                    if($print){
                    	p($n);
                    }
                }else{
                	if($print){
                		p($v);
                	}
                }
            }
         }     
	}
	/**
	 * 验证用户密码是否一致
	 * @param [type] $password     		[密码]
	 * @param [type] $repassword 		[确认密码]
	 * @return [type]            	[description]
	 */
	function checkPwd($password,$repassword){
		return ($password===$repassword)?1:0;
	}