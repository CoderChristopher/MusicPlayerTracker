# MusicPlayerTracker
This was I project I worked on for a client in May of 2020. The goal of the project was to develop a javascript audio player for his website that would be able to track simple listener analytics for his music content such as playtime, referral urls, and number of listens.

The project has to major components, the javascript coded public facing part that plays the music, tracks the info, and ships it off to a server; and the private facing component that catches the data, manages the mysql database, and provides a web interface for viewing the data and uploading new audio files to the system.

To view an example of what the audio player looks like check out:

https://www.copelandwebdesign.com/Projects/musicplayer/

this is a stripped down version that demonstrates the core project music player and stat capture. To respect my customer's privacy I prefer not to release the private details of his project. However, all the source code in this git repo is directly representitive of what is running in production currently.

# Other Notes
The only change I have made since May to this source code is I have gone through and annotated with comments various parts of the code to make in more clear what is going on. I have left all bugs, designs oversights, etc inside of the source because I want this be an accurate representation of my PHP/Javascript skills as of the time of the original project. There a few spots where I note things that with my increased understanding now I could have done better.

# Code Tour

	audio-player.php: This is the the actual javascript for the
	audio player. The player itself is designed so that for each instance
	of the player in the web page represents a different audio file. The
	name attribute of each player distinguishes the different players with
	a unique numeric identification. This allows for each player to have a
	unique state and unique tracking in the system.

	authenticate.php: This a simple script that take in a password
	from the request and then compares it against a stored hash of the
	correct password.

	avgpercentage.php: This script performs a basic MYSQL query to
	find the average listen time for a particular track id to be expressed
	as a percentage.

	avgplaytime.php: This script performs a basic MYSQL query to
	find the average listen time for a particular track id to be expressed
	as a numeric time. On current review I can see that this is a
	redundant copy of the avgpercentage.php code, because my quess is that I
	originally had the two seperate *.php files perform the
	percentage/actual calculation server side then send the results to
	client side, but I later opted to push the calculation to client side
	to save processing time on the server. In retrospect one of these
	scripts could be deleted to remove the confusion and redundancy.
	
	changeName.php: This script is used to change the name of
	particular track within the system.

	console.php: This is the script that serves up the console
	that the client uses to view all player stats. Most of the code in
	here is javascript. With a little bit of php to authenticate the
	php_session.

	countplaytime.php: This script performs a simple MYSQL query
	to figure out how many plays a particular track has.

	delete.php: This is the script that delete an audio file from
	the system, its associated SQL Track Info, and also all of the tracks
	records.

	index.php: A simple page the redirects a base request to the
	login.php page. With hindsight I now know that this can be more easily
	and efficently achieved using the apache .htaccess file and using
	either the special number redirects, or in this case setting the
	.htaccess file so that the login.php is the default page when none is
	explicitly identified in the url of the original request.

	input.php: This is the secret sauce script that catches the
	telephones from the javascript player and tracks the user stats.
	Basically at launch of the music player a unique id number for the
	specific player session is generated (see the javascript comments in
	the audio-player.php file for a description of this generation) when
	ever a play time analytic is to be recorded the javascript player
	sends a request off to this endpoint with a few bits of info: the
	above described unqiue session id called the 'id', the track id, the
	time stamp to be recorded, the current actual time of the record, and
	the date.
	
	This info is then used by the script to first my a MYSQL
	select query with the given unique id to see if it already exists. If
	it does not then that must mean this is the first tracking event and
	so a new record must be created using a insert query. But if the track
	id does already exist then that means that all that must be done is
	update the track played time stamp analytic with the new time stamp.

	login.php: This page manages the login process for the client
	console. If a php_session has already been initalized then the script
	sends the user off to console.php to display the console. If not then
	a login prompt is displayed. Logins enter here are then verified with
	the authenticate.php script.

	logout.php: A simple script that handles destroying the
	php_session on logout and displaying a page that confirms a successful logout.
	
	playtime.php: A script that performs a simple MYSQL query to
	get the total amount of time that a single track has been played.

	query.php: This script queries the table of all analytics
	records and then packages them into a colon delimited string stream to
	be sent back to the console javascript program. In hindsight I would
	rewrite the functioning of this element to take in a range and
	starting index of the request so that the query would not return all
	the results of the entire table. Since most of the results end up
	being thrown out by the console program during parsing of a particular
	page of results this means the server is doing way too much work. I
	get away with it in this project because it is small and has low demand.

	removerecord.php: Deletes a specific record from the MYSQL
	database based upon a provided id.

	trackinfo.php: Fetches the track info from the MYSQL database
	to be used by both the javascript player and console. Packages the data
	into a string stream that is comma delimited.

	updatePassword.php: A script to update the password.

	upload.php: A script to take a audio file uploaded via the
	client console.
