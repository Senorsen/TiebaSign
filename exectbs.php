<?php
set_time_limit(3600);	//一小时时间
date_default_timezone_set('Asia/Shanghai');
//###########################################################################################
    require "conn.php";         // $con = mysql_connect("localhost","username","password");
//###########################################################################################
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
$db="test";
mysql_select_db($db, $con);
if(!mysql_set_charset("gbk",$con))
{
    echo "设定字符集失败;an error occured when program set charset";
}
$res_users = mysql_query("SELECT * FROM `tb_user` ORDER BY `id`");
$users = array();
$starttime=time();
echo "开始时间： ".date("H:i:s",$starttime)."\n";
while($row = mysql_fetch_array($res_users))
{
    echo "获取：".$row['desc'];
    $alltb_o = gettb($row['cookies'],$row['filter']);
    echo " - ".count($alltb_o->tbn)."\n";
    array_push($users, (object)array('id'=>$row['id'],'desc'=>$row['desc'],'cookies'=>$row['cookies'],'filter'=>$row['filter'],'alltb'=>$alltb_o));
}
for($i=0;$i<count($users);$i++)
{
    printf("%s\t%s%-30s",date("H:i:s"),"当前签到：",$users[$i]->desc);
    $cnt = count($users[$i]->alltb->tbn);
    $id = $users[$i]->id;
    $tbs = $users[$i]->alltb->tbs;
    $filter = $users[$i]->filter;
    $cookies = $users[$i]->cookies;
    printf("贴吧数：%3d\n", $cnt);
    echo "-----------------\n";
    for($j=0;$j<$cnt;$j++)
    {
        $tb = $users[$i]->alltb->tbn[$j]->tb;
        $fid = $users[$i]->alltb->tbn[$j]->fid;
        $level = $users[$i]->alltb->tbn[$j]->level;
        $exp = $users[$i]->alltb->tbn[$j]->exp;
        do
        {
            if(!UserFilter($tb,$level,$exp,$filter))
            {
                echo "    跳过  $tb\n";
                continue;
            }
            printf("%-30s","    签到  $tb ");
            $ret = sign($cookies,$tbs,$fid,$tb);
            if($ret->no!=2)
            {
                echo "".$ret->str."\n";
            }
            else
            {
                echo "$ret->str|$ret->code\n";
                if($ret->code==160008) sleep(2);
            }
            sleep(1);
        }while($ret->no==2);
    }
    mysql_query("UPDATE `tb_user` SET `last`='".date('Y-m-d')."' WHERE `id`=$id");
    echo "\n";
}
echo date("H:i:s")."    全部签到完成，用时 ".date("i:s",time()-$starttime)."\n";
function sign($cookies,$tbs,$fid,$tb)
{
    $tbsurl='http://c.tieba.baidu.com/c/c/forum/sign';
    $myheader=array("$cookies","Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3 TiebaClient/1.2.1.17");
    $cookies_spl=$cookies.';';
    preg_match('/BDUSS=(.+?);/',$cookies_spl,$match_ck);
    $bduss=$match_ck[1];
    $postdata=array(
                "BDUSS"=>$bduss,
                "_client_id"=>"wappc_1378485686660_60",
                "_client_type"=>"2",
                "_client_version"=>"4.2.2",
                "_phone_imei"=>"540b43b59d21b7a48aaaaad31b08e9a5",
                "fid"=>$fid,
                "kw"=>mb_convert_encoding($tb,"utf-8","gbk"),
                "net_type"=>3,
                "tbs"=>$tbs
                );
    $strsign='';
    foreach($postdata as $t=>$v)
    {$strsign.=$t."=".$v;}
    $md5sign=strtoupper(md5($strsign."tiebaclient!!!"));
    $postdata['sign']=$md5sign;
    $str=curlFetch($tbsurl,$myheader[0],$postdata,$myheader[1]);
    $obj=json_decode($str,true);
    if(is_null($obj))
        return (object)array('no'=>2,'str'=>'未知错误 NULL','code'=>0);
    if($obj["error_code"]==0)
    {
        return (object)array('no'=>0,'str'=>'■增加'.$obj["user_info"]["sign_bonus_point"].'经验值');
    }
    else if($obj["error_code"]==160002)
    {
        return (object)array('no'=>-1,'str'=>'■'.mb_convert_encoding($obj["error_msg"],"gbk","utf-8"),'code'=>$obj["error_code"]);
    }
    else if($obj['error_code']==160004)
    {
        return (object)array('no'=>1,'str'=>'■本吧无签到');
    }
    else
    {
        return (object)array('no'=>2,'str'=>'□'.mb_convert_encoding($obj["error_msg"],"gbk","utf-8"),'code'=>$obj["error_code"]);
    }
}
function gettb($cookies,$filter)
{
    //返回tbs及tbn
    $tbn = array();
    $str = curlFetch("http://tieba.baidu.com/","$cookies");
    preg_match('/forums["][:](.+?[\]])/',$str,$matches);
    $str = $matches[1];
    $tbn_obj = json_decode($str);
    if(is_null($tbn_obj)) return (object)array('tbs'=>'','tbn'=>array());
    for($i=0;$i<count($tbn_obj);$i++)
    {
        array_push($tbn, (object)array('fid'=>$tbn_obj[$i]->forum_id,'tb'=>mb_convert_encoding($tbn_obj[$i]->forum_name,"gbk","utf-8"),'level'=>$tbn_obj[$i]->level_id,'exp'=>$tbn_obj[$i]->cur_score));
    }
    expsort(0,count($tbn)-1,$tbn);  //按经验值排序
    $tbs_obj = json_decode(curlFetch("http://tieba.baidu.com/dc/common/tbs","$cookies"));
    $tbs = $tbs_obj->tbs;
    return (object)array('tbs'=>$tbs,'tbn'=>$tbn);
}
function expsort($l,$r,&$a)
{
    $i=$l;$j=$r;$m=$a[intval(($l+$r)/2)]->exp;
    do{
        while($a[$i]->exp>$m) $i++;
        while($m>$a[$j]->exp) $j--;
        if($i<=$j)
        {
            $n=$a[$i];$a[$i]=$a[$j];$a[$j]=$n;
            $i++;$j--;
        }
    }while($i<=$j);
    if($l<$j) expsort($l,$j,$a);
    if($i<$r) expsort($i,$r,$a);
}
function UserFilter($tbname,$lv,$ex,$filter){$fr=1;eval($filter);return $fr;}
function curlFetch($url, $cookie = "", $data = null, $ua = "")
{
	$ch = curl_init(mb_convert_encoding($url,"UTF-8","GBK"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回字符串，而非直接输出
	curl_setopt($ch, CURLOPT_HEADER, false);   // 不返回header部分
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);   // 设置socket连接超时时间
	/*if (!empty($referer))
	{
		curl_setopt($ch, CURLOPT_REFERER, $referer);   // 设置引用网址
	}*/
	if (!empty($cookie))
	{
		curl_setopt($ch,CURLOPT_HTTPHEADER,array("Cookie: $cookie","User-Agent: $ua"));
	}
	
	if (is_null($data))
	{
		// GET
	}
	else if (is_string($data))
	{
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// POST
	}
	else if (is_array($data))
	{
		// POST
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	}
	$str = curl_exec($ch);
	curl_close($ch);
	return mb_convert_encoding($str,"GBK","UTF-8");
}
?>