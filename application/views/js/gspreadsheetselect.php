<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Creates the Javascript needed to ask a user to sign in to Google Drive, show a list of google docs, and then 
 * get the download url of a spreadsheet. 
 * 
 * @author John Etherton <john@ethertontech.com>
 * @category KoboMaps
 * @package KoboMaps
 *
 * 
 * File created on 2012-12-10 by John Etherton
 */

//get the client id
$config = Kohana::$config->load('googleapi');
$client_id = $config['client_id'];
?>  

    <script type="text/javascript">
      var CLIENT_ID = '<?php echo $client_id?>';
      var SCOPES = 'https://www.googleapis.com/auth/drive';

      /**
       * Called when the user clicks on authorize
       */
       function authorizeKoboMaps()
       {
    	   gapi.auth.authorize(
                   {'client_id': CLIENT_ID, 'scope': SCOPES, 'immediate': false},
                   handleAuthResult);
       }



       /**
        * Callback, that's called when the Google API gives us a list of files
        */ 
		function displayFileList (data)
		{
			for(i in data.items)
			{
				var item = data.items[i];

				$("#googleFilesList").append("<tr><td></td><td>"+item.title+"</td><td>"+item.ownerNames[0]+"</td><td>"+item.modifiedDate+"</td></tr>");
			}

			$("#googlewaiter").hide();
		}      
      

       /**
        * Called when authorization server replies.
        *
        * @param {Object} authResult Authorization result.
        */
		function handleAuthResult(authResult) 
		{
			var authButton = document.getElementById('authorizeButton');
			authButton.style.display = 'none';
			if (authResult && !authResult.error) 
			{
				// Access token has been successfully retrieved, requests can be sent to the API.
				// so ask for a list of files
				$("#googlewaiter").show();
				 gapi.client.request({
					 path: 'drive/v2/files', 
					 callback:displayFileList,
					 params: {q:"mimeType='application/vnd.google-apps.spreadsheet'"}
				});				 
  			} 
  			else 
  	  		{
				// No access token could be retrieved, show the button to start the authorization flow.
    			authButton.style.display = 'block';
			}
		}



    </script>
    <script type="text/javascript" src="https://apis.google.com/js/client.js"></script>
