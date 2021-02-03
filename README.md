# MusicPlayerTracker
This was I project I worked on for a client in May of 2020. The goal of the project was to develop a javascript audio player for his website that would be able to track simple listener analytics for his music content such as playtime, referral urls, and number of listens.

The project has to major components, the javascript coded public facing part that plays the music, tracks the info, and ships it off to a server; and the private facing component that catches the data, manages the mysql database, and provides a web interface for viewing the data and uploading new audio files to the system.

To view an example of what the audio player looks like check out:

https://www.copelandwebdesign.com/Projects/musicplayer/

this is a stripped down version that demonstrates the core project music player and stat capture. To respect my customer's privacy I prefer not to release the private details of his project. However, all the source code in this git repo is directly representitive of what is running in production currently.

# Other Notes
The only change I have made since May to this source code is I have gone through and annotated with comments various parts of the code to make in more clear what is going on. I have left all bugs, designs oversights, etc inside of the source because I want this be an accurate representation of my PHP/Javascript skills as of the time of the original project. There a few spots where I note things that with my increased understanding now I could have done better.

# Code Tour

	audio-player.php: This is the the actual javascript for the audio player. The player itself is designed so that for each instance of the player in the web page represents a different audio file. The name attribute of each player distinguishes the different players with a unique numeric identification. This allows for each player to have a unique state and unique tracking in the system.

	authenticate.php: This a simple script that take in a password from the request and then compares it against a stored hash of the correct password.

	avgpercentage.php: This script performs a basic MYSQL query to find the average listen time for a particular track id to be expressed as a percentage.

	avgplaytime.php: This script performs a basic MYSQL query to find the average listen time for a particular track id to be expressed as a numeric time. On current review I can see that this is a redundant copy of the avgpercentage.php code, because my quess is that I originally had the two seperate *.php files perform the percentage/actual calculation server side then send the results to client side, but I later opted to push the calculation to client side to save processing time on the server. In retrospect one of these scripts could be deleted to remove the confusion and redundancy.

	changeName.php: This script is used to change the name of particular track within the system.

	console.php: This is the script that serves up the console that the client uses to view all player stats. Most of the code in here is javascript. With a little bit of php to authenticate the php_session.

	countplaytime.php: This script performs a simple MYSQL query to figure out how many plays a particular track has.

	delete.php: This is the script that 
