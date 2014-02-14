<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* facebook.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by John Etherton <john@ethertontech.com> Tino also did some work on this back in the day
* Started on 2013-01-23
* Renders the JS needed to share maps and stuff on facebook.
*************************************************************/
?>
	


<script src='https://connect.facebook.net/en_US/all.js'></script>
<script type="text/javascript">

	$(document).ready(function() {
		FB.init({appId: "230846520384211", status: true, cookie: true});
	});

	/**
	 * Used to post things to a user's facebook.
	 * link String - The string that you want the user to share
	 * name String - Name of thing to share
	 * caption String - string that appears under the name
	 * description String - a long description about what you're sharing
	 * callback function - callback function, null if none
	 */
	function postToFacebookFeed(link, name, caption, description, callback) {
		// calling the API ...
		var obj = {
			method: 'feed',
			redirect_uri: 'http://ethertontech.com/dev/kobo/kobomaps/share/fbredirect',
			link: link,
			picture: 'http://ethertontech.com/dev/kobo/kobomaps/media/img/kobo_logo_square_75x75.png',
			name: name,
			caption: caption,
			description: description
		};
		
		function callbackDefault(response) {
			
		}

		//if no callback is given give them the default
		if(callback == null || typeof callback == 'undefined')
		{
			callback = callbackDefault;
		}
		
		FB.ui(obj, callback);
	}
    

</script>
