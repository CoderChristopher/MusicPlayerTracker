<?php
//p is the password to be checked
if(!isset($_REQUEST['p'])){
	echo "password not set";
	exit;
}
session_start();
//Hyper secure way of storing passwords...
//For a single user system I think this method suffices,
//and I make sure to block read access with .htaccess file
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
