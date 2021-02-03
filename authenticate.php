<?php
if(!isset($_REQUEST['p'])){
	echo "password not set";
	exit;
}
session_start();
$file=fopen("password.txt","r");
if($file){
	$hash=fgets($file);
	fclose($file);
}else{
	$_SESSION['authorized']=false;
	echo "file not found.";
	exit;
}
if(password_verify($_REQUEST['p'],$hash)){
	$_SESSION['authorized']=true;
	echo "success";
}else{
	$_SESSION['authorized']=false;
	echo "bad";
}
?>
