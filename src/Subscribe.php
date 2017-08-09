<?php

namespace Src;

/**
* Class used to wrap all subscription page creation related methods
*/
class Subscribe extends Main
{

   public function __construct()
   {
      // NOTE: Ajouter un date picker pour la naissance ?
      // TODO: Ajouter un champ pour uploader une image lors de la confirmation d'inscription
      // TODO: Ajouter un validateur de champs pour les paramètres
      // TODO: Utiliser Faker pour générer de fausses données à envoyer à la BDD
      // TODO: Regarder pour utiliser Upload pour l'envoi de données
      // TODO: Finir les regex de validation
   }

   // TODO: Rename all generate with GET
	/**
	* Generates HTML content of the page displayed when GET request on /Subscribe
	* @return string Subscribe's HTML code
	*/
   public function getPageSubscribe()
   {
		// TODO: Restore class from valid to validate in release mode and put back disabled button
		// TODO: Ajouter un champ confirmer l'email

		// TODO: Ajouter un error Handler pour le cas où l'utilisateur n'a pas à s'incrire sur la plateforme
		// FIXME: Changer le bouton mail pour qu'il redirige vers n'importe quel outil de mail
      $content ='
		<div class="col s12">
			<div class="row">
				<h4>Pour vous inscrire veuillez remplir les champs ci-dessous</h4>
			</div>

			<div class="row">
				<div class="input-field col s12">
					<input name="email" id="email" type="email" class="validate"  value="test@user.fr" autofocus>
					<label class="required" for="email" data-error="Adresse email invalide (example@sdc.com)" data-success="Adresse email valide"><p class="required_flag">*</p> Email</label>
				</div>
		   </div>

			<div class="row">
				<div class="input-field col s12">
					<input id="password" type="password" class="required validate" value="azer1">
					<label class="required" for="password" data-error="Mot de passe invalide (longueur minimale 5 caractères, doit contenir 1 chiffre" data-success="Mot de passe valide"><p class="required_flag">*</p> Password</label>
				</div>
			</div>

			<div class="row">
				<div class="input-field col s12">
					<input id="password_confirm" type="password" class="required validate" value="azer1">
					<label class="required" for="password_confirm" data-error="Les mots de passes ne correspondent pas" data-success="Les mots de passe correspondent bien"><p class="required_flag">*</p> Confirm Password</label>
				</div>
			</div>

			'.
			parent::getButton('createAccount()')
			.'
		</div>

		<!-- Modal that is shown to User after submitting the form -->
		' . $this->getSubscribeModal();
      //return parent::generatePage($content, array('FormConfirm', 'Subscribe'));
	  return parent::generatePage($content, array('FormConfirm', 'Subscription'));
   }

	/**
	* Generates modal view displayed after submit button is clicked on 'Subscribe'
	* Calls generic modal creator 'generateModal' to create subsciption specific modal
	* @return string Subscription Modal's HTML code
	*/
	private function getSubscribeModal()
	{
	   return $this->generateModal(
		   array(
			   "200" => "Inscription envoyée",
			   "409" => "Adresse email déjà utilisée"
		   ),
		   array(
			   "200" => "Veuillez vérifier vos mails et cliquer sur le lien qui vous à été envoyé.\nSi vous ne confirmez pas votre inscription dans les 24h la demande sera suprimée, vous devrez alors vous réinscrire.",
			   "409" => "Veuillez consulter vos mails pour vérifier qu\'une inscription n'a pas déjà été effectuée pour cette adresse. Toute inscription non confirmée dans les 24h sera suprimée, vous pourrez alors vous réinscire."
		   )
	   );
	}

	/**
	* Generates modal view displayed after submit button is clicked on 'Subscribe/[token]'
	* Calls generic modal creator 'generateModal' to create subsciption confirmation specific modal
	* @return string Subscription confirmation Modal's HTML code
	*/
	private function getSubscribeConfirmationModal()
	{
	   // TODO: Ajouter un lien pour se connecter en cliquant ici dans le message de succes
	   // TODO: Ajouter un lien pour s'inscrire directement dans le message d'erreur
	   return $this->generateModal(
		   array(
			   "200" => "Inscription terminée",
			   "422" => "Lien invalide"
		   ),
		   array(
			   "200" => "Vous pouvez désormais vous connecter à la plateforme",
			   "422" => "Votre demande d'inscription date de plus de 24h, veuillez recommencer votre inscription"
		   )
	   );
	}

	/**
	* Generic function that generates HTML code for Modal view, base on given parameters
	* @param array(errorCode => errorTitle) $titles Titles for differents error codes
	* @param array(errorCode => errorMessage) $messages Messages for differents error codes
	* @return string Generated modal view HTML code based on given params
	*/
	private function generateModal($titles, $messages)
	{
		// TODO: Voir pour ajouter les button aux paramètres
		$content = '';
		if (sizeof($titles) == sizeof($messages))
		{
			$content .=
'<div id="submitted" class="modal modal-fixed-footer">
	<div class="modal-content">

		<!-- Different titles following type of http response -->
		<h4 id="modal_title" hidden></h4>
		<h4 id="modal_title_424" hidden>Une méthode de la transaction a échoué</h4>
		<h4 id="modal_title_429" hidden>Trop de requêtes ont étés effectuées depuis votre adresse</h4>
		<h4 id="modal_title_err" hidden></h4>';

		foreach ($titles as $errCode => $errTitle)
		{
			$content .= '
		<h4 id="modal_title_'. $errCode .'" hidden>'. $errTitle .'</h4>';
		}

		$content .= '<div class="progress">
			<div id="modal_progression" class="indeterminate"></div>
		</div>

		<!-- Different messages following type of http response -->
		<p id="modal_message_424" hidden>Quelque chose s\'est mal passé lors de la création du compte, veuillez réessayer plus tard. Si le problème persiste, contactez l\administrateur.</p>
		<p id="modal_message_429" hidden>Veuillez patienter quelques instants avant de recommencer.</p>
		<p id="modal_message_err"></p>';

		foreach ($messages as $errCode => $errMessage)
		{
			$content .= '
		<p id="modal_message_'. $errCode .'" hidden>'. $errMessage .'</p>';
		}

		$content .=
'	</div>

	<div class="modal-footer">
		<a id="btn_footer_close" class="modal-action modal-close waves-effect waves-green btn-flat ">Close</a>
		<a id="btn_footer_mail" target="_top" href="http://www.gmail.com" class="modal-action modal-close waves-effect waves-green btn-flat ">Open Mail</a>
	</div>
</div>
';
		}
		return $content;
	}

	// TODO: Changer les fonctions (retirer le get car il n'y a pas de post)
   /**
   * Compute page for GET request on subscribtion validation
   * Calls generic page generator 'generatePage'
   * @param string $token : Unique token associated with the subscribtion request
   * @return string Subscribe's HTML code
   */
   public function getPageSubscribeConfirmation($token)
   {
	   // TODO: Rename JS File (FormConfirm => SubscribeFormValidator)
	   // TODO: Replace default test values with blank and disable button
	   $content = '
		<div class="row">
			<h4>Veuillez renseigner ces informations pour terminer votre inscription</h4>
		</div>

		<div class="row">
			<div class="col">
				<input class="indeterminate-checkbox valign-wrapper" id="first_name_status" type="checkbox" tabindex="-1" />
				<label class="empty_label"></label>
			</div>

			<div class="input-field col s6">
		        <input id="first_name" type="text" class="confirmation" placeholder="Saisissez votre prénom" value="" autofocus>
		        <label for="first_name"><p class="required_flag">*</p> Prénom</label>
	        </div>
		</div>

		<div class="row">
			<div class="col">
				<input type="checkbox" class="indeterminate-checkbox" id="last_name_status" tabindex="-1"/>
				<label class="empty_label"></label>
			</div>

	  		<div class="input-field col s6">
				<input id="last_name" type="text" class="confirmation" placeholder="Saisissez votre nom" value="">
				<label for="last_name"><p class="required_flag">*</p> Nom</label>
	        </div>
		</div>

		<div class="row">
			<div class="col">
				<input type="checkbox" class="indeterminate-checkbox" id="city_status" tabindex="-1" />
				<label class="empty_label"></label>
			</div>

			<div class="input-field col s6">
			  	<select id="city">
					<option value="" disabled selected>Choisissez une ville</option>
					<option value="1" >Liège</option>
					<option value="2" >Nancy</option>
			  	</select>
				<label for="city"><p class="required_flag">*</p> Ville d\'étude</label>
			</div>
		</div>

		<div class="row">
			<div class="col">
				<input type="checkbox" class="indeterminate-checkbox" id="age_status" tabindex="-1" />
				<label class="empty_label"></label>
			</div>

	    	<div class="input-field col s6">
	            <input id="age" type="number" class="confirmation" max="100", min="1" value="" placeholder="Saisissez votre age">
	            <label for="age"><p class="required_flag">*</p> Age</label>
	        </div>
		</div>

		'.
		parent::getButton('confirmSubscription(\''.$token.'\')')
		.'
		'. $this->getSubscribeConfirmationModal();

	  return parent::generatePage($content, array('Subscription', 'SubscribeConfirmationValidator'));
   }
}
