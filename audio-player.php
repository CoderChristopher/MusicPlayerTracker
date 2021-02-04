<script>
	var curentPlayer=-1;
	var audios=	document.getElementsByTagName("audio");
	var canvases=	document.getElementsByTagName("canvas");
	var numPlayers=	canvases.length;
	//Arrays are used so that multiple players may be supported on one page
	var plays=new Array(numPlayers);
	var percentages=new Array(numPlayers);
	var audioLengths=new Array(numPlayers);
	var trackids=new Array(numPlayers);
	var uniqueids=new Array(numPlayers);
	var times=new Array(numPlayers);
	var maxReportedTimes=new Array(numPlayers);
	var showTimes=new Array(numPlayers);
	var pressedPlays=new Array(numPlayers);
	var url=window.location.search;
	//curent date
	var d=new Date();

	var utm=url.indexOf("?");
	var mouseX=0;
	var mouseY=0;
	var mouseDown=false;

	//Parse the UTM
	if(utm!=-1){
		utm=url.substr(utm+1);
		utm=decodeURIComponent(utm);
		if(utm.indexOf("&")!==-1){
			utm=utm.split('&');
		}
	}
	//Run through players an initalize them
	for(var i=0;i<numPlayers;i++){
		plays[i]=true;
		percentages[i]=0;
		audioLengths[i]=1;
		trackids[i]=1;
		//Generate the unique player id by using the current time plus a random number
		uniqueids[i]=d.getTime().toString()+Math.floor(Math.random()*10000000).toString();
		times[i]=0;
		maxReportedTimes[i]=-1;
		pressedPlays[i]=false;
	}

	//setup all the functions for listening for actions on each player
	for(var i=0;i<numPlayers;i++){
		canvases[i].onmousedown=function(event){
			var i=-1;
			//This is used to figure out the index of the current player(i)
			for(var j=0;j<numPlayers;j++)
				if(this.attributes["name"].value===audios[j].attributes["name"].value)
					i=j
			mouseDown=true;
			mouseX=event.offsetX;
			mouseY=event.offsetY;
			//Calculate the distance from the play button on a click, if it is within 14px then toggle play/stop
			if(Math.sqrt(Math.pow(Math.abs(20-mouseX),2)+Math.pow(Math.abs(18-mouseY),2))<14){
				toggle(i);
				//Otherwise see if the user is clicking on the scrubbing area and if so set the audio player
				//to the appropriate time stamp
			}else if(mouseX>=40&&mouseX<=160&&mouseY>=8&&mouseY<=28){
				audios[i].currentTime=Math.round(((mouseX-39)/120)*audios[i].duration);	
			}
			
		};
		canvases[i].onmouseup=function(event){
			mouseDown=false;
		};
		canvases[i].onmousemove=function(event){
			var i=-1;
			//Figure out index for currently active 'this' player
			for(var j=0;j<numPlayers;j++)
				if(this.attributes["name"].value===audios[j].attributes["name"].value)
					i=j//In hindsight I could do this with one variable
			mouseX=event.offsetX;
			mouseY=event.offsetY;
			showTimes[i]=false;
			currentPlayer=i;	
			
			//If over the scrubbing bar show a time stamp
			if(mouseX>=40&&mouseX<=160&&mouseY>=8&&mouseY<=28){
				showTimes[i]=true;
				//And if the mouse is clicked then update the audio player position accordingly
				if(mouseDown)
					audios[i].currentTime=Math.round(((mouseX-39)/120)*audios[i].duration);	
			}
		};
		//Do much of the same as above, but for mobile touch screen devices
		canvases[i].ontouchstart=function(event){
			var i=-1;
			for(var j=0;j<numPlayers;j++)
				if(this.attributes["name"].value===audios[j].attributes["name"].value)
					i=j
			var t=event.touches[0];
			var rect=canvases[i].getBoundingClientRect();
			mouseX=t.clientX-rect.left;
			mouseY=t.clientY-rect.top;
			showTimes[i]=false;
			mouseDown=true;
			currentPlayer=i;	
			
			if(mouseX>=40&&mouseX<=160&&mouseY>=8&&mouseY<=28){
				audios[i].currentTime=Math.round(((mouseX-39)/120)*audios[i].duration);	
			}
		};
		canvases[i].ontouchend=function(event){
			mouseDown=false;
		};
		canvases[i].ontouchmove=function(event){
			event.preventDefault();
			var i=-1;
			for(var j=0;j<numPlayers;j++)
				if(this.attributes["name"].value===audios[j].attributes["name"].value)
					i=j
			var t=event.touches[0];
			var rect=canvases[i].getBoundingClientRect();
			mouseX=t.clientX-rect.left;
			mouseY=t.clientY-rect.top;
			showTimes[i]=false;
			if(mouseX>=30&&mouseX<=200&&mouseY>=1&&mouseY<=30){
				if(mouseX<=40&&mouseX>=30)
					mouseX=39;
				if(mouseX>=158)
					mouseX=158;
				showTimes[i]=true;
				audios[i].currentTime=Math.round(((mouseX-39)/120)*audios[i].duration);	
			}
		};
	}

	//setup functions for when the play time of the track updates
	for(var i=0;i<numPlayers;i++)
		audios[i].ontimeupdate=function(){
			var i=-1;
			for(var j=0;j<numPlayers;j++)
				if(this.attributes["name"].value===audios[j].attributes["name"].value)
					i=j
			percentages[i]=audios[i].currentTime/audioLengths[i];
			times[i]=audios[i].currentTime;	
		}
	//Just a simple utility function for taking a single digit string and padding with a zero to two digits
	function TwoDigit(number){
		return ("0"+number).slice(-2);
	}
	//Make a minute:second time stamp utility function
	function ConvertToStamp(seconds){
		 if(isNaN(seconds))
			 return "--:--";
		 if(seconds<3600)
			return TwoDigit((Math.floor(seconds/60)))+":"+TwoDigit((Math.round(seconds%60,0)));
		 else
			return Math.floor(seconds/3600)+":"+TwoDigit((Math.floor(seconds/60)%60))+":"+TwoDigit((Math.round(seconds%60,0)));

	}
	//toggles playing and paused state along with all associated state variables
	function toggle(i) {
		if(audios[i].paused){
			audios[i].play();
			if(!pressedPlays[i])
				pressedPlays[i]=true;
		}else{
			SendTime();
			audios[i].pause();
		}
		plays[i]=!plays[i];
		for(var j=0;j<numPlayers;j++)
			if(j!=i)
			{
				if(!audios[j].paused){
					audios[j].pause();
					SendTime();
					plays[j]=!plays[j];
				}
			}
	}
	//Utility function for drawing rounded rectangles
	function DrawRoundedRect(ctx,x,y,w,h,r){
		ctx.beginPath();
		ctx.moveTo(x,y+r);
		ctx.quadraticCurveTo(x,y,x+r,y);
		ctx.lineTo(x+w-r,y);
		ctx.quadraticCurveTo(x+w,y,x+w,y+r);
		ctx.lineTo(x+w,y+h-r);
		ctx.quadraticCurveTo(x+w,y+h,x+w-r,y+h);
		ctx.lineTo(x+r,y+h);
		ctx.quadraticCurveTo(x,y+h,x,y+h-r);
		ctx.closePath();
		ctx.fill();
	}
	//Utility function for drawing rounded triangles
	function DrawRoundedTriangle(ctx,x1,y1,x2,y2,x3,y3,r){
		ctx.beginPath();
		ctx.moveTo(x1,y1);
		ctx.lineTo(x2,y2);
		ctx.lineTo(x3,y3);
		ctx.closePath();
		ctx.fill();
		ctx.lineWidth=r;
		ctx.lineJoin="round";
		var oldStroke=ctx.strokeStyle;
		ctx.strokeStyle=ctx.fillStyle;
		ctx.stroke();
		ctx.strokeStyle=oldStroke;
	}
	//Main loop of the program
	function Loop(){
		//Check all players to see if the track is complete, if so
		//pause the track, and put the position back to the start
		//(if you do not do this then the track will just repeat,
		//which is not typical expected behavior)
		for(var i=0;i<numPlayers;i++)
			if(audios[i].currentTime==audios[i].duration){
				audios[i].pause();
				plays[i]=true;
				audios[i].currentTime=0;
			}
		Render();
	}
	function Render(){
		for(var i=0;i<numPlayers;i++){
			//get the context
			var ctx=canvases[i].getContext("2d");
			//Clear it
			ctx.clearRect(0,0,canvases[i].width,canvases[i].height);
			ctx.lineJoin="miter";
			ctx.lineWidth=1;
		
			ctx.fillStyle="white";
			
			//Draw background
			DrawRoundedRect(ctx,0,0,220,32,2);
			//Draw scrub bar
			ctx.fillStyle="#dddddd";
			DrawRoundedRect(ctx,44,16,120,4,1);

			//See if over the current position indicator, change its color accordingly
			if(Math.sqrt(Math.pow(Math.abs(44+112*percentages[i]-mouseX),2)+Math.pow(Math.abs(18-mouseY),2))<8&& currentPlayer==i)
				ctx.fillStyle="#555555";
			else
				ctx.fillStyle="#000000";
			ctx.beginPath();
			ctx.arc(44+112*percentages[i],18,8,0,2*Math.PI);
			ctx.fill();

			//Draw some text of the current time stamp
			ctx.fillStyle="black";
			ctx.textAlign="left";
			ctx.font="20px Time New Roman";
			ctx.fillText(Math.floor(Math.ceil(audios[i].currentTime)/60)+":"+String("0"+Math.round(Math.ceil(audios[i].currentTime))%60).slice(-2),172,24);
			
			//If mouse is over the scrub bar show the associated time stamp for the position
			if(showTimes[i]){
				ctx.font="16px Time New Roman";
				ctx.fillStyle="black";
				ctx.lineWidth=.6;
				ctx.strokeStyle="black";
				var scrubTime=Math.round(((mouseX-39)/120)*audios[i].duration);
				scrubTime=ConvertToStamp(scrubTime);
				ctx.fillText(scrubTime,mouseX-ctx.measureText(scrubTime).width/2,12);
			}

			if(plays[i]){
				//If paused then draw a play triangle, with a color that is lighter or darker depending on if it is moused over
				if(Math.sqrt(Math.pow(Math.abs(20-mouseX),2)+Math.pow(Math.abs(18-mouseY),2))<14&& currentPlayer==i)

					ctx.fillStyle="#555555";
				else
					ctx.fillStyle="#000000";
				DrawRoundedTriangle(ctx,14,10,26,17,14,24,4);
			}else{
				//If playing then draw a double bar pause button, with a color that is lighter or darker depending on if it is moused over
				if(Math.sqrt(Math.pow(Math.abs(20-mouseX),2)+Math.pow(Math.abs(18-mouseY),2))<14&& currentPlayer==i)
					ctx.fillStyle="#555555";
				else
					ctx.fillStyle="#000000";
				DrawRoundedRect(ctx,14,9,4,16,2);
				DrawRoundedRect(ctx,20,9,4,16,2);
			}
			ctx.font="26px Time New Roman";
			ctx.fillStyle="white";
			ctx.strokeStyle="black";
			ctx.lineWidth=.6;
			ctx.textAlign="center";
		}
		//Setup next frame
		window.requestAnimationFrame(Loop);
	}
	window.requestAnimationFrame(Loop);

	//Perform a ajax request to get the song info from the main server
	function RequestSongInfo(i){
		var request = new XMLHttpRequest();
		request.onreadystatechange=function(){
			if(request.readyState==XMLHttpRequest.DONE){
				if(request.status==200){
					var details=request.response.split(";");
					audioLengths[i]=parseInt(details[1]);
					audios[i].src=details[2];
					audios[i].load();
					trackids[i]=details[4];
				}
			}
		}
		trackids[i]=parseInt(audios[i].attributes["name"].value);
		request.open("POST","https://www.copelandwebdesign.com/Projects/MusicPlayerReciever/trackinfo.php?i="+trackids[i]);
		request.send();
	}

	//Get each player's associated track info
	for(var i=0;i<numPlayers;i++)
		RequestSongInfo(i);
	
	//This is the function that ships the analytics off to the server
	function SendTime(){
		var request = new Array(numPlayers);
		var thisMaybe=new Array(numPlayers);
		//cycle through all players
		for(var i=0;i<numPlayers;i++)
			//So see if the current play time is larger than the max previous reported time and the
			//user has at least once pressed play
			if(times[i]>maxReportedTimes[i]&&pressedPlays[i]){
				request[i]=new XMLHttpRequest();
				request[i].i=i;
				thisMaybe[i]=times[i];//Track a potential change to max time, but will only update upon
				//a successful transaction
				request[i].onreadystatechange=function(){
					if(request[this.i].readyState==XMLHttpRequest.DONE){
						if(request[this.i].status==200){

							maxReportedTimes[this.i]=thisMaybe[this.i];
						}
					}
				}
				if(utm!==-1){
					//Do a check because a string and array can look similar when using [] or length variables
					if(!Array.isArray(utm)){
						if(utm.indexOf("=")!==-1&&utm.indexOf("utm")!==-1)
							utm=utm.split("=");
						request[i].open("POST","https://www.copelandwebdesign.com/Projects/MusicPlayerReciever/input.php?id="+uniqueids[i]+"&time="+times[i]+"&utm="+utm[1]+"&trackid="+trackids[i],true);
					}else{
						var datatosend="";
						for(var j=0;j<utm.length;j++){
							if(utm[j].indexOf("=")!==-1&&utm[j].indexOf("utm")!==-1){
								var tmp=utm[j].split("=");
								datatosend+=tmp[1]+" ; ";
							}
						}
						request[i].open("POST","https://www.copelandwebdesign.com/Projects/MusicPlayerReciever/input.php?id="+uniqueids[i]+"&time="+times[i]+"&utm="+datatosend+"&trackid="+trackids[i],true);
					}
				}else{
					request[i].open("POST","https://www.copelandwebdesign.com/Projects/MusicPlayerReciever/input.php?id="+uniqueids[i]+"&time="+times[i]+"&trackid="+trackids[i],true);
				}
					
				request[i].withCredentials = true;
				request[i].setRequestHeader("Content-Type", "text/html");
				request[i].send();
			}
	}
	//Perform an intial entry
	SendTime();
	//Try to send times every 5 seconds
	window.setInterval(SendTime,5000);
</script>
