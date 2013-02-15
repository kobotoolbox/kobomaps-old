<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* window.php - View
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-02-06
*************************************************************/
?>


<div class="commentWindow" id="commentWindow_<?php echo $map->id;?>">

	<h2> <?php echo __('Send a comment to the owner of: ');?> <?php echo $map->title;?></h2>
	
	<?php echo __('Name (Optional)')?></br>
	<input type="text" id="nameField" maxlength=255/></br></br>
	
	<?php echo __('Email address (Optional)')?></br>
	<input type="text" id="emailField" style="width:250px" maxlength=255/></br></br>
	
	<?php echo __('Comment')?></br>
	<textarea type="text" id="commentField" style="width:400px; height:100px"/></br>
	
	<input type="button" value="<?php echo __('Submit')?>" onclick="submitComment()"/>

</div>


<script type="text/javascript"> 

	function submitComment(){
		var myName = $("#nameField").val();
		var myEmail = $("#emailField").val();
		var myComment = $("#commentField").val();

<<<<<<< HEAD:application/views/comment/window.php
		$.post("<?php echo URL::base(); ?>comment/submitmessage", 
=======
		myComment = '<?php echo __('From map:');?> <a href="<?php echo URL::base().'public/view?id='.$map->id?>"><?php echo $map->title;?></a><br/><br/>' + myComment;

		$.post("<?php echo URL::base(); ?>message/submitmessage", 
>>>>>>> df921402337cb9648d568b53857da4be5f4fa4ed:application/views/messages/submit_window.php
				{ 'myName': myName, 'myEmail': myEmail, 'myMessage': myComment, "map_id": "<?php echo $map->id?>" },
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


</script>