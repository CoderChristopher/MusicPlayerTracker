<?php
//Check origin for trusted site
if(isset($_SERVER["HTTP_ORIGIN"])===true){
	$origin=$_SERVER["HTTP_ORIGIN"];
	if(strcmp($origin,"https://www.copelandwebdesign.com")!==0)
		exit;
}
if(isset($_REQUEST['password'])){
	$hash=password_hash($_REQUEST['password'],PASSWORD_DEFAULT);
	$file=fopen("password.txt","w");
	if($file){
		fputs($file,$hash);
		fclose($file);
	}
	echo "<span style='color: green;'>Password Updated Successfully</span></br></br>";
}else{
	echo "There was an issue processing you request.</br>Please try again later. If the issue persists please contact copelandwebdesign@gmail.com";
}
?>
