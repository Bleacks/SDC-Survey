<?php

Namespace Src;

class Email extends Main {

	public function __construct()
	{

	}

	/**
	* Generate the email code and send it
	* @param Objet(ORM) $user User which will recive the email
	* @param String $title Subject of the email
	* @param String $body Main text of the email
	* @return bool True if the email was accepted to delevery
	*/
	public function generateEmail($user, $title, $body)
	{
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $user->Email))
		{
			$line_break ="\r\n";
		}
		else
		{
			$line_break ="\n";
		}

		$message_txt =
			$title.'
			Bonjour'.$user->FirstName.' '.$user->LastName.'
			'.$body.'
			Ceci est un email automatique, merci de ne pas y répondre.';

		$message_html = '
			<html>
			<head>
				<title>'.$title.'</title>
				<meta charset="utf-8" />
			</head>
			<body>
				<font color="#303030";>
				<div align="center">
				<table width="600px">
					 <tr>
						<td>
							<div align="center">Bonjour <b>'.$user->FirstName." ".$user->LastName.'</b>,</div>
							'.$body.'
						</td>
					</tr>
					<tr>
						<td align="center">
							<font size="2">
								Ceci est un email automatique, merci de ne pas y répondre.
							</font>
						</td>
					</tr>
				</table>
				</div>
				</font>
			</body>
			</html>
			';

		$boundary = "-----=".md5(rand());

		// $header.='From:"nomdusite.com"<support@nomdusite.com>'.$line_break;
		$header='MIME-Version: 1.0'.$line_break;
		$header.='Content-Type: multipart/alternative;'.$line_break.'boundary=\"$boundary\"'.$line_break;
		$header.='Content-Transfer-Encoding: 8bit';
		$message = $line_break.'--'.$boundary.$line_break;

		//Add message in text.
		$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$line_break;
// A VOIR SI CA MARCHE SANS!!!
		//$message.= "Content-Transfer-Encoding: 8bit".$line_break;
		$message.= $line_break.$message_txt.$line_break;

		$message.= $line_break."--".$boundary.$line_break;

		//Add message in HTML
		$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$line_break;
// A VOIR SI CA MARCHE SANS!!!
		//$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $line_break.$message_html.$line_break;

		$message.= $line_break."--".$boundary."--".$line_break;
		$message.= $line_break."--".$boundary."--".$line_break;

		return mail($user->Email, $title, $message, $header);
	}

	/**
	* Generate the email for the changing password
	* @param Objet(ORM) $user User which will recive the email
	* @return bool True if the email was accepted to delevery
	*/
	public function emailChangePassword($user)
	{
		$title = "Changement de mot de passe";
		$body = "Nous vous confirmons que votre mot de passe a bien été modifier. Cliquer <a href=\"Connect\">ici</a> pour vous connecter.";

		return $this->generateEmail($user->Email, $user->FirstName, $user->LastName, $title, $body);
	}

	/**
	* Generate the email when the user forget his password
	* @param Objet(ORM) $user User which will recive the email
	* @param varchar $recovery_code generate code used for the URL
	* @return bool True if the email was accepted to delevery
	*/
	public function emailForgetPassword($user, $recovery_code)
	{
		$title = "Récupération de mot de passe";
		$body = "Cliquer <a href=\"Recovery/'.$recovery_code.'\">ici</a> pour réinitialiser votre mot de passe."

		return $this->generateEmail($user->Email, $user->FirstName, $user->LastName, $title, $body);
	}

	/**
	* Generate the email when the user created an account
	* @param Objet(ORM) $user User which will recive the email
	* @return bool True if the email was accepted to delevery
	*/
	public function emailSubscribe($user)
	{
		$title = "Bienvenue sur SDC Survey";
		$body = "Nous vous confirmons la création de votre compte. Cliquer <a href=\"Connect\">ici</a> pour vous connecter."

		return $this->generateEmail($user->Email, $user->FirstName, $user->LastName, $title, $body);
	}

}
?>
