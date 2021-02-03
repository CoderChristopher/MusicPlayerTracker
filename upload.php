<?php
if(!isset($_REQUEST['trackName'])&&strpos($_REQUEST['trackName'],"'")!==false&&strpos($_REQUEST['trackName'],"\"")&&strpos($_FILES["fileToUpload"]["name"],"'")&&strpos($_FILES["fileToUpload"]["name"],"\""))
	exit;
$track=str_replace(" ", "",$_REQUEST['trackName']);
$targetAudioDir = "Audio/";
$audioExtension = pathinfo($_FILES["fileToUpload"]["name"])['extension'];
$targetAudioFile = $targetAudioDir.$track.".".$audioExtension;
if(file_exists($targetAudioFile)){
	echo "Sorry, file already exists.</br>";
	echo "Please try naming your track something that is already not in use.</br>";
	exit;
}else{
	if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$targetAudioFile)){
		echo "File uploaded Successfully!</br>";
		$con =mysqli_connect('mysql.copelandwebdesign.com','--readacted--','-------');	
		$query = mysqli_query($con,"select * from marcmusicplayer.TrackInfo order by id desc limit 1;");
		$id=$query->fetch_array()['id']+1;
		echo "Registered duration:";
		$duration=system("ffprobe '".$targetAudioFile."' 2>&1|awk '/Duration: [0-9]/{print $2};'");
		$parts=explode(":",$duration);
		$duration=number_format($parts[0])*3600+number_format($parts[1])*60+number_format($parts[2]);
		$query = mysqli_query($con,"insert into marcmusicplayer.TrackInfo (trackname,length,audiourl,id) values ('".$_REQUEST['trackName']."',".$duration.",'https://www.copelandwebdesign.com/Projects/MusicPlayerReciever/".$targetAudioFile."',".$id.");");	
		mysqli_close($con);
	}else{
		echo "Sorry, there was an error uploading your file!</br>";
		echo "Please try again later... If issue persists please contact copelandwebdesign@gmail.com</br>";
		exit;
	}
}
?>
