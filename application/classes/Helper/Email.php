<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Email.php - Helper
* Used to send email to unsuspecting victims.
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-02-14
*************************************************************/



class Helper_Email
{

	/**
	 * Used to send emails to people
	 * @param array $to something like array('jsmith@email.com'=>'Jimmy Smith', 'ksmith@email.com'=>'Kelly Smith');
	 * @param array $from something like array('admin@kobomap.com'=>'Kobo Maps Admin')
	 * @param string $subject you know like "you've excited your usage limit"
	 * @param string $body This can be HTML "<h1>Warning</h1> You've blown way past your limit."
	 */
	public static function send_email($to, $from, $subject, $body)
	{	
		if($_SERVER["SERVER_NAME"] != 'localhost' AND $_SERVER["SERVER_NAME"] != '127.0.0.1'){
			require Kohana::find_file('swiftmailer', 'classes/lib/swift_required');
			//Create the Transport
			$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
				->setUsername('maps@kobotoolbox.org')
				->setPassword("fy7D.rhf'f");
			//Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);
			//Create a message
			$message = Swift_Message::newInstance('Email')
			->setSubject($subject)
			->setFrom($from)
			->setTo($to)
			->setBody($body, 'text/html');
			//echo($message->toString());
			//phpinfo();
			//Send the message
			$result = $mailer->send($message);
		}
	}
}//end class
