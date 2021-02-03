<?php
/*if(strcmp($_SERVER['HTTP_ORIGIN'],"https://www.copelandwebdesign.com")!==0){
	echo "bad origin:".$_SERVER['HTTP_ORIGIN'];
	exit;
}*/
if(isset($_REQUEST['s'])&&is_numeric($_REQUEST['s'])){
	$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","--------");
	if(!isset($_REQUEST['g']))
		$res = mysqli_query($con,"select count(time) from marcmusicplayer.Music where trackid=\"".$_REQUEST['s']."\";");	
	else	
		$res = mysqli_query($con,"select count(time) from marcmusicplayer.Music where trackid=\"".$_REQUEST['s']."\" and time>0;");	
	echo $res->fetch_array()[0];
	mysqli_close($con);
}else
	exit;
?>
