<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html lang="en-us">
<head>
    <!--拥有者:魏巍
        创建日期:2014-03-13 15:16:04
        编辑器:Editplus-->
	<meta charset="utf-8">
	<title>管理后台</title>
	<meta name="keywords" content="你若你记得我，别人会记得你"/>
	<meta name="description" content="你若懂我，别人也懂你"/>
	<meta name="author" content="魏巍"/>
	<meta name="generator" content="Editplus"/>
	<meta name="robots" content="all"/>
	<link href="./Application/Admin/Public/content/css/default.css" rel="stylesheet" type="text/css" />
    <script src="./Application/Admin/Public/Scripts/jquery-1.4.1.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function () {
            window.InitHeight = function () {
                var h = document.body.clientHeight;
                if (window.main_height != h) {
                    window.main_height = h;
                    $('#left').height(300);
                    $('#main_right').height(300);
                    var height = document.body.clientHeight - 58 - 27;
                    $(".main").height(height);
                    $('#left').height(height);
                    $("#main_right").height(height);
                    $(window.frames["main_right"].document).find('[id^="main_right"]').height(height - 5);
                }
            }
            setInterval("InitHeight()", 100)
            var maxW = 230;
            var minW = 12;
            $("#frameBar").click(function () {
                if ($(this).attr("isclose") == "0") {
                    $('#frameLeft').stop(true, false).animate({ left: (minW - maxW) }, 800);
                    $('#frameLeft').parent().width(minW);
                    $(this).removeClass("bar_c");
                    $(this).addClass("bar_o");
                    $(this).attr("isclose", 1);
					$("#open").html("&raquo;");
					$(this).attr('title','打开菜单');
                } else {
                    $('#frameLeft').stop(true, false).animate({ left: 0 }, 800);
                    $('#frameLeft').parent().width(maxW);
                    $(this).removeClass("bar_o");
                    $(this).addClass("bar_c");
                    $(this).attr("isclose", 0);
					$("#open").html("&laquo;");
					$(this).attr('title','关闭菜单');
					
                }
            });
            //setTimeout("onContentLoad();", 10);
        });
         
    </script>
    <style type="text/css">
        html, body { width: 100%; min-width: 1024px; height: 100%; }
    </style>
</head>
<body>
	 <!-- header start -->
    <div id="header" class="clearfix" style="height: 80px; border-bottom: 5px solid white;
        background: url(./Application/Admin/Public/images/line.png) repeat-x 0px 0px">
        <img src="./Application/Admin/Public/images/left.png" alt="" class="f_l" />
        <img src="./Application/Admin/Public/images/right.png" alt="" class="f_r" />
    </div>
    <!-- header end -->
    <!-- main start -->
    <table cellpadding="0" cellspacing="0"  class="main">
        <tr>
            <td style="width: 230px">
                <div id="frameLeft" class="t_l">
                    <div class="t_l_c">
                        <iframe frameborder="0" id="left" name="left" scrolling="auto" src="<?php echo U('Index/nav');?>" style="width: 220px;
                            visibility: visible; z-index: 2;" height="100%">浏览器不支持嵌入式框架，或被配置为不显示嵌入式框架。</iframe>
                        <div title="关闭菜单" id="frameBar" isclose="0" class="bar_c">
                            <font style="color:#ff9966;font-size:30px;height:50px;left:215px;width:20px;" id="open">&laquo;</font></div>
                    </div>
                </div>
            </td>
            <td class="t_r">
                <iframe width="100%" height="100%" frameborder="0" marginwidth="0" marginheight="0"
                    scrolling="auto" src="<?php echo U('Index/main');?>" id="main_right" name="main_right"></iframe>
            </td>
        </tr>
    </table>
	
    <!-- main end -->
    <!-- footer start -->
    <!-- footer end -->
</body>
</html>