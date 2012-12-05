<?php
/***********************************************************
* header.php - View
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/ 
?>
<div id="header">
	<div id="login_logout">
		<?php
			$auth = Auth::instance();
			$logged_in = $auth->logged_in() OR $auth->auto_login();
			if($logged_in)
			{
				$user = ORM::factory('user',$auth->get_user());
				$user_name = $user->first_name. ' ' . $user->last_name;
				echo ' <span class="user_info"><a href="'.url::base().'home">'.__('welcome')." ".$user_name .'</a></span> &gt;&gt;';
				echo ' <span class="user_info"><a href="'.url::base().'home/profile">'.__('profile') .'</a></span>';
				echo ' <span class="user_action"><a href="'.url::base().'logout">'.__('logout').'</a></span>';
			}
			else
			{
				echo '<span class="user_action">';
				echo '<a href="'.url::base().'login">'.__('login').'</a>';
				echo ' '.__("or"). ' ';
				echo '<a href="'.url::base().'signup">'.__('signup').'</a>';
				echo '</span>';
			}
		?>
	</div>
	<!--
	<h1>
		<a href="<?php //echo url::base(); ?>">
			<?php //echo __('site name'); ?>
		</a>
	</h1>
	<p>
		<a href="<?php //echo url::base(); ?>">
			<?php //echo __('tagline'); ?>
		</a>
	</p>
	-->
</div>
<div id="mainMenu"><?php echo Helper_Mainmenu::make_menu($menu_page, $user);?></div>
