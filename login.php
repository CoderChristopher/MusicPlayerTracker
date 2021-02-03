<?php
session_start();
if(!isset($_SESSION['authorized']))
	$_SESSION['authorized']=false;
?>
<style>
p{
	text-align: center;
}
div{
	text-align: center;
}
</style>
<?php
if($_SESSION['authorized']===true)
{
?> <script>
location.href="console.php";
</script>
</br>
<a href='logout.php'>Logout</a>
<?php
}else if($_SESSION['authorized']===false){
?>
<p>Please enter the correct password:</br></p>
<div>
<input type=password id=pass >
<input type=submit onclick='submit()' value='login'>
</div>
<div id='info' style='width:25%; text-align:center;margin:auto;color:red;'>
</div>
<?php
}
?>
<script>
window.addEventListener("keydown",event=>{
	if(event.keyCode== 13){
		submit();
	}
});
function submit(){
	var request = new XMLHttpRequest();
	request.onreadystatechange=function(){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				if(request.response==="success")
					location.reload();
				else
					document.getElementById("info").innerHTML="The password you entered was incorrect, please try again!</br></br>If issue persists please contact copelandwebdesign@gmail.com to reset the password.";				
			}
		}	
	}
	request.open('POST','authenticate.php?p='+document.getElementById('pass').value);
	request.send();
}
</script>
