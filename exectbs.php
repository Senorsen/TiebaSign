<?php 
set_time_limit(3600);	//一小时时间
date_default_timezone_set('PRC');
$users = array();
$starttime=time();
$usertime = array();
echo "Senor 森 贴吧自动签到系统开始工作。\n\n";
echo "开始时间： ".date("Y-m-d D H:i:s",$starttime)."\n";

if ($argc >= 2) {
switch ($argv[1]) {
case "cachetb": {
    //###########################################################################################
        require "../Conn/Conn.php";
    //###########################################################################################
    $db = @senor_conn();
    if (mysqli_connect_errno())
    {
        echo('Could not connect: ' . mysqli_error());
    }
    else if(! @$db->set_charset("utf8"))
    {
        echo("Could not connect: an error occured when program set charset");
    }
    $result = $db->query("SELECT * FROM `tb_user` ORDER BY `id`");
    //Tieba info Cache
    echo "模式：缓存贴吧……\n";
    echo "---------------\n";
    $rogue_n = array('浙江大学', '连云小森森');
    $rogue = array();
    foreach ($rogue_n as $value) {
        echo "rogue-get: ".$value." = ";
        $fid = 0;
        while(!$fid) {
            $fid = getfid($value);
        }
        echo $fid."\n";
        array_push($rogue, (object)array('tb' => $value, 'fid' => $fid, 'force' => 1));
    }
    echo "---------------\n";
    while($row = $result->fetch_array())
    {
        echo "获取： ".$row['id'].' '.$row['nick'].' ';
        $alltb_o = NULL;
        $i = 5;
        $tbs = login_validate($row['cookies']);
        $username = 'username';
        if (!$tbs) {
            echo " *** 登陆状态失效。\n";
            array_push($users, (object)array('id'=>$row['id'],'nick'=>$row['nick'],'wantmail'=>$row['wantmail'],'email'=>$row['email'],'username'=>$username,'cookies'=>$row['cookies'],'filter'=>$row['filter'],'tbs'=>0));
            continue;
        }
        $username = FALSE;
        $tb_home_obj = null;
        while ($username === FALSE) {
            $username = FALSE;
            echo '^';
            while ($tb_home_obj == '' || is_null($tb_home_obj)) {
                echo '*';
                $tb_home_obj = get_tbhome($row['cookies']);
            }
            $username = get_username($tb_home_obj);
        }
        echo '('.$username.') ';
        $alltb_o = gettb($tb_home_obj,$row['cookies'],$row['filter']);
        echo " - ".count($alltb_o->tbn)." - ".($alltb_o->valid)."\n";
        $alltb_o->tbn = array_merge($rogue, $alltb_o->tbn);
        array_push($users, (object)array('id'=>$row['id'],'nick'=>$row['nick'],'wantmail'=>$row['wantmail'],'email'=>$row['email'],'username'=>$username,'cookies'=>$row['cookies'],'filter'=>$row['filter'],'tbs'=>$tbs,'alltb'=>$alltb_o));
    }
    fwrite(fopen("tbcache.serialize","w"),serialize($users));
    fwrite(fopen("cache/tbcache.".date('Y-m-d', time()).".serialize","w"),serialize($users));
    echo "获取完毕\n-------------------\n";
    break;
}
case "check": {
    echo "检查模式\n";
    require 'sendmail.php';
    $obj = unserialize(file_get_contents('tbcache.serialize'));
    foreach ($obj as $key => $value) {
        if (!$value->tbs) {
            alert_loginfail($value->id);
        }
    }
    break;
}
default: {
    echo "错误：未指定操作。\n";
    break;
}
}
} else {
    // 无参数即 Tieba Sign
    $wait_flag = 0;
    if (3600*24-(time()+3600*8)%(3600*24)<=120) {
       $wait_flag = 1;
    }
    // 取消wait_flag
    $wait_flag = 0;
    echo "模式：签到\n";
    $users = unserialize(file_get_contents("tbcache.serialize"));
    if(is_null($users))
    {
        echo "获取缓存失败！(请联系您的系统管理员 >_<)\n";
        echo "\n-------------------------------------$\n";
    }
    for($i=0;$i<count($users);$i++)
    {
//        sleep(rand(2, 5));
        //if($i<count($users)-1) continue;
        printf("%s\t%s%s(%s)",date("H:i:s"),"当前签到：",$users[$i]->nick,$users[$i]->username);
        if (!$users[$i]->tbs) {
            echo " 未登录！？\n";
            continue;
        }
        $cnt = count($users[$i]->alltb->tbn);
        $id = $users[$i]->id;
        $tbs = $users[$i]->tbs;
        $filter = $users[$i]->filter;
        $cookies = $users[$i]->cookies;
        $tbs = login_validate($cookies);
        if (!$tbs) {
            echo ' 「登录状态最近失效」 '."\n";
            continue;
        }
        printf("总贴吧数：%3d        过滤后：%3d\n", $cnt, $users[$i]->alltb->valid);
        echo "-----------------\n";
        for($j=0;$j<$cnt;$j++)
        {
            $tb = $users[$i]->alltb->tbn[$j]->tb;
            $fid = $users[$i]->alltb->tbn[$j]->fid;
            if (!$users[$i]->alltb->tbn[$j]->force) {
                $level = $users[$i]->alltb->tbn[$j]->level;
                $exp = $users[$i]->alltb->tbn[$j]->exp;
                $ForceStop = false;
            }
            $this_tb_sign_cnt = 0;
            do {
                if(!$users[$i]->alltb->tbn[$j]->force && !UserFilter($tb,$level,$exp,$filter))
                {
                    echo "    跳过  $tb\n";
                    continue;
                }
                printf("%-30s","    签到  $tb ");
                if ($users[$i]->alltb->tbn[$j]->force) echo "(Senorsen) ";
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
                $this_tb_sign_cnt++;
                while ($wait_flag) {
                    echo '*';
                    $ret = sign($cookies,$tbs,$fid,$tb);
                    if ($ret->no == 0 && ((time()+3600*24)%(3600*24)>120&& (time()+3600*24)%(3600*24)<3600*23)) $wait_flag = 0;
                }
//                if ($this_tb_sign_cnt > 10) break;
//                sleep(rand(5, 8));
            }while($ret->no==2);
        }
        echo "\n";
    }
    echo date("H:i:s")."    全部签到完成，用时 ".date("i:s",time()-$starttime)."\n";
}
function login_validate($cookies) {
    $tbs_obj = json_decode(curlFetch("http://tieba.baidu.com/dc/common/tbs","$cookies"));
    $tbs = $tbs_obj->tbs;
    $is_login = $tbs_obj->is_login;
    return $is_login?$tbs:0;
}
function get_tbhome($cookies) {
    $ret = curlFetch('http://tieba.baidu.com/', $cookies);
    return $ret;
}
function get_username($tb_home_str) {
    $regex = '/var PageData = ({[\s\S]+?});/';
    preg_match($regex, $tb_home_str, $matches);
    if (!isset($matches[1])) return FALSE;
    preg_match('/\'user\' : ({[\s\S]+?})};/', $matches[0], $matches2);
    $json_str = $matches2[1]; 
    $obj = json_decode($json_str);
    return $obj->user_name;
}
function sign($cookies,$tbs,$fid,$tb)
{
    $tbsurl='http://c.tieba.baidu.com/c/c/forum/sign';
    $myheader=array("$cookies","Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3 TiebaClient/1.2.1.17");
    $cookies_spl=$cookies.';';
    preg_match('/BDUSS=(.+?);/',$cookies_spl,$match_ck);
    $bduss=$match_ck[1];
    $imei_hash = strtolower(md5($bduss));
    //echo $cookies;
    $postdata=array(
                "BDUSS"=>$bduss,
                "_client_id"=>"wappc_1378485686660_60",
                "_client_type"=>"2",
                "_client_version"=>"4.2.2",
                "_phone_imei"=>$imei_hash,
                //"_phone_imei"=>"540b43b59d21b7a48aaaaad31b08e9a5",
                "fid"=>$fid,
                "kw"=>$tb,
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
        return (object)array('no'=>0,'str'=>'■增加'.$obj["user_info"]["sign_bonus_point"].'经验值,第'.$obj["user_info"]["user_sign_rank"].'个签到');
    }
    else if(strpos($obj["error_msg"], '已经签过了') != FALSE)
    {
        return (object)array('no'=>-1,'str'=>'■'.$obj["error_msg"],'code'=>$obj["error_code"]);
    }
    else if($obj['error_code']==160004)
    {
        return (object)array('no'=>1,'str'=>'■本吧无签到');
    }
    else
    {
        return (object)array('no'=>2,'str'=>'Unknown '.$obj["error_msg"],'code'=>$obj["error_code"]);
    }
}
function gettb($tb_home_str,$cookies,$filter)
{
    //返回tbs及tbn
    $tbn = array();
    $str = $tb_home_str;
    preg_match('/forums["][:](.+?[\]])/',$str,$matches);
    if(!isset($matches[1])) return NULL;
    $str = $matches[1];
    $tbn_obj = json_decode($str);
    if(is_null($tbn_obj)) return (object)array('tbs'=>'','tbn'=>array());
    $valid = 0;
    for($i=0;$i<count($tbn_obj);$i++)
    {
        $this_tb = (object)array('force'=>0,'fid'=>$tbn_obj[$i]->forum_id,'tb'=>$tbn_obj[$i]->forum_name,'level'=>isset($tbn_obj[$i]->level_id)?$tbn_obj[$i]->level_id:0,'exp'=>isset($tbn_obj[$i]->cur_score)?$tbn_obj[$i]->cur_score:0);
        array_push($tbn, $this_tb);
        if(UserFilter($this_tb->tb,$this_tb->level,$this_tb->exp,$filter)) $valid++;
    }
    if(count($tbn)>0)expsort(0,count($tbn)-1,$tbn);  //按经验值排序
    return (object)array('tbn'=>$tbn, 'valid'=>$valid, 'is_login' => 1);
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
function getfid($tbname) {
    $flag = 1;
    while ($flag) {
        $url = "http://wapp.baidu.com/f?kw=".urlencode($tbname);
        $retstr = curlFetch($url);
        preg_match('/ name="fid" value="(\d+)"\/>/', $retstr, $matches);
        if (!isset($matches[1])) continue;
        $fid = $matches[1];
        $flag = 0;
    }
    return $fid;
}
function curlFetch($url, $cookie = "", $data = null, $ua = "")
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回字符串，而非直接输出
	curl_setopt($ch, CURLOPT_HEADER, false);   // 不返回header部分
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);   // 设置socket连接超时时间
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$str = curl_exec($ch);
	curl_close($ch);
	return $str;
}
