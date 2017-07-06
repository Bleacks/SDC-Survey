<?php

Namespace Src;

/**
* Class used to wrap all connection page creation related methods
*/
class Connect extends Main {

	public function __construct()
	{

	}

	/**
	* Generates content of the connect page
	* @return $content HTML body of the connect page
	*/
	public function getPageConnect()
	{
		$content =
'<div class="container">
	<div class="row">
		<div class="col s12">

			<div class="row">
				<h4>Veuillez renseigner les champs ci-dessous pour vous connecter</h4>
			</div>

			<div class="row">
				<div class="input-field col s12">
					<input name="email" id="email" type="email" class="required valid"  value="tst@user.fr" autofocus>
					<label for="email"><p class="required_flag">*</p> Email</label>
				</div>
			</div>

			<div class="row">
				<div class="input-field col s12">
					<input id="password" type="password" class="required valid" value="azer1">
					<label for="password"><p class="required_flag">*</p> Password</label>
				</div>
				<div class="input-field col s12">
					<input id="remember" type="checkbox" checked class="filled-in">
					<label for="remember">Se souvenir de moi</label>
				</div>
			</div>

		  '.
		  parent::getButton('connectUser()')
		  .'

		</div>
	</div>
</div>';
		return parent::generatePage($content, array('Connection', 'ConnectionConfirmation'));
	}
}
