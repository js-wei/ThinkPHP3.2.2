<?php
	import('TagLib');
	Class TagLibHd extends TagLib{
		Protected $tags = array(
			'nav' => array('attr' => 'order', 'close' => 1)
		);
		Public function _nav($attr, $content){
			$attr = $this->parseXmlAttr($attr);
			$order=$attr['order'];
			$str = <<<str
<?php
					\$_nav_cate = M('channel')->order("{$order}")->select();
					import('Class.Category', APP_PATH);
					\$_nav_cate = Category::unlimitForLevel(\$_nav_cate);
					foreach(\$_nav_cate as \$_nav_cate_v):
						extract(\$_nav_cate_v);
					
?>
str;
			$str .= $content;
			$str .= '<?php endforeach;?>';
			return $str;
		}
	}
?>