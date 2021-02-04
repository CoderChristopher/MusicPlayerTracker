<?php
//name variable is for what the track name will be changed to
//i variable is to what track id to search for
//strpos checks are to check against a SQL injection attempt
//though honestly I see now that I forgot to in the past do a check against
//i variable...
if(isset($_REQUEST['name'])&&strpos($_REQUEST['name'],"\"")===false&&strpos($_REQUEST['name'],"'")===false){
	$con = mysqli_connect("mysql.copelandwebdesign.com","--redacted--","---------");
	$res = mysqli_query($con,"update marcmusicplayer.TrackInfo set trackname='".$_REQUEST['name']."' where id=".$_REQUEST['i'].";");	
	mysqli_close($con);
}else
	exit;
?>
