<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* forms_js.php - View
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 12/06/2011
*************************************************************/
?>

<script type="text/javascript">
	
	function deleteMap(id)
	{
		if (confirm("<?php echo __('are you sure you want to delete this map');?>"))
		{
			$("#map_id").val(id);
			$("#action").val('delete');
			$("#edit_map_form").submit();
		}
	}


	$(document).ready(function() 
			{


			//make the apple overlay work for sharing purposes
				$("a[rel]").overlay({
					mask: 'grey',
					effect: 'apple',
					onBeforeLoad: function() {
						 
			            // grab wrapper element inside content
			            var wrap = this.getOverlay().find(".contentWrap");
			 
			            // load the page specified in the trigger
			            wrap.load(this.getTrigger().attr("href"));
			        }
				});

				//make the select all/deselect all check box work
				$("#selectAll").change(function(){
					$('input[id^="map_check_"]').prop('checked', this.checked);
										
				});

				//setup the Delete Selected button handler
				$(".deleteSelectedBtn").click(function(){
					$("#action").val('delete_selected');
					$("#edit_map_form").submit();
					return false;
					});
				
		    });






	

	function changeState(id)
	{
		$("#stateWaiting").html('<img src="<?php echo URL::base();?>media/img/wait16trans.gif"/>');
		//send the data to the server
		$.post("<?php echo url::base()?>share/changestateajax", { "id":id},
				  function(data){
					$("#stateWaiting").html(''); //turn off waiter
				    if(data.status == "success")
				    {
					    $("#mapState").remove();
					    $("#whoHasAccess").prepend(data.html);
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
