<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* main.php - View
* This software is copy righted by Etherton Technologies Ltd. 2013
* Writen by Dylan Gillespie
* Started on 2013-01-23
* Show user their stats
*************************************************************/
?>

<h1>Loaded</h1>


<div id="titleBar">
<?php 
	echo Form::open(NULL, array('id'=>'edit_custompage_form', 'enctype'=>'multipart/form-data'));
	echo Form::hidden('action','edit', array('id'=>'action'));
	
	echo'<div id="customWork" style="float:right">';
	
	echo __('Title of page: ');
	echo Form::input('slug_id', $data['slug'], array('id'=>'slug_id', 'style'=>'width:300px;', 'maxlength' => '128'));
	
	echo '</br></br>';
	echo __('Content of page: ');
	echo '</br>';
	echo Form::textarea('content', $data['content'], array('id'=>'content', 'style'=>'width:600px;'));
	echo '</div>';
	
	
	echo '<div id="pageTable" style="height: 500px">';
	echo Form::select('pages', $pages, null, array('id'=>'pages', 'style' => 'width: 175px; height: 22px'));
	echo '</br></br>';
	echo Form::submit('edit', __('Save'), array('id'=>'edit_button'));
	echo '</br></div>';

	echo Form::close();
?>
