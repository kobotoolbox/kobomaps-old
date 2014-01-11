<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* window.php - View
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-02-06
*************************************************************/

$publickey = '';
?>


<div class="commentWindow" id="commentWindow_<?php echo $map->id;?>">

	<h2> <?php echo __('Send a comment to the owner of: ');?> <?php echo $map->title;?></h2>
	
	<?php echo __('Name (Optional)')?></br>
	<input type="text" id="nameField" maxlength="255"/></br></br>
	
	<?php echo __('Email address (Optional)')?></br>
	<input type="text" id="emailField" maxlength=255/></br></br>
	
	<?php echo __('Comment')?></br>
	<textarea type="text" id="commentField"/></br>
	
	<?php if($user == null){?>
        <?php
          $publickey = "6Lfn2-wSAAAAAJeq9ycUb8soS7SPMZLRJ2l3bWuI"; // you got this from the signup page
          ?>
            <div id="recaptchaSpace"></div>
            <script type="text/javascript" src="<?php echo URL::base(); ?>media/js/recaptcha_ajax.js"></script>
        <?php }
	?>
	
	<input type="button" value="<?php echo __('Submit')?>" onclick="submitComment()"/>

</div>

<script type="text/javascript"> 

	$(document).ready(function(){
		<?php if($publickey != ''){?>
			Recaptcha.create("<?php echo $publickey?>", "recaptchaSpace", {
				theme: "clean",
				custom_translations : {instructions_visual: "<?php echo __('Type the text.')?>",
					instructions_audio: "<?php echo __('Type what you hear.')?>",
					play_again: "<?php echo __('Play again')?>",
					cant_hear_this: "<?php echo __('Download as an MP3')?>",
					refresh_btn: "<?php echo __('Ask new words')?>",
					help_btn: "<?php echo __('Help')?>",
					incorrect_try_again: "<?php echo __('Incorrect, try again.')?>"
										}
			});
			<?php }?>
	});
	function submitComment(){
		var myName = $("#nameField").val();
		var myEmail = $("#emailField").val();
		var myComment = $("#commentField").val();
		myComment = '<?php echo __('From map:');?> <a href="<?php echo URL::base().$map->slug?>"><?php echo $map->title;?></a><br/><br/>' + myComment;
		console.log(typeof(Recaptcha));
		if(typeof(Recaptcha)!= 'undefined'){
			$.post("<?php echo URL::base(); ?>message/submitmessage", 
	
					{ 'myName': myName, 'myEmail': myEmail, 'myMessage': myComment, "map_id": "<?php echo $map->id?>", 
						"challenge": Recaptcha.get_challenge(), "response" : Recaptcha.get_response()},
					function(data) {
						if(data.status == 'success'){
							alert('<?php echo __('Message sent successfully!')?>');
							$("a[rel]").overlay().close();
						}
						else{
							alert(data.message);
						}
					}, 'json');		
		}
		else{
			$.post("<?php echo URL::base(); ?>message/submitmessage", 
					
					{ 'myName': myName, 'myEmail': myEmail, 'myMessage': myComment, "map_id": "<?php echo $map->id?>"}, 
					function(data) {
						if(data.status == 'success'){
							alert('<?php echo __('Message sent successfully!')?>');
							$("a[rel]").overlay().close();
						}
						else{
							alert(data.message);
						}
					}, 'json');	
		}
	}


</script>