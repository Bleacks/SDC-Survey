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
	<button id="send" type="submit" class="btn waves-effect waves-light" onclick="createAccount()">Submit
		<i class="material-icons right">send</i>
	</button>

	</div>

	<!-- Modal that is shown to User after submitting the form -->
	<div id="submitted" class="modal modal-fixed-footer">
    <div class="modal-content">

	 	<!-- Different titles following type of http response -->
		<h4 id="modal_title_200" hidden>Inscription envoyée</h4>
		<h4 id="modal_title_409" hidden>Adresse email déjà utilisée</h4>
		<h4 id="modal_title_424" hidden>Une méthode de la transaction a échoué</h4>
		<h4 id="modal_title_429" hidden>Trop de requêtes ont étés effectuées depuis votre adresse</h4>
		<h4 id="modal_title" hidden></h4>

		<div class="progress">
			<div id="modal_progression" class="indeterminate"></div>
		</div>

		<!-- Different messages following type of http response -->
      <p id="modal_message_err"></p>
		<p id="modal_message_200" hidden>Veuillez vérifier vos mails et cliquer sur le lien qui vous à été envoyé.\nSi vous ne confirmer pas votre inscription dans les 24h la demande sera suprimée, vous devrez alors vous réinscrire.</p>
		<p id="modal_message_409" hidden>Veuillez consulter vos mails pour vérifier qu\'une inscription que vous avez effectué est en attente. Toute inscription non confirmée dans les 24h sera suprimée, vous pourrez alors vous réinscire.</p>
		<p id="modal_message_424" hidden>Quelque chose s\'est mal passé lors de la création du compte, veuillez réessayer plus tard. Si le problème persiste, contactez l\administrateur.</p>
		<p id="modal_message_429" hidden>Veuillez patienter quelques instants avant de recommencer.</p>
    </div>

    <div class="modal-footer">
	 	<a id="btn_footer_close" class="modal-action modal-close waves-effect waves-green btn-flat ">Close</a>
      <a id="btn_footer_mail" target="_top" href="http://www.gmail.com" class="modal-action modal-close waves-effect waves-green btn-flat ">Open Mail</a>
    </div>
  </div>

</div>';
      return parent::generatePage($content, array('FormConfirm', 'Subscribe'));
   }

   // FIXME: Ajouter les informations dans la base de données
   /**
   * Compute page for subscribtion validation
   * @param $token : Unique token associated with the subscribtion request
   */
   public function getPageSubscribeConfirmation($token)
   {
	   // TODO: Rename JS File (FormConfirm => SubscribeFormValidator)
	   $scripts = array('SubscribeConfirmation', 'SubscribeConfirmationValidator');
	   $content = '
<div class="row">

      <div class="row">
         <div class="input-field col s6">
            <input id="first_name" type="text" class="validate">
            <label for="first_name">First Name</label>
         </div>
      </div>

      <div class="row">
         <div class="input-field col s6">
            <input id="last_name" type="text" class="validate">
            <label for="last_name">Last Name</label>
         </div>
      </div>

      <div class="row">
         <div class="input-field col s6">
            <input id="ville" type="text" class="validate">
            <label for="ville">Ville</label>
         </div>
      </div>

	  <div class="row">
         <div class="input-field col s6">
            <input id="Age" type="number" max="100" min="0" class="validate">
            <label for="ville" data-error="Veuillez entrer un age correct">Age</label>
         </div>
      </div>

      <button class="btn waves-effect waves-light" type="submit" onclick="confirmSubscription(token)">Sumbit
         <i class="material-icons right">send</i>
      </button>

</div>';
      return parent::generatePage($content, $scripts);
   }

   /**
   * Insert information for subscription validation
   * @param (String)$params : data send in POST request
   */
   public function getPagePerishedConfirmation($token)
   {
      $content = 'Ce lien a expiré, votre inscription à eu lieu il y a plus de 24h, vous devez recommencer';
      return parent::generatePage($content);
   }
}
