<?php
if(isset($_REQUEST['name'])&&strpos($_REQUEST['name'],"\"")===false&&strpos($_REQUEST['name'],"'")===false){
	$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","---------");
	$res = mysqli_query($con,"update marcmusicplayer.TrackInfo set trackname='".$_REQUEST['name']."' where id=".$_REQUEST['i'].";");	
	mysqli_close($con);
}else
	exit;
?>
