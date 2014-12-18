<?php
/**
@自定义的用户标签，含有以下的标签:
1、list列表读取标签：
<list model="auth_group" limit="9" order="id desc" field="*">
     <li> <a title="{$result.title}">{$result.title}--{$result.id}</a></li>
</list>
2、article文章详情标签：
<article model="auth_group" id="1" >
  {$article.title}</br>
  上一条：<notempty name="pre">{$pre.title}<else/>没有了</notempty><br/>
  下一条：<notempty name="nxt">{$nxt.title}<else/>没有了</notempty>
</article>
3、nav导航标签，支持多层栏目
<nav model="channel"  where="status eq 0">
  <notempty name="nav">
    <volist name="nav" id="vo">
        {$vo.title}-----{$vo.id}<br/>
    </volist>
  </notempty>
</nav>
4、location面包标签
<location model="channel" current="8" >
    <volist name="location" id="vo">
      <lt name="key" value="1">
        <a href="{$vo.uri}">{$vo.title}</a>&gt;
        <else/>
        <a href="{:U('/test/'.$vo['name'])}">{$vo.title}</a><lt name="key" value="$length">&gt;</lt>
      </lt>
    </volist>     
  </location>
  5、banner大屏滚动
  <banner limit="5" width="950" height="320" auto="true" point="ture" timespan="2000"/>
  6、myad广告调用
  <myad model="channel" limit="5">
      <volist name="myad" id="ad">
        {$ad.title}---{$ad.id}<br/>
      </volist>
  </myad>
  7、漂浮广告
  <myflAd id="1"  width="250" height="120"/> 
  8、对联广告
  <mycoplAd left="1" right="2" width="250" height="120"/> 
  8、flink友情链接调用
  <flink model="channel" limit="5" where="id like '1%' ">
    <volist name="flink" id="link">
      {$link.title}---{$link.id}<br/>
    </volist>
  </flink>
  9、channel单个栏目调用
  <channel model="channel" id="9">
    {$channel.title}---{$channel.id}<br/>
  </channel>
  10、channellist指定栏目的子栏目
  <channellist model="channel" fid="3" where="id between 3 and 9">
    <volist name="channellist" id="l">
      {$l.title}---{$l.id}<br/>
    </volist>
  </channellist>
  11、randomrecom随机推荐文章
  <randomrecom model="channel">
    <volist name="randomrecom" id="com">
        {$com.title}---{$com.id}<br/>
    </volist>
  </randomrecom>
  12、tag单个标签调用
  <tag  model="channel" id="3">
    {$tag.title}-----{$tag.id}<br/>
  </tag>
  13、tags指定栏目标签调用，fid指定栏目下的标签，不填调用所有标签
  <tags  model="channel" fid="3">
    <volist name="tags" id="tgs">
      {$tgs.title}--{$tgs.id}<br/>
    </volist>
  </tags>
  14、banner2大屏滚动
  <banner2 limit="5" limit="5" order="id desc"/>
  15、客服代码,limit调用条数，custom自定义客服QQ(不使用数据库中的QQ),order排序
  <service limit="5" where="status eq 0 " custom="" order=""/>
**/
//import('TagLib');  //引入TagLib 类
class TagLibLists extends TagLib{
    protected $tags = array(
         'list' => array('attr' => 'id,model,field,limit,order,where,empty,date','close' =>1),// attr 属性列表close 是否闭合（0 或者1 默认为1，表示闭合）
         'list2' => array('attr' => 'id,model,field,limit,order,where,empty,date','close' =>1),
         'article' => array('attr' => 'id,field,where','close' =>1),
         'nav' => array('attr' => 'position,where,order','close' =>1),
         'location'=>array('attr' => 'model,field,current,where','close' =>1),
         'banner'=>array('attr' => 'model,field,limit,where,width,height,auto,timespan,date,roll,order,','close' =>0),
         'banner2'=>array('attr' => 'model,field,limit,where,order','close' =>1),
         'myad'=>array('attr' => 'model,field,limit,where,date','close' =>1),
         'myflAd'=>array('attr' => 'model,field,id,where,date,width,height','close' =>0),
         'mycoplAd'=>array('attr' => 'model,field,left,right,where,date,width,height','close' =>0),
         'flink'=>array('attr' => 'model,field,limit,where,position','close' =>1),
         'channel'=>array('attr' => 'model,id,field,limit,where','close' =>1),  
         'channellist'=>array('attr' => 'model,fid,field,limit,where,order','close' =>1),
         'randomrecom'=>array('attr' => 'model,fid,field,limit,where,order','close' =>1),
         'tag'=>array('attr' => 'model,id,field,limit,where,order,date','close' =>1),
         'tags'=>array('attr' => 'model,fid,field,limit,where,order,date','close' =>1),
         'service'=>array('attr' => 'model,field,limit,where,date,custom','close' =>0),
    );
    /**
     * [_list 列表输出结果]
     * @param  [type] $attr    [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function _list ($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Article';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
      $limit=$attr['limit'];//参数$limit，可通过模板传入参数值
      $order=$attr['order'];//$order$limit，可通过模板传入参数值
      $where= $this->adjunct($attr);
      $id=!empty($attr['id'])?$attr['id']:I('id');

      if(!empty($attr['id']) && empty($_REQUEST['id'])){
          if(!empty($where)){
            $where.=' and column_id = '.$id;
          }else{
             $where.=' column_id = '.$id;
          }
      }

      
      $str='<?php ';
      $str .= '$_list_news=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->limit('.$limit.')->order("'.$order.'")->select();';//查询语句 
      $str .='$_column=M("Column")->find("'.$id.'");';      
      $str .='$column=$_column;';
      $str .= 'foreach ($_list_news as $_list_value):';
      //$str .= 'extract($_list_value);';
      $str .= '$list=$_list_value;';
      $str .= '?>';//自定义文章生成路径$url
      $str .= $content;
      $str .='<?php endforeach ?>';
      return $str;
    }
    /**
     * [_list2 文章列表返回数组]
     * @param  [type] $attr    [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function _list2 ($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Article';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
      $limit=$attr['limit'];//参数$limit，可通过模板传入参数值
      $order=$attr['order'];//$order$limit，可通过模板传入参数值
      $where= $this->adjunct($attr);
      $id=!empty($attr['id'])?$attr['id']:I('id');

    
      if(!empty($attr['id']) && empty($_REQUEST['id'])){
          if(!empty($where)){
            $where.=' and column_id = '.$id;
          }else{
             $where.=' column_id = '.$id;
          }
      }else{
          $_res=M($model)->find($id);
          $id=$_res['column_id'];
          if(!empty($where)){
            $where.=' and column_id='.$id;
          }else{
            $where.=' column_id='.$id;
          }
          
      }

      
      $str='<?php ';
      $str .= '$_list_news=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->limit('.$limit.')->order("'.$order.'")->select();';//查询语句 
      $str .='$_column=M("Column")->find("'.$id.'");';      
      $str .='$column=$_column;';
      //$str .= 'extract($_list_value);';
      $str .= '$list2=$_list_news;';
      $str .= '?>';//自定义文章生成路径$url
      $str .= $content;
      return $str;
    }
    /**
     * [_nav 导航]
     * @param  [type] $attr    [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function _nav ($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Column';
      $field=!empty($attr['field'])?$attr['field']:'*';
     
      $limit=$attr['limit'];//参数$limit，可通过模板传入参数值
      $order=$attr['order'];//$order，可通过模板传入参数值
      $position=$attr['position'];
      $where= $this->condition($attr['where']);

     
      if(!empty($position)){
        if(empty($where))
            $where .=' position = '.$position;
        else
            $where .=' and position = '.$position;
      }
      
      $str='<?php ';
      $str .= '$_list_result=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->limit('.$limit.')->order("'.$order.'")->select();';//查询语句
      $str .= 'import("Class.Category",APP_PATH);
               $nav=Category::unlimitedForLevel($_list_result);';
      $str .= '?>';  
      $str .= $content;
      return $str;
    }
    /**
     * [_article 文章]
     * @param  [type] $attr    [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function _article($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Article';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $key=!empty($attr['key'])?$attr['key']:'id';
      $id=!empty($attr['id'])?$attr['id']:I('id');
      
      $str='<?php ';
      $str .= '$_result_content=M("'.$model.'")->field("'.$field.'")->find("'.$id.'");';//查询语句
      $str .= '$_column=M("Column")->find($_result_content["column_id"]);';
      $str .= '$pre=M("'.$model.'")->where("'.$key.' < '.$id.' and column_id=".$_result_content["column_id"])->order("'.$key.' desc")->limit(1)->find();'; //上一条
      $str .= '$nxt=M("'.$model.'")->where("'.$key.' > '.$id.' and column_id=".$_result_content["column_id"])->order("'.$key.' asc")->limit(1)->find();'; //下一条
      $str .= '$article=$_result_content;';
      $str .= '$column=$_column;';
      $str .= '?>';//自定义文章生成路径$url
      $str .= $content;
      return $str;
    }
   /**
    * [_location 面包屑导航]
    * @param  [type] $attr    [description]
    * @param  [type] $content [description]
    * @return [type]          [description]
    */
   public function _location($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Column';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $where= $this->condition($attr['where']);
     
      if(I('id')){
        $_article=M('Article')->find(I('id'));
        $current=!empty($attr["current"])?$attr["current"]:$_article['column_id'];

      }else{
        $current=!empty($attr["current"])?$attr["current"]:I('cid');
      }
      
      

      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0'; 
    
      $str='<?php ';
      $str .= '$_current_column=M("'.$model.'")->field("'.$field.'")->find("'.$current.'"); 
              $_result_content=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->select();
              import("Class.Category",APP_PATH);
              $location=Category::getparents($_result_content,$_current_column);
              $location[]=$_current_column;
              $temp=array(array("id" =>0,"title" => "首页",uri=>__GROUP__));
              $location=array_merge($temp,$location);
              $length=count($location)-1;
            ';
      $str .= '?>';
      $str .= $content;
      return $str;
   }
  /***
    *Banner滚屏图片表
    CREATE TABLE IF NOT EXISTS `think_Banner` (
      `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
      `title` char(80) NOT NULL DEFAULT '' COMMENT '滚屏图片中文名称',
      `name` char(20) NOT NULL DEFAULT '' COMMENT '滚屏图片英文名称',
      `description` char(250) NOT NULL DEFAULT '' COMMENT '滚屏图片简单介绍',
      `image` char(250) NOT NULL DEFAULT '' COMMENT '滚屏图片图片',
      `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
      `effective` int(11) NOT NULL DEFAULT 0 COMMENT '滚屏图片有效时间,在有效时间内会显示',
      `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越小越靠前',
      `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态：0正常，1禁用',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT 'Banner表'
   ***/
  /**
   * [_banner 图片滚屏]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
   public function _banner($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Banner';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $limit=!empty($attr['limit'])?$attr['limit']:5;
      $width=!empty($attr['width'])?$attr['width']:1600;
      $height=!empty($attr['height'])?$attr['height']:'';
      $auto=!empty($attr['auto'])?$attr['auto']:1;
      $point=!empty($attr['point'])?$attr['point']:1;
      $type=!empty($attr['type'])?$attr['type']:'Banner';
      $timespan=!empty($attr['timespan'])?$attr['timespan']:1500;
      $where= $this->condition($attr['where']);
      $date=!empty($attr['date'])?$attr['date']:'';
      $roll=!empty($attr['roll'])?$attr['roll']:0;
      $speed=!empty($attr['speed'])?$attr['speed']:0;
      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0';

      if(!empty($date)){
          $date=explode(' ', $date);
          if(count($date)>1){
            if(count($date)==2){
              $where.=' and create_time between '.strtotime($date[0]).' and '.strtotime($date[1]);
            }else{
              foreach ($date as $v) {
                $temp.= strtotime($v).',';
              }
              $temp=substr($temp, 1, -1);   //去掉最后一个字符
             
              $where.=' create_time IN ('.$temp.')';
            }
          }else{
            $where.=' and create_time = '.strtotime($date[0]);
          }
      }

      $_result=M($model)->field($field)->where($where)->limit($limit)->select();

      $html='';
      if(!empty($_result)){
        
          foreach ($_result as $v) {
              if(!empty($height)){ 
                $image .='<li><img src=\"__ROOT__/'.str_replace('./','', $v["image"]).'\" width=\"'.$width.'\" height=\"'.$height.'\" alt=\"'.$v["title"].'\"></li>';
              }else{
                $image .='<li><img src=\"__ROOT__/'.str_replace('./','', $v["image"]).'\" width=\"'.$width.'\"  alt=\"'.$v["title"].'\"></li>';
              }
              
          }
          if($roll) {
                  $html.='<style type=\"text/css\">
                    *{margin:0;padding:0;}
                    ul.banner-list-content{list-style:none;};
                    ul.banner-list-content>li{color:#666;}
                    ul.banner-list-content>li.active{color:orange;}
                  </style>'; 
                  $html.='<div class=\"banner-container\">
                    <div class=\"banner-image\">
                      <ul class=\"banner-image-content\">
                         '.$image.'
                      </ul>
                    </div>
                  </div>
                  <script src=\"__JS__/jquery.min.js\" text=\"text/javascript\" charset=\"uft-8\"></script>
                  <script src=\"__JS__/bannerBox.js\" text=\"text/javascript\" charset=\"uft-8\"></script>
                  <script type=\"text/javascript\">
                    $(function(){                  
                       $(\".banner-container\").BannerBox({auto:'.$auto.',point:'.$point.',speed:'.$speed.',timespan:'.$timespan.'});
                    });
                  </script>';
              }else{
                $html= '<ul class=\"banner-image-content\">'.$image.'</ul>';
                $html.='<style type=\"text/css\">
                        *{margin:0;padding:0;}
                        ul.banner-list-content{list-style:none;};
                      </style>';
              } 
      }else{
        $html='<b><i>抱歉您还未插入任何Banner图片</i></b>';
      }

      $str='<?php 
              echo "'.$html.'\n";
            ?>';
      $str .= $content;
      return $str;
   }
  /**
   * [_banner2 Banner无格式调用]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _banner2($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Banner';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $where= $this->condition($attr['where']);
      $limit=$attr['limit'];
      $order=$attr['order'];
      

      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0'; 

      $str='<?php ';
      $str .= '$_list_banner2=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->limit('.$limit.')->order("'.$order.'")->select();';//查询语句 
      $str .= '$_i=0; foreach ($_list_banner2 as $_list_value):';
      //$str .= 'extract($_list_value);';
      $str .= '$banner2=$_list_value;';
      $str .= '$key=$_i;$_i++;';
      $str .= '?>';//自定义文章生成路径$url
      $str .= $content;
      $str .='<?php endforeach ?>';
      return $str;

    } 
   /***
    *Ad广告表
    CREATE TABLE IF NOT EXISTS `think_Ad` (
      `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
      `title` char(80) NOT NULL DEFAULT '' COMMENT '广告中文名称',
      `name` char(20) NOT NULL DEFAULT '' COMMENT '广告英文名称',
      `description` char(250) NOT NULL DEFAULT '' COMMENT '广告简单介绍',
      `image` char(250) NOT NULL DEFAULT '' COMMENT '广告图片',
      `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
      `effective` int(11) NOT NULL DEFAULT 0 COMMENT '广告有效时间,在有效时间内会显示',
      `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越小越靠前',
      `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态：0正常，1禁用',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   ***/
   /**
    * [_myad 广告图片]
    * @param  [type] $attr    [description]
    * @param  [type] $content [description]
    * @return [type]          [description]
    */
   public function _myad($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Ad';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
      $where= $this->condition($attr['where']);
      $limit=$attr['limit'];
      $order=$attr['order'];

      if(empty($where))
          $where.='status=0';
        else
          $where.=' and status=0'; 

      //有效时间
      if(!empty($date)){
          $date=explode(' ', $date);
          if(count($date)>1){
            if(count($date)==2){
              $where.=' and create_time between '.strtotime($date[0]).' and '.strtotime($date[1]);
            }else{
              foreach ($date as $v) {
                $temp.= strtotime($v).',';
              }
              $temp=substr($temp, 1, -1);   //去掉最后一个字符
             
              $where.=' create_time IN ('.$temp.')';
            }
          }else{
            $where.=' and create_time = '.strtotime($date[0]);
          }
      }

      $str='<?php 
          $_result_content=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->order("'.$order.'")->limit("'.$limit.'")->select();
          //p(M("'.$model.'")->getlastsql());
          $myad=$_result_content;
      ?>';
      $str .= $content;
      return $str;
   }

  /**
   * [_mycoplAd 对联广告]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _mycoplAd($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Ad';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
      $width=!empty($attr['width'])?$attr['width']:0;
      $height=!empty($attr['height'])?$attr['height']:0;
      $where= $this->condition($attr['where']);
      $left=!empty($attr['left'])?$attr['left']:0;
      $right=!empty($attr['right'])?$attr['right']:0;
      $order=$attr['order'];

      if(empty($where))
          $where.='status=0';
        else
          $where.=' and status=0'; 

      //有效时间
      if(!empty($date)){
          $date=explode(' ', $date);
          if(count($date)>1){
            if(count($date)==2){
              $where.=' and create_time between '.strtotime($date[0]).' and '.strtotime($date[1]);
            }else{
              foreach ($date as $v) {
                $temp.= strtotime($v).',';
              }
              $temp=substr($temp, 1, -1);   //去掉最后一个字符
             
              $where.=' create_time IN ('.$temp.')';
            }
          }else{
            $where.=' and create_time = '.strtotime($date[0]);
          }
      }

      $_result_left=M($model)->field($field)->where($where)->order($order)->limit($limit)->find($left);
      $_result_right=M($model)->field($field)->where($where)->order($order)->limit($limit)->find($right);

      $html='<script src=\"__JS__/ad/ad.js\" text=\"text/javascript\" charset=\"uft-8\"></script>\n';
      if(!empty($_result_left) && !empty($_result_right)){
          if($width && $height){
              $lad='<a style=\"display:block;\" href=\"'.$_result_left['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_left['image'],1).'\" width=\"'.$width.'\" height=\"'.$height.'\" border=\"0\"></a>';
              $rad='<a style=\"display:block;\" href=\"'.$_result_right['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_right['image'],1).'\" width=\"'.$width.'\" height=\"'.$height.'\" border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv1\",\"document.body.clientWidth-'.$width.'\",230,\"'.addslashes($rad).'\");
                    theFloaters.addItem(\"followDiv2\",20,230,\"'.addslashes($lad).'\");
                    theFloaters.play();
                  </script>
               '; 
          }elseif($width){
              $lad='<a style=\"display:block;\" href=\"'.$_result_left['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_left['image'],1).'\" width=\"'.$width.'\" border=\"0\"></a>';
              $rad='<a style=\"display:block;\" href=\"'.$_result_right['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_right['image'],1).'\" width=\"'.$width.'\"  border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv1\",\"document.body.clientWidth-'.$width.'\",230,\"'.addslashes($rad).'\");
                    theFloaters.addItem(\"followDiv2\",20,230,\"'.addslashes($lad).'\");
                    theFloaters.play();
                  </script>
               ';
          }else{
              $lad='<a style=\"display:block;\" href=\"'.$_result_left['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_left['image'],1).'\" width=\"120\" height=\"150\" border=\"0\"></a>';
              $rad='<a style=\"display:block;\" href=\"'.$_result_right['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_right['image'],1).'\" width=\"120\" height=\"150\" border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv1\",\"document.body.clientWidth-120\",230,\"'.addslashes($rad).'\");
                    theFloaters.addItem(\"followDiv2\",20,230,\"'.addslashes($lad).'\");
                    theFloaters.play();
                  </script>
               ';
          }
          
      }elseif(!empty($_result_left)){
          if($width && $height){
              $lad='<a style=\"display:block;\" href=\"'.$_result_left['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_left['image'],1).'\" width=\"'.$width.'\" height=\"'.$height.'\" border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv2\",20,230,\"'.addslashes($lad).'\");
                    theFloaters.play();
                  </script>
               '; 
          }elseif($width){
              $lad='<a style=\"display:block;\" href=\"'.$_result_left['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_left['image'],1).'\" width=\"'.$width.'\" border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv2\",20,230,\"'.addslashes($lad).'\");
                    theFloaters.play();
                  </script>
               ';
          }else{
              $lad='<a style=\"display:block;\" href=\"'.$_result_left['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_left['image'],1).'\" width=\"120\" height=\"150\" border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv2\",20,230,\"'.addslashes($lad).'\");
                    theFloaters.play();
                  </script>
               ';
          }
      }elseif(!empty($_result_right)){
          if($width && $height){
              $rad='<a style=\"display:block;\" href=\"'.$_result_right['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_right['image'],1).'\" width=\"'.$width.'\" height=\"'.$height.'\" border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv1\",\"document.body.clientWidth-'.$width.'\",230,\"'.addslashes($rad).'\");
                    theFloaters.play();
                  </script>
               '; 
          }elseif($width){
              $rad='<a style=\"display:block;\" href=\"'.$_result_right['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_right['image'],1).'\" width=\"'.$width.'\"  border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv1\",\"document.body.clientWidth-'.$width.'\",230,\"'.addslashes($rad).'\"); 
                    theFloaters.play();
                  </script>
               ';
          }else{
              $rad='<a style=\"display:block;\" href=\"'.$_result_right['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_right['image'],1).'\" width=\"120\" height=\"150\" border=\"0\"></a>';
               $html .='
                  <script type=\"text/javascript\">
                    var theFloaters  = new floaters();
                    theFloaters.addItem(\"followDiv1\",\"document.body.clientWidth-120\",230,\"'.addslashes($rad).'\");
                    theFloaters.play();
                  </script>
               ';
          }
      }else{
        $html='<span style=\"color:red\">请输入正确的漂浮广告ID</span>';
      }
      
      $str='<?php 
              echo "'.$html.'\n";
            ?>';
      $str .= $content;
      return $str;
  }

  /**
   * [_myflAd 漂浮广告]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _myflAd($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Ad';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
      $width=!empty($attr['width'])?$attr['width']:0;
      $height=!empty($attr['height'])?$attr['height']:0;
      $where= $this->condition($attr['where']);
      $id=!empty($attr['id'])?$attr['id']:0;
      $order=$attr['order'];

      if(empty($where))
          $where.='status=0';
        else
          $where.=' and status=0'; 

      //有效时间
      if(!empty($date)){
          $date=explode(' ', $date);
          if(count($date)>1){
            if(count($date)==2){
              $where.=' and create_time between '.strtotime($date[0]).' and '.strtotime($date[1]);
            }else{
              foreach ($date as $v) {
                $temp.= strtotime($v).',';
              }
              $temp=substr($temp, 1, -1);   //去掉最后一个字符
             
              $where.=' create_time IN ('.$temp.')';
            }
          }else{
            $where.=' and create_time = '.strtotime($date[0]);
          }
      }

      if($id){
          $_result_content=M($model)->field($field)->where($where)->order($order)->limit($limit)->find($id);
          
          $html='<script src=\"__JS__/ad/ad.js\" text=\"text/javascript\" charset=\"uft-8\"></script>\n';
          if(!empty($_result_content)){
              if($width && $height){
                $flad='<a href=\"'.$_result_content['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_content['image'],1).'\" width=\"'.$width.'\" height=\"'.$height.'\" border=\"0\"></a>';
                 $html .='
                    <script type=\"text/javascript\">
                      var ad=new ad();
                      ad.addItem(\"'.addslashes($flad).'\");
                      ad.play();
                    </script>
                 '; 

              }elseif ($width || $height) {
                if($width){
                   $flad='<a href=\"'.$_result_content['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_content['image'],1).'\" width=\"'.$width.'\" border=\"0\"></a>';
                   $html .='
                      <script type=\"text/javascript\">
                        var ad=new ad();
                        ad.addItem(\"'.addslashes($flad).'\");
                        ad.play();
                      </script>
                   '; 
                }else{
                   $flad='<a href=\"'.$_result_content['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_content['image'],1).'\" height=\"'.$height.'\" border=\"0\"></a>';
                   $html .='
                      <script type=\"text/javascript\">
                        var ad=new ad();
                        ad.addItem(\"'.addslashes($flad).'\");
                        ad.play();
                      </script>
                   ';   
                }
              }else{
                 $flad='<a href=\"'.$_result_content['url'].'\" target=\"_blank\"><img src=\"__ROOT__'.substr($_result_content['image'],1).'\" width=\"80\" height=\"80\" border=\"0\"></a>';
                 $html .='
                    <script type=\"text/javascript\">
                      var ad=new ad();
                      ad.addItem(\"'.addslashes($flad).'\");
                      ad.play();
                    </script>
                 '; 
              }
          }else{
            $html='<span style=\"color:red\">请输入正确的漂浮广告ID</span>';
          }
      }else{
        $html ='<span style=\"color:red\">请输入漂浮广告的ID</span>';
      }
      
      $str='<?php 
              echo "'.$html.'\n";
            ?>';
      $str .= $content;
      return $str;
  }

  /***
    *Flink友情链接
    CREATE TABLE IF NOT EXISTS `think_Flink` (
      `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
      `title` char(80) NOT NULL DEFAULT '' COMMENT '友情链接中文名称',
      `name` char(20) NOT NULL DEFAULT '' COMMENT '友情链接英文名称',
      `description` char(250) NOT NULL DEFAULT '' COMMENT '友情链接简单介绍',
      `ico` char(250) NOT NULL DEFAULT '' COMMENT '友情链接图标',
      `url` char(250) NOT NULL DEFAULT '' COMMENT '友情链接链接指向,链接到的地址',
      `position` int(1) NOT NULL DEFAULT 0 COMMENT '友情链接位置：1首页，2内页',
      `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
      `effective` int(11) NOT NULL DEFAULT 0 COMMENT '友情链接有效时间,在有效时间内会显示',
      `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越小越靠前',
      `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态：0正常，1禁用',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   ***/
  /**
   * [_flink 友情链接读取]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _flink($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Flink';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $position=!empty($attr['position'])?$attr['position']:'';
      
      $where= $this->condition($attr['where']);
      $limit=$attr['limit'];
      $order=$attr['order'];

      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0';

      if(!empty($position)){
          $where.=' and position='.$position;
      }

      $str='<?php 
          $_result_content=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->order("'.$order.'")->limit("'.$limit.'")->select();
          $flink=$_result_content;
      ?>';
      $str .= $content;
      return $str;
   }
  /***
    *Column栏目表
    CREATE TABLE IF NOT EXISTS `think_Column` (
      `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
      `title` char(80) NOT NULL DEFAULT '' COMMENT '栏目中文名称',
      `name` char(20) NOT NULL DEFAULT '' COMMENT '栏目英文名称',
      `description` char(250) NOT NULL DEFAULT '' COMMENT '栏目简单介绍',
      `banner` char(250) NOT NULL DEFAULT '' COMMENT '栏目Banner',
      `iamge` char(250) NOT NULL DEFAULT '' COMMENT '栏目图片',
      `ico` char(250) NOT NULL DEFAULT '' COMMENT '栏目图标',
      `position` int(1) NOT NULL DEFAULT 0 COMMENT '栏目位置：1头部，2中部，3左侧，4右侧，5底部',
      `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
      `effective` int(11) NOT NULL DEFAULT 0 COMMENT '栏目有效时间,在有效时间内会显示',
      `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越小越靠前',
      `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态：0正常，1禁用',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   ***/
  /**
   * [_channel 单个栏目读取]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _channel($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Column';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $id=$attr['id'];
      $where= $this->condition($attr['where']);
      $limit=$attr['limit'];
      $order=$attr['order'];

      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0'; 
     
      $str='<?php 
          $channel=M("'.$model.'")->field("'.$field.'")->find("'.$id.'");
      ?>';
      $str .= $content;
      return $str;
  }
  //调用子栏目列表
  public function _channellist($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Column';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $fid=!empty($attr['fid'])?$attr['fid']:$_GET['cid'];
      $where= $this->condition($attr['where']);
      $limit=$attr['limit'];
      $order=$attr['order'];
      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0';

      if(!empty($fid)){
          $where.=' and fid = '.$fid;
      }

      $str='<?php 
          $_result_channellist=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->order("'.$order.'")->limit("'.$limit.'")->select(); 

          $channellist=$_result_channellist;
      ?>';
      $str .= $content;
      return $str;
  }
  /***
    *Article文章表
    CREATE TABLE IF NOT EXISTS `think_Article` (
      `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
      `column_id` int(1) NOT NULL DEFAULT 0 COMMENT '所属栏目',
      `title` char(80) NOT NULL DEFAULT '' COMMENT '文章中文名称',
      `name` char(20) NOT NULL DEFAULT '' COMMENT '文章英文名称',
      `description` char(250) NOT NULL DEFAULT '' COMMENT '栏目简单介绍',
      `iamge` char(250) NOT NULL DEFAULT '' COMMENT '文章图片',
      `com` int(1) NOT NULL DEFAULT 0 COMMENT '推荐，0否，1是',
      `hot` int(1) NOT NULL DEFAULT 0 COMMENT '最热，0否，1是',
      `new` int(1) NOT NULL DEFAULT 0 COMMENT '最新，0否，1是',
      `head` int(1) NOT NULL DEFAULT 0 COMMENT '头条，0否，1是',
      `top` int(1) NOT NULL DEFAULT 0 COMMENT '置顶，0否，1是',
      `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
      `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越小越靠前',
      `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态：0正常，1禁用',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   ***/
  /**
   * [_randomrecom 随机推荐文章]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _randomrecom($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Article';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $fid=$attr['fid'];
      $where= $this->condition($attr['where']);
      $limit=!empty($attr['limit'])?$attr['limit']:2;
      $order=!empty($attr['order'])?$attr['order']:'id';

      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0';
      
      if(!empty($fid)){
          $where.=' and fid = '.$fid;
      }

      $DB_PREFIX=C('DB_PREFIX');
      /*$sql='SELECT *  FROM `'.$DB_PREFIX.$model.'` AS t1 JOIN 
            (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `'.$DB_PREFIX.$model.'`)-(SELECT MIN(id) FROM `'.$DB_PREFIX.$model.'`))+(SELECT MIN(id) FROM `'.$DB_PREFIX.$model.'`)) AS id) AS t2
            WHERE t1.id >= t2.id ORDER BY t1.'.$order.' LIMIT '.$limit;*/
      $sql='SELECT * FROM `'.$DB_PREFIX.$model.'`
          WHERE id >= (SELECT floor(RAND() * (SELECT MAX(id) FROM `'.$DB_PREFIX.$model.'`))) and '.$where.' 
          ORDER BY id LIMIT '.$limit;
      $str='<?php 
          $_result_randomrecom=M("'.$model.'")->query("'.$sql.'");  
          $randomrecom=$_result_randomrecom;
      ?>';
      $str .= $content;
      return $str;
   }
   /***
    *Tag标签表
    CREATE TABLE IF NOT EXISTS `think_Tag` (
      `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
      `column_id` int(1) NOT NULL DEFAULT 0 COMMENT '所属栏目',
      `title` char(80) NOT NULL DEFAULT '' COMMENT '标签中文名称',
      `name` char(20) NOT NULL DEFAULT '' COMMENT '标签英文名称',
      `description` char(250) NOT NULL DEFAULT '' COMMENT '标签简单介绍',
      `content` ntext NOT NULL DEFAULT '' COMMENT '标签内容',
      `position` int(1) NOT NULL DEFAULT 0 COMMENT '标签位置：定位标签',
      `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
      `effective` int(11) NOT NULL DEFAULT 0 COMMENT '标签有效时间,在有效时间内会显示',
      `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越小越靠前',
      `status` int(1) NOT NULL DEFAULT 0 COMMENT '状态：0正常，1禁用',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   ***/
  /**
   * [_tag 标签调用]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _tag($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Tag';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
     
      $id=$attr['id'];
      $where= $this->condition($attr['where']);
      $limit=!empty($attr['limit'])?$attr['limit']:2;
      $order=!empty($attr['order'])?$attr['order']:'id';

      if(empty($where))
          $where.='status=0';
        else
         $where.=' and status=0';
      
      if(!empty($id)){
          $where.=' and id = '.$id;
      }

      if(!empty($date)){
          $date=explode(' ', $date);
          if(count($date)>1){
            if(count($date)==2){
              $where.=' and create_time between '.strtotime($date[0]).' and '.strtotime($date[1]);
            }else{
              foreach ($date as $v) {
                $temp.= strtotime($v).',';
              }
              $temp=substr($temp, 1, -1);   //去掉最后一个字符
              $where.=' create_time IN ('.$temp.')';
            }
          }else{
            $where.=' and create_time = '.strtotime($date[0]);
          }
      }
      
      $str='<?php 
          $_result_tag=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->find(); 
          $tag=$_result_tag;          
      ?>';
      $str .= $content;
      return $str;
   }

  /**
   * [_tags 标签调用]
   * @param  [type] $attr    [description]
   * @param  [type] $content [description]
   * @return [type]          [description]
   */
  public function _tags($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Tag';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
      $limit=$attr['limit'];
      $order=$attr['order'];
      $id=$attr['fid'];

      $where= $this->adjunct($attr);  //条件

      if(!empty($id)){
          $where.=' and fid = '.$id;
      }
            
      $str='<?php 
          $_result_tags=M("'.$model.'")->field("'.$field.'")->where("'.$where.'")->order("'.$order.'")->limit("'.$limit.'")->select(); 
          $tags=$_result_tags;          
      ?>';
      $str .= $content;
      return $str;
   }
   /*
    --
    -- 表的结构 `think_service`
    --
    CREATE TABLE IF NOT EXISTS `think_service` (
      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自动增长',
      `name` varchar(100) NOT NULL COMMENT '客服名称',
      `connect` varchar(50) NOT NULL COMMENT '联系方式',
      `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用：0是，1否',
      `create_time` int(11) NOT NULL COMMENT '添加时间',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='客服表' AUTO_INCREMENT=1 ;
    */
   /**
    * [_service 客服]
    * @param  [type] $attr    [description]
    * @param  [type] $content [description]
    * @return [type]          [description]
    */
   public function _service($attr,$content){
      $attr = $this->parseXmlAttr($attr);
      $model=!empty($attr['model'])?$attr['model']:'Service';
      $field=!empty($attr['field'])?$attr['field']:'*';
      $date=!empty($attr['date'])?$attr['date']:'';
      $limit=$attr['limit'];
      $order=$attr['order'];
      $custom=!empty($attr['custom'])?$attr['custom']:'';
   
      $where= $this->adjunct($attr);  //条件
      if(empty($custom)){
          $_result_content=M($model)->field($field)->where($where)->order($order)->limit($limit)->select();
         
          foreach ($_result_content as  $v) { 
            $qq .= $v['connect'].":".'|'.$v['name'].'*';
          }
      }else{
           $qq .=$custom;
      }
    

      $html='<link rel=\"stylesheet\" href=\"__JS__/qq/skin/style.css\">
        <script type=\"text/javascript\"> \$url=\"__URL__\";\$js=\"__JS__\";</script>
        <script type=\"text/javascript\" src=\"__JS__/qq/js/jquery.min.js\"></script>
        <script type=\"text/javascript\" src=\"__JS__/qq/js/jquery.kf.js\"></script>
        <script type=\"text/javascript\">
            $(function(){
              $(\"body\").kefu({qq:\"'.$qq.'\"});
            });
        </script>
        ';
      $str='<?php 
              echo "'.$html.'\n";
            ?>';
      $str .= $content;
      return $str;
   }

   //重写查询符号
   private function condition($str){
      if(strstr($str,'neq') || strstr($str,'eq')){
          if(strstr($str,'neq')){
              $str=str_replace('neq', '!=', $str);
          }else{
              $str=str_replace('eq', '=', $str);
          }
      }

      if(strstr($str,'elt') || strstr($str,'lgt') || strstr($str,'lt') ){
         if(strstr($str,'elt')){
            $str=str_replace('elt', '<=', $str);
         }elseif(strstr($str,'lgt')){
            $str=str_replace('lgt', '<>', $str);
         }else{
            $str=str_replace('lt', '<', $str);
         }
      }

      if(strstr($str,'gt') || strstr($str,'egt')){
         
          if(strstr($str,'egt')){
             $str=str_replace('egt', '>=', $str);
          }else{
             $str=str_replace('gt', '>', $str);
          }
      }
      return $str;
   }

  //重组查询条件
  private function adjunct($attr){
      $date=!empty($attr['date'])?$attr['date']:'';
      $where= $this->condition($attr['where']);

      if(empty($where)){
           $where.='status=0';
      }else{
        if(strpos($where,'status') === false){
            $where.=' and status=0';
        }  
      }
        

      if(!empty($date)){
          $date=explode(' ', $date);
          if(count($date)>1){
            if(count($date)==2){  //两个区间查询
              $where.=' and create_time between '.strtotime($date[0]).' and '.strtotime($date[1]);
            }else{
              foreach ($date as $v) { //两个以上存在查询
                $temp.= strtotime($v).',';
              }
              $temp=substr($temp, 1, -1);   //去掉最后一个字符
             
              $where.=' and create_time IN ('.$temp.')';
            }
          }else{  //一个精确查询
            $where.=' and create_time = '.strtotime($date[0]);
          }
      }

      return $where;
  }
}