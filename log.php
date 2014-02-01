<?php
$str = file_get_contents('log/log-'.$_SERVER['QUERY_STRING'].'.log');
echo str_replace("  ","&nbsp;&nbsp;",nl2br($str));
?>