<?php

Namespace Src;

/* Form to to get the email of the user to be able to send him a code to update pw
and form to get the his new password*/

class PasswordRecovery extends Main {

	public function __construct()
	{

	}
	// Form to get the email

	public function getPageFormRecoveryEmail()
	{
		$content =
		'
		<h5> Mot de passe oublié </h5></br>
		<div class="row">
			<div class="col s12">
				<div class="row">
					<div class="input-field col s12">
						<input id="recovery_email" type="email" class="required validate" name="recovery_email" value="test@user.fr">
						<label for="recovery_email" data-error="Adresse email non valide" data-success="Adresse email valide">Entrer une adresse Email</label>
					</div>
					'.
						parent::getButton('sendRecoveryRequest()')
						.'
				</div>
			</div>

		</div>';

		return parent::generatePage($content, array('Recovery'));
	}

	// Form to change password (forget password)

	public function getPageFormRecoveryPw ()
	{

		/*<?php  if($section == "changepw") { ?> Nouveau mot de passe pour <?= $_SESSION['recovery_email'] ?>*/

		$content ='
		<div>
			<h5> Mot de passe oublié </h5></br>
			'. $this->getFormNewPassword('sendRecoveryRequest()');

		return parent::generatePage($content, array('RecoveryPassword'));
	}

	// function used in forget password and change password

	public function getFormNewPassword($listener)
	{
		$content =
			'<div class="row">
				<div class="input-field col s12">
					<input id="change_pw" type="password" class="required validate" name="change_pw" value="azer1">
					<label data-error="Mot de passe invalide (longueur minimale 5 caractères, doit contenir 1 chiffre)" data-success="Mot de passe valide"><p class="required_flag" >*</p> Nouveau mot de passe</label>
				</div>
			</div>

			<div class="row">
				<div class="input-field col s12">
					<input id="change_pwc" type="password" class="required validate" name="change_pwc"  value="azer1">
					<label for="password" data-error="Les mots de passe ne correspondent pas" data-success="Les mots de passe correspondent"><p class="required_flag" >*</p> Confirmation du mot de passe</label>
				</div>
			</div>
			'.
				parent::getButton($listener)
				.'

		</div>';

		return $content;
	}

	//Form to change the password

	public function getFormChangePassword()
	{
		$content =
		'
		<h5> Changer de mot de passe </h5></br>
		<div class="row">
				<div class="input-field col s12">
					<input id="old_pw" type="password" value="azer1" class="required validate" name="old_pw">
					<label data-error="Mot de passe invalide (longueur minimale 5 caractères, doit contenir 1 chiffre)" data-success="Mot de passe valide">
						<p class="required_flag" >*</p> Ancien mot de passe
					</label>
				</div>
				<a href="Recovery"> Mot de passe oublié? </a>
		</div>'.
				$this->getFormNewPassword('sendChangePasswordRequest()');

		return parent::generatePage($content, array('RecoveryPassword'));
	}


	// changer avec du css de materialize
	/*<?php if (isset($error)) {echo '<span style ="color:red">'.$error.'</span>';}else{echo"";}?> */
}
