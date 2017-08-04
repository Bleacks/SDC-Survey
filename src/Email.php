<?php

Namespace Src;

class Email extends Main {
	
	public function __construct()
	{
		
	}
	
	public function sendEmail($firstName, $lastName)
	{
		$header="MIME-Version: 1.0\r\n";
		// $header.='From:"nomdusite.com"<support@nomdusite.com>'."\n";
		$header.='Content-Type:text/html; charset="utf-8"'."\n";
		$header.='Content-Transfer-Encoding: 8bit';
		$message = '
				<html>
				<head>
					<title>Récupération de mot de passe </title>
					<meta charset="utf-8" />
				</head>
				<body>
					<font color="#303030";>
					<div align="center">
					<table width="600px">
						 <tr>
							<td>
									 
								<div align="center">Bonjour <b>'.$firstName.$lastName.'</b>,</div>
									Cliquer <a href="https://127.0.0.1/recovery_pw.php?section=code&code='.$recovery_code.'">ici</a> pour réinitialiser votre mot de passe.
																	 
							</td>
						</tr>
						<tr>
							<td align="center">
								<font size="2">
									Ceci est un email automatique, merci de ne pas y répondre
								</font>
							</td>
						</tr>
					</table>
					</div>
					</font>
				</body>
				</html>
				';
		mail($recovery_email,"Récupération de mot de passe",$message,$header);
	}
}
?>