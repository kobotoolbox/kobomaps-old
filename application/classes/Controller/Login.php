<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Login.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Login extends Controller_Main {

	
  	
	/**
	where users go to sign up
	*/
	public function action_index()
	{
		
		//if they're already logged in then take them to their profile
		$auth = Auth::instance();
		if( $auth->logged_in() OR $auth->auto_login())
		{
			HTTP::redirect(Session::instance()->get_once('returnUrl','home'));
		}
		
		 
		$this->template->html_head->title = __("Login");
		$this->template->content = View::factory('login/login');
		$this->template->header->menu_page = 'login';
		$this->template->content->errors = array();

		
		//set the focus on the username input box
		$this->template->html_head->script_views[] = '<script type="text/javascript">$(document).ready(function() {  $("#username").focus();});</script>';
		
		$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';		
		//main JS view
		$this->template->html_head->script_views[] = view::factory('login/login_js');
		if(isset($_GET['openidurl']))
		{
			if(strlen($_GET['openidurl']) != 0) //they're using openID
			{
				//do open id
				$this->detect_open_id();
			}
		}
		
		//check if this is the result of an open id login
		$sesh  = Session::instance();
		if($sesh->get_once('open_id_login','0') == '1')
		{
			$_POST['username'] = $sesh->get_once('email','');
			$_POST['password'] = $sesh->get_once('password','');
		}
		
		
		if(!empty($_POST)) // They've submitted their login form
		{
			//check if it's a facebook style login
			if(isset($_POST['open_id']) AND $_POST['open_id']!='' AND isset($_POST['open_id_url']) AND $_POST['open_id_url']!='')
			{
				//does such a person exists
				$user = ORM::factory('User')
					->where('open_id', '=', $_POST['open_id'] . $_POST['open_id_url'])
					->find();
				if(!$user->loaded())
				{
					Session::instance()->set('open_id_sign_up','1');
					Session::instance()->set('email','');
					Session::instance()->set('password',$_POST['password']);
					Session::instance()->set('first_name','');
					Session::instance()->set('last_name','');
					Session::instance()->set('open_id',$_POST['open_id'] . $_POST['open_id_url']);
					HTTP::redirect('/signup');
				}
				else
				{ //they do exist
					$_POST['username'] = $user->username;		
				}
			}
			
			//check if they're using open id
			
			$auth->login($_POST['username'], $_POST['password'], true);
			if($auth->logged_in())
			{
				HTTP::redirect(Session::instance()->get_once('returnUrl','mymaps'));	
			}
			else
			{				
				$this->template->content->errors[] = __("incorrect login");					
			}
		
		}
		else 
		{	//They're visiting for the first time		
		
		}
	}//end index action
	
	
	
	/**
	 * this function is called by the login function
	 * to handle open id stuff
	 * @return string Null if there's no error, else returns an error string
	 */
	protected function detect_open_id()
	{
		$auth_url = $_GET['openidurl'];
		
		if($auth_url != null)
		{
			
			set_include_path(get_include_path() . PATH_SEPARATOR . MODPATH.'vendor/php-openid/');
			
			require_once Kohana::find_file('php-openid', 'Auth/OpenID/Consumer');			
			require_once Kohana::find_file('php-openid', 'Auth/OpenID/SReg');
			require_once Kohana::find_file('php-openid', 'Auth/OpenID/AX');
			require_once Kohana::find_file('php-openid', 'Auth/OpenID/PAPE');
			
			
			$s = $this->getStore();
								        
		    $consumer = new Auth_OpenID_Consumer($s);
		    
		 	// Begin the OpenID authentication process.
		    $auth_request = $consumer->begin($auth_url);
		    
		    
		    //for Google, might also need for Yahoo.
		    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email',2,1, 'email');
		    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first',1,1, 'firstname');
		    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last',1,1, 'lastname');
		    
		    
		    // Create AX fetch request
		    $ax = new Auth_OpenID_AX_FetchRequest;
		    
		    // Add attributes to AX fetch request
		    foreach($attribute as $attr){
		    	$ax->add($attr);
		    }
		    
		    // Add AX fetch request to authentication request
		    $auth_request->addExtension($ax);
		    
		    
		
		    // No auth request means we can't begin OpenID.
		    if (!$auth_request) {
		        Echo "Authentication error; not a valid OpenID.";
		    }
		
		    $sreg_request = Auth_OpenID_SRegRequest::build(
		                                     // Required
		                                     array('email'),
		                                     // Optional
		                                     array());
		
		    if ($sreg_request) {
		        $auth_request->addExtension($sreg_request);
		    }
		

		    $realm = URL::site(null, TRUE);
		    $return_url = URL::site('/login/openid', TRUE);
		    $redirect_url = $auth_request->redirectURL($realm,$return_url);
		    HTTP::redirect($redirect_url);
		}
	}
		
	
	
	
	
	protected function &getStore() {
		
		
		//require_once Kohana::find_file('php-openid', 'Auth/OpenID/MySQLiStore');
		//require_once Kohana::find_file('php-openid', 'Auth/OpenID/DatabaseConnectionMysqli');
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/FileStore');
		
		/*
		$server = Kohana::$config->load('database.default.connection.hostname');
		$user_name = Kohana::$config->load('database.default.connection.username');
		$password = Kohana::$config->load('database.default.connection.password');
		$database = Kohana::$config->load('database.default.connection.database');
		
		$database = new mysqli($server, $user_name, $password, $database);
		
		$openIdDb = new Auth_OpenID_DatabaseConnectionMysqli($database);
		
		$s = new Auth_OpenID_MySQLiStore($openIdDb);
		*/
		
		
		$store_path = null;
		if (function_exists('sys_get_temp_dir')) {
			$store_path = sys_get_temp_dir();
		}
		else {
			if (strpos(PHP_OS, 'WIN') === 0) {
				$store_path = $_ENV['TMP'];
				if (!isset($store_path)) {
					$dir = 'C:\Windows\Temp';
				}
			}
			else {
				$store_path = @$_ENV['TMPDIR'];
				if (!isset($store_path)) {
					$store_path = '/tmp';
				}
			}
		}
		$store_path .= DIRECTORY_SEPARATOR . 'kobo_open_id';
		
		$store_path = substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME'])-9);
		$store_path .= 'uploads/openid';
		
		
		$s = new Auth_OpenID_FileStore($store_path);
		
		return $s;
		
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/FileStore');
		$STORE_PATH = "/tmp/_php_consumer_test";
		$store = new Auth_OpenID_FileStore($STORE_PATH);
		return $store;
		
		
	}
	
	/**
	 * Called when a user wants to reset their password
	 */
	 public function action_reset()
	 {
		 //this function isn't participating in the auto render side of things
		$this->template = "";
		$this->auto_render = FALSE;
		
		//get the email.
		$email = null;
		if(isset($_GET['email']))
		{
			$email = urldecode($_GET['email']);
		}
		//if there's no email:
		if($email == null)
		{
			echo __('email null');
			return;
		}
		//get the user that corresponds to this email
		$user = ORM::factory('User')->and_where('email', '=', $email)->find();
		if(!$user->loaded())
		{
			echo __('no user found with email');
			return;
		}
		
		$this->_email_resetlink($user);
		echo __('reset email sent');
		
	 }//end action reset
	 
	 
	 /**
	  * Called when a user has received the password reset key and wants
	  * to now reset their password.
	  */
	public function action_resetpassword()
	 {
	 	//make sure there's a key
	 	$hash = isset($_GET['key']) ? $_GET['key'] : '';
	 	
	 	//get the user that's requesting the reset
	 	$user = ORM::factory('User')
	 		->where('reset_hash','=',$hash)
	 		->find();
	 	
	 	//if this isn't valid get them out of here
	 	if(!$user->loaded())
	 	{
	 		HTTP::redirect('');
	 	}
	 	
	 	//get the expiration date of the key
	 	$expiration_date = strtotime($user->reset_expire);
	 	if($expiration_date < time())
	 	{
	 		HTTP::redirect('');
	 	}
	 	
	 	
	 	$this->template->html_head->title = __('Password Reset');
	 	$this->template->content = View::factory('password_reset');
	 	$this->template->header->menu_page = 'login';
	 	$this->template->content->errors = array();
	 	
	 	//set the focus on the username input box
	 	$this->template->html_head->script_views[] = '<script type="text/javascript">$(document).ready(function() {  $("#password").focus();});</script>';
	 	
	 	$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';
		
	 	
	 	if(!empty($_POST)) // They've submitted their registration form
	 	{
	 		//check if the two passwords match
	 		if(isset($_POST['password']) AND isset($_POST['password_confirm']))
	 		{
	 			if($_POST['password'] == $_POST['password_confirm'])
	 			{	 				
	 				//reset the reset values in the DB and store this new password
	 				$auth = Auth::instance();
	 				$_POST['reset_hash'] = null;
	 				$_POST['reset_expire'] = null;
	 				$user->update_user($_POST, array('password','reset_hash','reset_expire'));
	 				//kick them out to the login page
	 				HTTP::redirect('login');
	 			}
	 			else
	 			{
	 				$this->template->content->errors[] = __('Your passwords don\'t match');
	 			}
	 		}
	 	}	 	
	 		
	 }
	 
	private function _email_resetlink( $user)
	{
		//first create a hash
		$auth = Auth::instance();
		$hash = $auth->hash_password(date('U').$user->password);
		$user->reset_hash = $hash;
		$user->reset_expire = date('Y-m-d G:i:s', time() + (2*60*60*1000)); //give them two hours
		$user->save();
		//create the link
		$secret_link = '<a href="'.URL::site(NULL, 'http').'login/resetpassword?key='.$hash.'">'.URL::site(NULL, 'http').'login/resetpassword?key='.$hash.'</a>';
		//figure out the no reply email address
		$config = Kohana::$config->load('config');
		$no_reply = $config->get('no_reply_email');

		$to = array($user->email=>$user->first_name. ' '. $user->last_name);
		$from =array($no_reply=>__('KoboMaps System'));
		$subject = __('KoboMaps Password Reset');		
		$body = $user->first_name.' '.__('reset your password by following this link:').' '. $secret_link;		
		
		Helper_Email::send_email($to, $from, $subject, $body);

	}
	
	
	
	/** 
	 * used to handle income open id replies.
	 */
	public function action_openid()
	{
		
		set_include_path(get_include_path() . PATH_SEPARATOR . MODPATH.'vendor/php-openid/');
			
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/Consumer');
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/MySQLiStore');
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/DatabaseConnectionMysqli');
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/SReg');
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/AX');
		require_once Kohana::find_file('php-openid', 'Auth/OpenID/PAPE');
		
		
		$store = $this->getStore();
		$consumer = new Auth_OpenID_Consumer($store);

		
		// Complete the authentication process using the server's
		// response.
		$return_to = URL::site('/login/openid', TRUE);
		$response = $consumer->complete($return_to);
		
		// Check the response status.
		if ($response->status == Auth_OpenID_CANCEL) {
			// This means the authentication was cancelled.
			$msg = 'Verification cancelled.';
			echo $msg;
		} else if ($response->status == Auth_OpenID_FAILURE) {
			// Authentication failed; display the error message.
			$msg = "OpenID authentication failed: " . $response->message;
			echo $msg;
		} else if ($response->status == Auth_OpenID_SUCCESS) {
			// This means the authentication succeeded; extract the
			// identity URL and Simple Registration data (if it was
			// returned).
			$open_id = $response->getDisplayIdentifier();
		
		
			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();
		
			$email = null;
			if(isset($sreg['email']))
			{
				$email = $sreg['email'];
			}
			
			$fullname = null;
			if(isset($sreg['fullname']))
			{
				$fullname = $sreg['fullname'];
			}
			
			
			//look for AX items
			$ax = new Auth_OpenID_AX_FetchResponse();
			$ax_response = $ax->fromSuccessResponse($response);
			//grab the email via AX if we haven't set it already and if we have it
			if($email == null AND isset($ax_response->data['http://axschema.org/contact/email']))
			{
				$email = $ax_response->data['http://axschema.org/contact/email'][0];
			}
			
			//grab the name if we can and haven't already
			$first_name = null;
			$last_name = null;
			if(isset($ax_response->data['http://axschema.org/namePerson/first']) AND
					isset($ax_response->data['http://axschema.org/namePerson/last']))
			{
				$first_name = $ax_response->data['http://axschema.org/namePerson/first'][0];
				$last_name = $ax_response->data['http://axschema.org/namePerson/last'][0];
			}
			
			/*		
			$pape_resp = Auth_OpenID_PAPE_Response::fromSuccessResponse($response);
		
			if ($pape_resp) {
				if ($pape_resp->auth_policies) {
					$success .= "<p>The following PAPE policies affected the authentication:</p><ul>";
		
					foreach ($pape_resp->auth_policies as $uri) {
						$escaped_uri = htmlentities($uri);
						$success .= "<li><tt>$escaped_uri</tt></li>";
					}
		
					$success .= "</ul>";
				} else {
					$success .= "<p>No PAPE policies affected the authentication.</p>";
				}
		
				if ($pape_resp->auth_age) {
					$age = htmlentities($pape_resp->auth_age);
					$success .= "<p>The authentication age returned by the " .
							"server is: <tt>".$age."</tt></p>";
				}
		
				if ($pape_resp->nist_auth_level) {
					$auth_level = htmlentities($pape_resp->nist_auth_level);
					$success .= "<p>The NIST auth level returned by the " .
							"server is: <tt>".$auth_level."</tt></p>";
				}
		
			} else {
				$success .= "<p>No PAPE response was sent by the provider.</p>";
			}
			*/
			
			//first check if there's a user already with this username, which is the email address
			$user = ORM::factory('User')
				->where('open_id', '=',$email)
				->find();
			if($user->loaded()) //if a user does exists then log them in
			{
				Session::instance()->set('open_id_login','1');
				Session::instance()->set('email',$email);
				Session::instance()->set('password',$open_id);				
				HTTP::redirect('/login');
			}
			else //takie them to create a new user
			{
				Session::instance()->set('open_id_sign_up','1');
				Session::instance()->set('email',$email);
				Session::instance()->set('password',$open_id);
				Session::instance()->set('first_name',$first_name);
				Session::instance()->set('last_name',$last_name);
				Session::instance()->set('open_id',$email);
				HTTP::redirect('/signup');
			}
		}
		
		exit;
	}
	
} // End Welcome
