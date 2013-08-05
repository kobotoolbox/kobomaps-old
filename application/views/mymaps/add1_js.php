<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* add1_js.php - View
* Written by Etherton Technologies Ltd. 2012
* Started on 12/06/2011
*************************************************************/
?>
	
<script type="text/javascript" src="<?php echo URL::base(); ?>media/js/jscolor/jscolor.js"> </script>


<script type="text/javascript">

/**
* Used to toggle specific divs
* @param string elem_id generic id to toggle divs
*/
function toggle_id(elem_id)
{
	$('#'+elem_id).toggle('slow');
}

/**
* Used to toggle all divs of a specific class, and if it's advanced, flip the gradient box
* @param string elem_class generic id to toggle classes
*/
function toggle_class(elem_class)
{
	$('.'+elem_class).toggle('slow');
	if($("#gradient").prop('checked')){
		$(".gradient_explain").show('slow');
	}
	else {
		$(".gradient_explain").hide();}

}

/**
* @param string elem_id id of the box to include map style
*/
function set_default_map_style(elem_id)
{
	$('#'+elem_id).val('<?php echo json_decode(Model_Map::get_style_default_js()); ?>');
	
}

function openGradient(){
	$(".gradient_explain").toggle('slow');
}

//changes the color of the slug box dependent on the results of the checker
$(document).ready(function(){
	$("#slug").change(function(){
		$("#slug").css('border-color', '');
		$.post("<?php echo URL::base(); ?>mymaps/checkslug", { "slug": $("#slug").val(), 'id': <?php echo $map_id ?> }).done(
				function(response) {
					response = JSON.parse(response);

					if(response.status == 'valid'){
						$("#slug").css('border-color', 'green');
						$("#slug").val(response.slug);
					}
					else if(response.status == 'illegal'){
						$("#slug").css('border-color', 'red');
						alert('<?php echo __('Your slug had illegal characters, they have been replaced.')?>');
						$("#slug").val(response.slug);
					}
					else if(response.status == 'notUnique'){
						$("#slug").css('border-color', 'red');
						alert('<?php echo __('You slug has already been used. Please choose another.')?>');
					}
				});
	});
});	
	


</script>