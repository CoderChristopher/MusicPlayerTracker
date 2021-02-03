<?php
	session_start();
	if(!isset($_SESSION['authorized'])||$_SESSION['authorized']===false){
		$_SESSION['authorized']=false;
?>
<head>
	<meta http-equiv=refresh content='0; login.php'>
</head>
<?php
}else{
?>
<style>
body{
	background-color: dimgrey;
}
td{
	border: 1px solid black;
	cursor: pointer;
	padding-left: 5px;
	padding-right: 5px;
}
td.results{
	min-width: 12em;
	cursor: text;
}
table.menu{
	border-collapse: separate;
	border-spacing: 15px 0px;
}
tr:nth-child(odd){
	background-color: lightsteelblue;
}
tr:nth-child(even){
	background-color: mintcream;
}
a{
	cursor: pointer;
	color: darkslategrey;
	text-decoration: none;
}
</style>
<html>
<table class='menu'>
<td class='menu'>
<a href='javascript: DisplayStats();' style='color: black;'>Stats</a>
<td class='menu'>
<a href='javascript: DisplayUploads();' style='color: black;'>Upload</a>
<td class='menu'>
<a href='javascript: DisplayManage();' style='color: black;'>Manage</a>
<td class='menu'>
<a href='logout.php' style='color: black;'>Logout</a>
</table>
<div style='width:95%; height:75%;' id='container'>
</div>
</html>
<script>
var searched=false;
var searchSum=0;
var container=document.getElementById("container");
var songnames=new Array();
var songLengths =new Array();
var trackIds =new Array();
var playtime =new Array();
var avgpercent=new Array();
var avgplaytime=new Array();
var playcount=new Array();
var playcountzero=new Array();
var currentOpen=0;
var selected=0;
var listing;
var page=1;
var totalPages=1;
var buttons=false;
var songinfo=false;
function BuildSongs(){
	SongQuery();
	searched=false;
	searchSum=0;
	container=document.getElementById("container");
	songnames=new Array();
	songLengths =new Array();
	trackIds =new Array();
	playtime =new Array();
	avgpercent=new Array();
	avgplaytime=new Array();
	playcount=new Array();
	playcountzero=new Array();
	selected=0;
	totalPages=1;
	buttons=false;
	for(var i=0;i<songinfo.length-1;i+=5){
		songnames.push(songinfo[i]);
		songLengths.push(parseInt(songinfo[i+1]));
		trackIds.push(parseInt(songinfo[i+4]));
	}
}
function LoadTable(table){
	if(table!=currentOpen){
		page=1;
		var atag=document.createElement("a");
		atag.href="javascript:LoadTable("+currentOpen+")";
		atag.text="--Load Table--";
		document.getElementById("song-container-"+currentOpen).appendChild(atag);
		currentOpen=table;
		document.getElementsByTagName("table")[1].remove();
		document.getElementById('table-navigation').remove();
		selected=document.getElementById("song-container-"+currentOpen);
		document.getElementById("song-container-"+currentOpen).getElementsByTagName("a")[0].remove();
		RunQuery();
	}	
}
function SongQuery(){
	var request = new XMLHttpRequest();
	request.onreadystatechange=function(){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				songinfo=false;
				songinfo=request.response.split(";");
			}
		}
	}
	request.open('POST','trackinfo.php',false);
	request.send();
}
function GetPlaytime(destination){
	var request= new XMLHttpRequest();
	request.onreadystatechange=function(){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				playtime[destination]=parseInt(request.response);
				document.getElementById('playtime-container-'+destination).innerHTML=ConvertToStamp(playtime[destination]);
			}
		}
	}
	request.open('POST','playtime.php?s='+trackIds[destination]);
	request.send();
}
function GetAveragePlaytime(destination){
	var request= new XMLHttpRequest();
	request.onreadystatechange=function(){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				avgplaytime[destination]=Math.round(parseInt(request.response),0);
				document.getElementById('avg-playtime-container-'+destination).innerHTML=ConvertToStamp(avgplaytime[destination]);
			}
		}
	}
	request.open('POST','avgplaytime.php?s='+trackIds[destination],false);
	request.send();
}
function GetAveragePercentage(destination){
	if(isNaN(avgplaytime[destination]))
		GetAveragePlaytime(destination);
	avgpercent[destination]=Math.round(avgplaytime[destination]/songLengths[destination]*100,0);
	if(!isNaN(avgpercent[destination]))
		document.getElementById('percentage-container-'+destination).innerHTML=avgpercent[destination];
	else
		document.getElementById('percentage-container-'+destination).innerHTML="--";
}
function GetPlayCount(destination,greaterThanZero=false){
	var request= new XMLHttpRequest();
	request.onreadystatechange = function (){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				playcount[destination]=parseInt(request.response);
				document.getElementById('play-count-container-'+destination).innerHTML=playcount[destination];
			}
		}
	}	
	request.open('POST', 'countplaytime.php?s='+trackIds[destination],false);
	request.send();
}
function GetPlayCountWithTime(destination){
	var request= new XMLHttpRequest();
	request.onreadystatechange = function (){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				playcountzero[destination]=parseInt(request.response);
				document.getElementById('play-count-withtime-container-'+destination).innerHTML=playcountzero[destination];
			}
		}
	}	
	request.open('POST', 'countplaytime.php?s='+trackIds[destination]+'&g=1',false);
	request.send();
}
function RunQuery(){
	var request = new XMLHttpRequest();
	request.onreadystatechange=function(){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				ParseQuery(request.response);
			}
		}
	}
	request.open('POST','query.php?q='+trackIds[currentOpen]);
	request.send();
}
function QueryManage(){
	var request = new XMLHttpRequest();
	request.onreadystatechange=function(){
		if(request.readyState===XMLHttpRequest.DONE){
			if(request.status===200){
				listing=query.split(":");
			}
		}
	}
	request.open('POST','query.php?q='+trackIds[currentOpen]);
	request.send();
}
function UploadFiles(e){
	e.preventDefault();	
	if(document.getElementById("trackName").value.indexOf("\"",0)===-1&&document.getElementById("trackName").value.indexOf("*",0)===-1&&document.getElementById("trackName").value.indexOf("`",0)===-1&&document.getElementById("trackName").value.indexOf("'",0)===-1&&document.getElementById("trackName").value.indexOf("\\",0)===-1&&document.getElementById("trackName").value.indexOf("~",0)===-1&&document.getElementById("trackName").value.indexOf("/",0)===-1&&document.getElementById("trackName").value.indexOf("|",0)===-1&&document.getElementById("trackName").value.indexOf("<",0)===-1&&document.getElementById("trackName").value.indexOf(">",0)===-1&&document.getElementById("trackName").value.indexOf("&",0)===-1&&document.getElementById("trackName").value.indexOf(";",0)===-1&&document.getElementById("trackName").value.indexOf("$(",0)===-1){
		var audioFile = document.getElementById('fileToUpload').files[0];
		var formData = new FormData();
		formData.append("trackName",document.getElementById("trackName").value);

		if(audioFile)
			formData.append("fileToUpload",audioFile);
		var request = new XMLHttpRequest();
		request.onreadystatechange= function(){
			if(request.readyState===XMLHttpRequest.DONE){
				if(request.status===200){
					document.getElementById("upload-zone").innerHTML='</br>File Upload complete with status:</br></br>'+request.response+'</br></br><b>You may now upload additional files.</b>';
					BuildSongs();
				}
			}
		}
		request.open('POST','upload.php',true);
		request.send(formData);
		document.getElementById('trackName').value="";
		document.getElementById('upload-zone').innerHTML="</br>File is uploading...</br>Please do not close out of this page till complete...</br>Please wait...";
	}else{
		alert("File name cannot include the illegal characters:\n\n \" * ` ' \\ / ~ | ; & < > \n\nPlease remove them.");
	}
}
function BuildTable(){
	var table=document.createElement("table");
	var forwardButton=false;
	var backwardButton=false;
	var fastForwardButton=false;
	var fastBackwardButton=false;
	var numberRows=0;
	var row=document.createElement("tr");
	var col=document.createElement("td");
	col=document.createElement("td");
	col.innerHTML="Song Title";
	col.className="result";
	row.appendChild(col);
	col=document.createElement("td");
	col.innerHTML="UTM";
	col.className="results";
	row.appendChild(col);
	col=document.createElement("td");
	col.innerHTML="Play Time";
	col.className="results";
	row.appendChild(col);
	col=document.createElement("td");
	col.innerHTML="Listen Date";
	col.className="results";
	row.appendChild(col);
	table.appendChild(row);
	if(document.getElementById("search").value==-1){
		var numberCounter=(page-1)*10;
		for(var i=listing.length-5;i>=0;i-=4){
			if(numberCounter==0){
				row=document.createElement("tr");
				col=document.createElement("td");
				col.className="results";
				col.innerHTML+=GetTitleFromTrackID(listing[i]);
				row.appendChild(col);
				col=document.createElement("td");
				col.className="results";
				col.innerHTML+=listing[i+1];
				row.appendChild(col);
				col=document.createElement("td");
				col.className="results";
				col.innerHTML+=ConvertToStamp(listing[i+2]);
				row.appendChild(col);
				col=document.createElement("td");
				col.className="results";
				var utcseconds=parseInt(listing[i+3]);
				var d = new Date(0);
				d.setUTCSeconds(utcseconds);
				col.innerHTML=d.toString();
				row.appendChild(col);
				table.appendChild(row);

				numberRows++;
			}else{
				numberCounter--;
			}
			if(numberRows>=10){
				break;
			}
		}
		if(numberRows<10)
		{
			for(var i=0;i<10-numberRows;i++){
				row=document.createElement("tr");
				col=document.createElement("td");
				col.innerHTML="no data";
				row.appendChild(col);
				col=document.createElement("td");
				col.innerHTML="---";
				row.appendChild(col);
				col=document.createElement("td");
				col.innerHTML="---";
				row.appendChild(col);
				col=document.createElement("td");
				col.innerHTML="---";
				row.appendChild(col);
				table.appendChild(row);
			}	
			
		}
	}else{
		var numberCounter=(page-1)*10;
		for(var i=listing.length-5;i>=0;i-=4){
			if(document.getElementById("search").value==listing[i]){
				if(numberCounter==0){
					if(document.getElementById("search").value==listing[i]){
						row=document.createElement("tr");
						col=document.createElement("td");
						col.className="results";
						col.innerHTML+=GetTitleFromTrackID(listing[i]);
						row.appendChild(col);
						col=document.createElement("td");
						col.className="results";
						col.innerHTML+=listing[i+1];
						row.appendChild(col);
						col=document.createElement("td");
						col.className="results";
						col.innerHTML+=ConvertToStamp(listing[i+2]);
						row.appendChild(col);
						col=document.createElement("td");
						col.className="results";
						var utcseconds=parseInt(listing[i+3]);
						var d = new Date(0);
						d.setUTCSeconds(utcseconds);
						col.innerHTML=d.toString();
						row.appendChild(col);
						table.appendChild(row);
						numberRows++;
					}
				}else{
					numberCounter--;
				}
			}
			if(numberRows>=10){
				break;
			}
		}
		if(numberRows<10)
		{
			for(var i=0;i<10-numberRows;i++){
				row=document.createElement("tr");
				col=document.createElement("td");
				col.innerHTML="no data";
				row.appendChild(col);
				col=document.createElement("td");
				col.innerHTML="---";
				row.appendChild(col);
				col=document.createElement("td");
				col.innerHTML="---";
				row.appendChild(col);
				col=document.createElement("td");
				col.innerHTML="---";
				row.appendChild(col);
				table.appendChild(row);
			}	
			
		}
		
	}
	if(searched){
		searchSum=Math.floor(searchSum/10);
		searched=false;
	}
	if(totalPages-searchSum-page>=1)
		forwardButton=true;
	if(totalPages-searchSum-page>=5)
		fastForwardButton=true;
	if(page!=1)
		backwardButton=true;
	if(page>5)
		fastBackwardButton=true;

	var div=document.createElement("div");
	div.id='table-navigation';
	if(fastBackwardButton){
		var back=document.createElement("a");
		back.text="<<<  ";
		back.href="javascript:PrevPage5();";
		div.appendChild(back);
	}
	if(backwardButton){
		var back=document.createElement("a");
		back.text="Back";
		back.href="javascript:PrevPage();";
		div.appendChild(back);	
	}
	if((totalPages)>1){
		var text=document.createElement("div");
		text.id='page-count';
		text.style.display="inline";
		text.innerHTML="         Page "+page+" of "+(totalPages-searchSum)+"        ";
		div.appendChild(text);
	}
	if(forwardButton){
		var forward=document.createElement("a");
		forward.text="Forward";
		forward.href="javascript:NextPage();";
		div.appendChild(forward);	
	}
	if(fastForwardButton){
		var forward=document.createElement("a");
		forward.text="  >>>";
		forward.href="javascript:NextPage5();";
		div.appendChild(forward);
	}
	selected.appendChild(table);
	table.after(div);
}
function TwoDigit(number){
	return ("0"+number).slice(-2);
}
function ConvertToStamp(seconds){
	if(isNaN(seconds))
		return "--:--";
	if(seconds<3600)
		return TwoDigit((Math.floor(seconds/60)))+":"+TwoDigit((Math.round(seconds%60,0)));
	else
		return Math.floor(seconds/3600)+":"+TwoDigit((Math.floor(seconds/60)%60))+":"+TwoDigit((Math.round(seconds%60,0)));
		
}
function Search(){
	searchSum=0;
	page=1;
	searched=false;
	if(document.getElementById("search").value!=-1){
		searched=true;
		for(var i=0;i<listing.length;i+=4)
			if(listing[i]!=document.getElementById("search").value)
				searchSum++;		
			
	}
	document.getElementsByTagName("table")[1].remove();
	document.getElementById('table-navigation').remove();
	BuildTable();
}
function NextPage5(){
	if(page+5<=totalPages)
		page+=5;
	document.getElementsByTagName("table")[1].remove();
	document.getElementById('table-navigation').remove();
	BuildTable();
}
function NextPage(){
	if(page+1<=totalPages)
		page++;
	document.getElementsByTagName("table")[1].remove();
	document.getElementById('table-navigation').remove();
	BuildTable();
}
function PrevPage5(){
	if(page-5>=1)
		page-=5;
	document.getElementsByTagName("table")[1].remove();
	document.getElementById('table-navigation').remove();
	BuildTable();
}
function PrevPage(){
	if(page-1>=1)
		page--;
	document.getElementsByTagName("table")[1].remove();
	document.getElementById('table-navigation').remove();
	BuildTable();
} 
function ParseQuery(query){
	listing=query.split(":");
	totalPages=Math.ceil((listing.length-1)/4/10);
	BuildTable();
}
function DisplayStats(){
	document.getElementById('container').innerHTML="";
	
	currentOpen=0;
	container.innerHTML+="<div id='song-container-0' style='border: 1px solid black; margin:1%;padding:1%;background-color: whitesmoke;'>";	
	document.getElementById("song-container-0").innerHTML+="<select onclick='Search()' id='search'></select>";	
	document.getElementById("search").innerHTML+="<option value='-1'></option>";
	for(var i=0;i<songnames.length;i++)
		document.getElementById("search").innerHTML+="<option value='"+trackIds[i]+"'>"+songnames[i]+"</option>";
	
	for(var i=0;i<songnames.length;i++){
		container.innerHTML+="<div id='song-container-"+i+1+"' style='border: 1px solid black; margin:1%;padding:1%;background-color: whitesmoke;'>";	
		document.getElementById("song-container-"+i+1).innerHTML="Track Name: "+songnames[i]+"</br>Page Hits: <div style='display:inline;' id='play-count-container-"+i+"'></div> (Includes page loads with no play time)</br>Total Listens:<div style='display:inline;' id='play-count-withtime-container-"+i+"'></div></br> Total Play Time: <div style='display: inline;' id='playtime-container-"+i+"'>"+ConvertToStamp(playtime[i])+"</div> </br>Average Play Time: <div style='display:inline;' id='avg-playtime-container-"+i+"'>"+ConvertToStamp(avgplaytime[i])+"</div> </br>Average Percentage: <div style='display:inline;' id='percentage-container-"+i+"'>"+avgpercent[i]+"</div>%</br>";
		GetPlayCount(i);
		GetPlayCountWithTime(i);
		GetAveragePlaytime(i);
		GetPlaytime(i);
		GetAveragePercentage(i);
	}	
	selected=document.getElementById("song-container-0");
	RunQuery();
}
function DisplayUploads(){
	document.getElementById('container').innerHTML="";
	var container=document.getElementById('container');
	container.innerHTML='<div id="upload-container" style="border: 1px solid black; margin: 1%;padding:1%;background-color: whitesmoke;">';	
	container=document.getElementById('upload-container');
	container.innerHTML='Name as it Will Appear:</br></br><form onsubmit="UploadFiles(event);" enctype="multipart/form-data"><input type=text name="trackName" id="trackName" ></br></br><h3>Audio File:</h3>Upload: <input type=file accept="audio/*" multiple=false name="fileToUpload" id="fileToUpload"></br></br></br><input type=submit value="Submit"></form><div id="upload-zone"></div>';
}
function SubmitChange(i){
	for(var j=0;j<songnames.length;j++){
		if(document.getElementById("song-entry-"+i).value==songnames[j]){
			alert("The song name "+document.getElementById("song-entry-"+i).value+" has already be taken by another song. Please select a different name.");
			return;
		}	
	}
	var request = new XMLHttpRequest();
	request.onreadystatechange=function (){
	}	
	request.open('POST','changeName.php?i='+trackIds[i]+'&name='+document.getElementById("song-entry-"+i).value,false);
	request.send();
	SongQuery();
	songnames[i]=songinfo[i*5];
	DisplayManage();
}

function DeleteTrack(i){
	if(confirm("Are you absolutely sure you want to delete the track "+songnames[i]+"? Any song analytics and files relating to the track will completely removed from the system and be unrecoverable.")){
		var request = new XMLHttpRequest();
		request.onreadystatechange=function(){
			if(request.readyState===XMLHttpRequest.DONE){
				if(request.status===200){
					BuildSongs();
				}
			}	
		}
		request.open('post','delete.php?i='+trackIds[i]);
		request.send();
		SongQuery();
		DisplayManage();
	}
}

function GenerateUTM(){
	var utm=document.getElementById("utm reciever");
	if(document.getElementById("utm entry").value.length!==0)
		utm.innerHTML=document.getElementById("url fragment").value+"?utm="+encodeURIComponent(document.getElementById("utm entry").value);
	else
		utm.innerHTML=document.getElementById("url fragment").value;
		
}
function GenerateTag(){
	var tag=document.getElementById("tagtext");
	tag.innerHTML="<audio name=\""+document.getElementById("song").value+"\" preload='auto'></audio>\n<canvas name=\""+document.getElementById("song").value+"\" width='220px' height='32px' style='box-shadow:2px 2px 2px grey;'></canvas>";
		
}
function SubmitPassword(){
	document.getElementById("passwordresponse").innerHTML="";
	if(document.getElementById("newpassword-1").value!==document.getElementById("newpassword-2").value){
		document.getElementById("passwordresponse").innerHTML="<span style='color: red;'>Your two passwords do not match!</span>";
		return;
	}else{
		var request= new XMLHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState===XMLHttpRequest.DONE){
				if(request.status==200){
					document.getElementById("passwordresponse").innerHTML=request.response;	
				}
			}
		}
		var formData = new FormData();
		formData.append("password",document.getElementById("newpassword-1").value);
		request.open('POST',"updatePassword.php");
		request.send(formData);
		document.getElementById("newpassword-1").value="";
		document.getElementById("newpassword-2").value="";
	}
}
function LoadPlayerSource(){
	var raw = new XMLHttpRequest();
	raw.onreadystatechange=function(){
		if(raw.readyState===XMLHttpRequest.DONE){
			if(raw.status===200){
				document.getElementById("source").innerHTML=raw.responseText;
			}
		}	
	}
	raw.open("GET","audio-player.php");
	raw.send();

}
function DisplayManage(){
	document.getElementById('container').innerHTML="";
	var container=document.getElementById('container');
	container.innerHTML='<div id="manage-container" style="border: 1px solid black; margin: 1%;padding:1%;background-color: whitesmoke;">';
	container=document.getElementById('manage-container');
	var tmp=document.createElement("h3");
	tmp.innerHTML="Manage Tracks";
	container.appendChild(tmp);
	var table=document.createElement("table");
	container.appendChild(table);
	var tr=document.createElement("tr");
	tr.innerHTML="<td>Track Name</td><td></td>";
	table.appendChild(tr);

	for(var i=0;i<songnames.length;i++){
		tr=document.createElement("tr");
		tr.innerHTML="<td><input style='width:300px' type=text id='song-entry-"+i+"' value='"+songnames[i]+"'></td><td><input type=button onclick='SubmitChange("+i+")' value='Submit Changes'><input type=button onclick='DeleteTrack("+i+")' value='Delete Track'></td>";
		table.appendChild(tr);
	}	
	container.appendChild(document.createElement("br"));
	container.appendChild(document.createElement("br"));
	tmp=document.createElement("h3");
	tmp.innerHTML="Tag Generator";
	container.appendChild(tmp);
	var input=document.createElement("div");
	input.innerHTML="<select id='song'>";
	container.appendChild(input);
	var select=document.getElementById("song");
	for(var i=0;i<songnames.length;i++)
		select.innerHTML+="<option  value='"+trackIds[i]+"'>"+songnames[i]+"</option>";
	input.innerHTML+="  <button type='button' onclick='GenerateTag()'>Generate</button>";
	input.innerHTML+="</br></br><b>Player Tag</b></br>Copy the code below (cmd/ctrl + a , cmd/ctrl + c)  and paste into your host site where you want the player to be present. The name=\"#\" controls which song is played. The name attribute <u>must</u> be the same for the audio and canvas tag for the player to work correctly.</br><textarea style='width:50%;' rows=5 id='tagtext'>";
	
	container.appendChild(document.createElement("br"));
	container.appendChild(document.createElement("br"));
	tmp=document.createElement("h3");
	tmp.innerHTML="UTM Generator";
	container.appendChild(tmp);
	
	input=document.createElement("div");
	input.innerHTML="<input style='width:300px' type=text value='https://www.jbryanmarcus.com/audio-player' id='url fragment'/>  UTM:<input type=text value='' id='utm entry'/> ";
	container.appendChild(input);
	
	input.innerHTML+="  <button type='button' onclick='GenerateUTM()'>Generate</button>";
	input.innerHTML+="</br></br><div id='utm reciever'></div>";
	tmp=document.createElement("h3");
	tmp.innerHTML='Manage Password';
	container.appendChild(tmp);
	tmp=document.createElement("div");
	tmp.id='passwordresponse';
	container.appendChild(tmp);
	var passwordForm=document.createElement("form");
	container.appendChild(passwordForm);	
	tmp=document.createElement("span");
	tmp.innerHTML="New Password: ";
	passwordForm.appendChild(tmp);
	tmp=document.createElement("input");
	tmp.type="password";
	tmp.id='newpassword-1';
	passwordForm.appendChild(tmp);
	passwordForm.appendChild(document.createElement("br"));
	tmp=document.createElement("span");
	tmp.innerHTML="Re-Enter Password: ";
	passwordForm.appendChild(tmp);
	tmp=document.createElement("input");
	tmp.type='password';
	tmp.id='newpassword-2';
	passwordForm.appendChild(tmp);
	passwordForm.appendChild(document.createElement("br"));
	var submitbtn=document.createElement("input");
	submitbtn.type='button';
	submitbtn.value='Submit';	
	submitbtn.onclick=SubmitPassword;
	passwordForm.appendChild(submitbtn);
	passwordForm.appendChild(document.createElement("br"));
	tmp=document.createElement("h3");
	tmp.innerHTML='Source Code';
	container.appendChild(tmp);
	passwordForm.appendChild(document.createElement("br"));
	tmp=document.createElement("span");
	tmp.innerHTML='You can copy and paste this source code by clicking inside the text box below, pressing CMD + A (CTRL - A on Windows) to select all and then CMD + C (CTRL - C on Windows) to copy it. You may paste this anywhere where javascript injection is allowed and it should work out of the box. I recommend putting in the footer of a web page.';
	container.appendChild(tmp);
	passwordForm.appendChild(document.createElement("br"));
	tmp=document.createElement("textarea");
	tmp.rows="50";
	tmp.style="width:100%;";
	tmp.innerHTML="text goes here...";
	tmp.id="source";
	container.appendChild(tmp);
	LoadPlayerSource();
}
function GetTitleFromTrackID(id){
	for(var i=0;i<trackIds.length;i++){
		if(trackIds[i]==id)
			return songnames[i];
	}
	return null;
}
BuildSongs();
DisplayStats();
</script>

<?php
}
?>
