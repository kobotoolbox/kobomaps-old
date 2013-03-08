<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* shareEdit.php - javascript
* This software is copy righted by Kobo 2013
* Moved here by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-03-08
*************************************************************/
?>

<script type="text/javascript">
/*
 * Created/split to 2013-03-8
 * Dylan Gillespie
 * functions that should only be called when the share window is open, as all the divs are only in those pages
 * 
 */

	function changeState(id)
	{
		$("#stateWaiting").html('<img src="<?php echo URL::base();?>media/img/wait16trans.gif"/>');
		//send the data to the server
		$.post("<?php echo url::base()?>share/changestateajax", { "id":id },
				  function(data){
					$("#stateWaiting").html(''); //turn off waiter
				    if(data.status == "success")
				    {
					    $("#mapState").remove();
					    $("#whoHasAccess").prepend(data.html);
					    if(data.state == "0")
					    {
					    	$("#privateCol_"+id).text("X");
					    }
					    else
					    {
					    	
					    	$("#privateCol_"+id).text("");
					    }
				    }
				    else
				    {
					    if(typeof data.message == 'undefined')
					    {
					    	alert("Error. Please try again.");
					    }
					    else
					    {
						    alert(data.message);
					    }
				    }
				  }, "json");  
	}


	function addNewUser(id)
	{
		$("#stateWaitingUser").html('<img src="<?php echo URL::base();?>media/img/wait16trans.gif"/>');
		var name = $("#newUserName").val();
		var permission = $("#newUserPrivildge").val();
		$.post("<?php echo url::base()?>share/adduserajax", { "id":id,
					"name": name,
					"permission": permission
					},
				  function(data){
					$("#stateWaitingUser").html(''); //turn off waiter
				    if(data.status == "success")
				    {
				    	alert(data.message);
					    $("#accessList").remove();
					    $("#whoHasAccess").append(data.html);
					    $("#newUserName").val('');
				    }
				    else
				    {
					    if(typeof data.message == 'undefined')
					    {
					    	alert("Error. Please try again.");
					    }
					    else
					    {
						    alert(data.message);
					    }
				    }
				  }, "json");  
	}

	/**
	 * Used to update the permission a user has on the system
	 */
	function updateUser(id)
	{
		var elementId = "#colab_perm_"+id;
		$(elementId).after('<img id="updateUserWaiter_"'+id+'src="<?php echo URL::base();?>media/img/wait16trans.gif"/>');
		var permission = $(elementId).val();
		$.post("<?php echo url::base()?>share/updateuserajax", { "id":id,
					"permission": permission
					},
				  function(data){
					$("#updateUserWaiter_"+id).remove(); //turn off waiter
				    if(data.status == "success")
				    {
				    	//alert(data.message);						
				    }
				    else
				    {
					    if(typeof data.message == 'undefined')
					    {
					    	alert("Error. Please try again.");
					    }
					    else
					    {
						    alert(data.message);
					    }
				    }
				  }, "json");  
	}

	/**
	 * This will remove a colaborator from a map
	 */
	function delColab(id)
	{
		var elementId = "#delColab_"+id;
		$(elementId).after('<img id="delUserWaiter_"'+id+'src="<?php echo URL::base();?>media/img/wait16trans.gif"/>');
		$.post("<?php echo url::base()?>share/deluserajax", { "id":id},
				  function(data){
						$("#delUserWaiter_"+id).remove(); //turn off waiter
				    if(data.status == "success")
				    {
				    	//alert(data.message);
					    $("#accessList").remove();
					    $("#whoHasAccess").append(data.html);
				    }
				    else
				    {
					    if(typeof data.message == 'undefined')
					    {
					    	alert("Error. Please try again.");
					    }
					    else
					    {
						    alert(data.message);
					    }
				    }
				  }, "json");  
	}
	


</script>