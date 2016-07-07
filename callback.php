<?php
//print_r($_SERVER);
$uri=$_SERVER["REQUEST_URI"];
//user2345.html
$uid=substr($uri,5,strpos($uri,'.')-5);
//echo $uid;
$mem=new memcache();
$mem->addServer('127.0.0.1',11211);
$mem->addServer('127.0.0.1',11212);
$mem->addServer('127.0.0.1',11213);
$conn=mysql_connect("localhost","root","wan520lw");
$sql="use test";
mysql_query($sql,$conn);
$sql="set names utf8";
mysql_query($sql,$conn);
$sql="select * from user where uid=".$uid;
$rs=mysql_query($sql,$conn);
$user=mysql_fetch_assoc($rs);
if(empty($user)){
echo "no this user";
}else{
print_r($user);
//$mem=new memcache();
//$mem->connect('localhost',11211);
$mem->add($uri,$user['uname'],0,300);
$mem->close();
}
?>


