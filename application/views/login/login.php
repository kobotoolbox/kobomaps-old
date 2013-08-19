<h2><?php echo __('Log In'); ?></h2>


<?php if(count($errors) > 0 )
{
?>
	<div class="errors">
	<?php echo __('Error'); ?>
		<ul>
<?php 
	foreach($errors as $error)
	{
?>
		<li> <?php echo $error; ?></li>
<?php
	} 
	?>
		</ul>
	</div>
<?php 
}
?>


<form action method="post" accept-charset="utf-8" id="loginForm" name="loginForm">
	<input type="hidden" id="open_id" name="open_id"/>
	<input type="hidden" id="open_id_url" name="open_id_url"/>
	<table id="logintable">
		<tr>
			<td>
				<?php echo __('user name');  ?>
			</td>
			<td>
				<?php echo Form::input('username', null, array('id'=>'username'));?>
			</td>
			<td rowspan="2" style="border-left: solid 1px #aaa;padding-left:15px;text-align:center;">
				<input type="button" value="<?php echo __('OpenID Login')?>" onclick="toggleOpenId(); return false;"/>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo __('password');  ?>
			</td>
			<td>
				<?php echo Form::password('password', null, array('id'=>'password'));?>
			</td>
			
			
			
		</tr>
		<tr>
			<td>
				<br/>
				<a rel="#overlay" href="" ><?php echo __("Forgot password?");  ?></a>
			</td>
			<td>			
			</td>
		</tr>
	</table>
	<div id="openIDTable" style="display:none;">
		<fieldset>
            <legend><?php echo __('OpenID - Sign-in using'); ?></legend>
            <div id="openid_choice" style="display: block; ">
               
                <div id="openid_btns">
                	<a title="log in with Google" href="<?php echo URL::base()?>login?openidurl=<?php echo urlencode('https://www.google.com/accounts/o8/id')?>" style="background: #FFF url('<?php echo URL::base()?>media/img/openid-providers-en.png'); background-position: 0px 0px" class="google openid_large_btn"></a>
                	<a title="log in with Yahoo" href="<?php echo URL::base()?>login?openidurl=<?php echo urlencode('http://me.yahoo.com/')?>" style="background: #FFF url('<?php echo URL::base()?>media/img/openid-providers-en.png'); background-position: -100px 0px" class="yahoo openid_large_btn"></a>
                	<!-- <a title="log in with Facebook" href="#" onclick="myFBlogin(); return false;" style="background: #FFF url('<?php echo URL::base()?>media/img/openid-providers-en.png'); background-position: -500px 0px" class="facebook openid_large_btn"></a> -->
                	<!-- <a title="log in with Twitter" href="<?php echo URL::base()?>login?openidurl=<?php echo urlencode('https://www.twitter.com')?>" style="background: #FFF url('<?php echo URL::base()?>media/img/openid-providers-en.png'); background-position: -600px 0px" class="twitter openid_large_btn"></a> -->
                	<br/>
                	<div style="clear:both;">
                		<input type="button" value="<?php echo __('Traditional Login')?>" onclick="toggleOpenId(); return false;"/>
                	</div>
                </div>
            </div>
            <div id="openid_input_area"></div>
            <noscript>
                &lt;p&gt;OpenID is service that allows you to log-on to many different websites using a single indentity.
				Find out &lt;a href="http://openid.net/what/"&gt;more about OpenID&lt;/a&gt; and &lt;a href="http://openid.net/get/"&gt;how to get an OpenID enabled account&lt;/a&gt;.&lt;/p&gt;
            </noscript>
        </fieldset>
	</div>
	<br/>
	<br/>
	<?php echo Form::submit("login_form",  __("Log In")); ?>
<?php echo Kohana_Form::close(); ?>	

	
	<div class="apple_overlay" id="overlay">
		<div class="contentWrap">
			<h2><?php echo __('Forgot Password'); ?></h2>
			<p><?php echo __('Enter your email address and instructions to reset your password will be emailed to you');?></p>
			<?php echo __('email address'); ?> <input type="text" style="width:200px;" name="reset_email" id="reset_email">
			<br/>
			<?php echo Form::button("rest_form",  __('Submit'), array('onclick'=>"submit_reset(); return false;")); ?> <img id="reset_spinner" style="display:none;" src="<?php echo url::base(); ?>media/img/wait16trans.gif"/>
			<br/><br/>
			<div id="reset_response" style="display:none;">
			</div>
		</div>
	</div>
	
	
<div id="fb-root"></div>
<script>
  // Additional JS functions here
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '230846520384211', // App ID
      channelUrl : '<?php echo URL::site('channel.html',true);?>', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });


    FB.getLoginStatus(function(response) {
    	  if (response.status === 'connected') {
    		  <?php if(count($errors) == 0 AND FALSE ){?>
    		  $("#open_id").val(response.authResponse.userID);
    		  $("#password").val(response.authResponse.accessToken);
    		  $("#open_id_url").val('facebook.com');
    		  $("#loginForm").submit();
    		  return true;
    		  <?php } ?>
    	  } else if (response.status === 'not_authorized') {
    	    //do nothing
    	  } else {
    		//do nothing
    	  }
    	 });

  };


  function myFBlogin() {
	    FB.login(function(response) {
	        if (response.authResponse) {
	        	$("#open_id").val(response.authResponse.userID);
	    		$("#password").val(response.authResponse.accessToken);
	    		$("#open_id_url").val('facebook.com');
	    		$("#loginForm").submit();
	        } else {
	            alert("<?php echo __('Facebook login failed.');?>");
	        }
	    });
	}

  

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));
</script>
	
