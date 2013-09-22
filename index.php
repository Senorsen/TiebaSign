<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>签到日志</title>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="datetime.js"></script>
<script>
var is_b = 0;
var now = new DateTime();
var tstxt='';
$(function(){
    $.ajaxSetup({async:true});
    $("#time").val('今天-'+now.year()+'-'+now.month()+'-'+parseInt(now.day())+'-'+0);
    tstxt = $("#time").val();
    var s = now.year()+'-'+now.month()+'-'+parseInt(now.day())+'-'+now.hour();
    $cache = $('#cache-log');
    $sign = $('#sign-log');
    ra = function(){var a = s.match(/^\d+-\d+-\d+/)[0];$cache.load('log-'+a);};
    rb = function(){now = new DateTime();if($('#time').val() == '' || /今天/.test($('#time').val())){s = now.year()+'-'+now.month()+'-'+parseInt(now.day())+'-'+0;s2='今天-'+s;if($('#time').val() != s2)$('#time').val(s2);if(/全部签到完成/.test($sign.html()))return;}else s=$('#time').val();$sign.load('log-'+s,'',function(){setTimeout("if(is_b) document.body.scrollTop = document.body.scrollHeight;",100);});};
    setInterval("$('#current-time').html(now.year()+'-'+now.month()+'-'+parseInt(now.day())+'-'+now.hour());",1000);
    ra();
    rb();
    setInterval(ra,3*1000);
    setInterval(rb,1000);
    $btn = $('#is-btn');
    $isb = $('#is-b');
    $btn.hover(function(){$(this).stop(0,1).animate({opacity:1});},function(){$(this).stop(0,1).animate({opacity:0.5});}).click(function(){$(this).css("opacity","0.4");setTimeout(function(){$btn.css("opacity","1")},50);is_b ^= 1;$isb.html(is_b);});
});
</script>
</head>

<body>
当前时间：<div id="current-time"></div>
<input id="time" value="这里可以改成你想看的时间，清空则为当前时间" maxlength="200" style="width:300px">
<div id="cache-log"></div>
<hr>
<div id="sign-log"></div>
<div id="is-btn" style="position:fixed;right:100px;bottom:100px;width:60px;height:60px;background:rgba(0,0,0,1);opacity:0.5;color:white">总在底端：<div id="is-b" style="display:inline">0</div></div>
</body>
</html>