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

				$("#googleFilesList").append('<tr><td><input type="radio" name="googleFile" value="'+item.id+'" onclick="googleFileSelected(\''+item.id+'\',\''+item.exportLinks["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"]+'\');"></td><td>'+item.title+"</td><td>"+item.ownerNames[0]+"</td><td>"+item.modifiedDate+"</td></tr>");
			}
			//console.log(data.items);
			$("#googlewaiter").hide();
		}      

	  /**
       * Called when a user selects the checkbox for a given google spreadsheet
       *
       * @param {String} id The id of the google file
       * @param {String} link The url to the Excel version of the file
       */
       function googleFileSelected(id, link)
       {
           $("#googleid").val(id);
           $("#googlelink").val(link);
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
				//store the authorization token
				$("#googletoken").val(gapi.auth.getToken().access_token);
				
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
