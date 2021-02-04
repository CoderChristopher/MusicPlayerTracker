<?php
//is_numeric to guard against SQL Injection, see avgpercentage.php comment for more words on this.
//s variable is trackid to reference
//g variable is a simple flag to indicate whether to include 'plays' with no time or not
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
