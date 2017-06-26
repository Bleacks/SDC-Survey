<?php

namespace Src;

/**
* Class used to create Page base
*/
class Subscribe extends Main
{

   public function __construct()
   {
      // TODO: Ajouter un date picker pour la naissance
      // TODO: Ajouter un champ pour uploader une image lors de la confirmation d'inscription
      // TODO: Découper l'inscription en deux étapes comme dans la maquette
      // TODO: Ajouter un validateur de champs pour l'inscription, la connexion et les paramètres
      // TODO: Utiliser Faker pour générer de fausses données à envoyer à la BDD
      // TODO: Regarder pour utiliser Upload pour l'envoi de données
      // TODO: Finir les regex de validation
      // TODO: Ajouter des regex sur les form d'inscription et connexion
	  // TODO: Tester le bon fonctionnement de la méthode de suppression des token périmés
   }

	/**
	* Computes content of the page displayed when GET request on /Subscribe
	*/
   public function getPageSubscribe()
   {
		// TODO: Restore class from valid to validate in release mode and put back disabled button
		// TODO: Ajouter un champ confirmer l'email

		// TODO: Ajouter un error Handler pour le cas où l'utilisateur n'a pas à s'incrire sur la plateforme
		// FIXME: Changer le bouton mail pour qu'il redirige vers n'importe quel outil de mail
		// FIXME: Ajouter une méthode d'ajout de scripts automatique
      $content =
'<div class="row">
	<div class="col s12">

		<div class="row">
			<div class="input-field col s12">
				<input name="email" id="email" type="email" class="valid"  value="test@user.fr" autofocus>
				<label class="required" for="email" data-error="Email not valid (example@sdc.com)" data-success="Email valid">Email</label>
			</div>
	   </div>

		<div class="row">
			<div class="input-field col s12">
				<input id="password" type="password" class="required valid" value="azer1">
				<label class="required" for="password" data-error="Password must be 5 chars length" data-success="Valid password">Password</label>
			</div>
		</div>

		<div class="row">
			<div class="input-field col s12">
				<input id="password_confirm" type="password" class="required valid" value="azer1">
				<label class="required" for="password_confirm" data-error="Password not match" data-success="Password Match">Confirm Password</label>
			</div>
		</div>

		<button id="send" type="submit" class="btn waves-effect waves-light" onclick="createAccount()">Envoyer
			<i class="material-icons right">send</i>
		</button>
	</div>

	<!-- Modal that is shown to User after submitting the form -->
	' . $this->generateSubscribeModal() . '

</div>';
      //return parent::generatePage($content, array('FormConfirm', 'Subscribe'));
	  return parent::generatePage($content, array('FormConfirm', 'Subscription'));
   }

	/**
	* Generates modal view displayed after submit button is clicked on 'Subscribe'
	* Calls generic modal creator 'generateModal'
	* @return $this->generateModal : generated modal
	*/
	private function generateSubscribeModal()
	{
	   return $this->generateModal(
		   array(
			   "200" => "Inscription envoyée",
			   "409" => "Adresse email déjà utilisée"
		   ),
		   array(
			   "200" => "Veuillez vérifier vos mails et cliquer sur le lien qui vous à été envoyé.\nSi vous ne confirmer pas votre inscription dans les 24h la demande sera suprimée, vous devrez alors vous réinscrire.",
			   "409" => "Veuillez consulter vos mails pour vérifier qu\'une inscription que vous avez effectué est en attente. Toute inscription non confirmée dans les 24h sera suprimée, vous pourrez alors vous réinscire."
		   )
	   );
	}


	/**
	* Generates modal view displayed after submit button is clicked on 'Subscribe/[token]'
	* Calls generic modal creator 'generateModal'
	* @return $this->generateModal : generate modal
	*/
	private function generateSubscribeConfirmationModal()
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
	* Generates modal view based on the given error titles and messages
	* @param $titles : array (errorCode => errorTitle)
	* @param $messages array (errorCode => errorMessage)
	* @return $content : modal view generated
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
   * Compute page for subscribtion validation
   * Calls generic page generator 'generatePage'
   * @param $token : Unique token associated with the subscribtion request
   * @return parent::generatePage
   */
   public function getPageSubscribeConfirmation($token)
   {
	   // TODO: Rename JS File (FormConfirm => SubscribeFormValidator)
	   // TODO: Replace default test values with blank and disable button
	   $content = '
<div class="container">
	<div class="row">

		<div class="row">
			<h4>Veuillez renseigner ces informations pour terminer votre inscription</h4>
		</div>

		<div class="row">
			<div class="col validator">
				<input class="indeterminate-checkbox valign-wrapper" id="first_name_status" type="checkbox" tabindex="-1" />
				<label class="empty_label"></label>
			</div>

			<div class="input-field col s6">
		        <input id="first_name" type="text" class="confirmation" placeholder="Saisissez votre prénom" value="Maxime" autofocus>
		        <label for="first_name"><p class="required_flag">*</p> Prénom</label>
	        </div>
		</div>

		<div class="row">
			<div class="col">
				<input type="checkbox" class="indeterminate-checkbox" id="last_name_status" tabindex="-1"/>
				<label class="empty_label"></label>
			</div>

	  		<div class="input-field col s6">
				<input id="last_name" type="text" class="confirmation" placeholder="Saisissez votre nom" value="Dolet">
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
					<option value="" disabled >Choisissez une ville</option>
					<option value="1" selected>Liège</option>
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
	            <input id="age" type="number" class="confirmation" max="100", min="1" value="21" placeholder="Saisissez votre age">
	            <label for="age"><p class="required_flag">*</p> Age</label>
	        </div>
		</div>


		<button id="send" class="btn waves-effect waves-light" type="submit" onclick="confirmSubscription(\''.$token.'\')">Envoyer
			<i class="material-icons right">send</i>
		</button>

		'. $this->generateSubscribeConfirmationModal() .'

	</div>
</div>';
      //return parent::generatePage($content, array('SubscribeConfirmation', 'SubscribeConfirmationValidator'));
	  return parent::generatePage($content, array('Subscription', 'SubscribeConfirmationValidator'));
   }
}
