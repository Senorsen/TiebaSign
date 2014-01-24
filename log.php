<?php
$str = file_get_contents('log/log-'.$_GET['get'].'.log');
echo str_replace("  ","&nbsp;&nbsp;",nl2br($str));
?>