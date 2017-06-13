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
      // TODO: Ajouter un choix de la langue dans le footer
      // TODO: Ajouter un champ pour uploader une image lors de la confirmation d'inscription
      // TODO: Découper l'inscription en deux étapes comme dans la maquette
      // TODO: Ajouter un validateur de champs pour l'inscription, la connexion et les paramètres
      // TODO: Utiliser Faker pour générer de fausses données à envoyer à la BDD
      // TODO: Regarder pour utiliser Upload pour l'envoi de données
      // TODO: Finir les regex de validation
      // TODO: Ajouter des regex sur les form d'inscription et connexion
   }

	/**
	* Computes content of the page displayed when GET request on /Subscribe
	*/
   public function generatePageGet()
   {
		// TODO: Restore class from valid to validate in release mode and put back disabled button
		// TODO: Ajouter un captcha pour limiter le nombre d'inscriptions
      $content =
'<div class="row">
   <div class="row">
     <div class="input-field col s12">
     <input name="email" id="email" type="email" class="valid tooltipped" value="user@test.fr" autofocus>
       <label for="email" class="tooltipped" data-error="Email not valid (example@sdc.com)" data-success="Email valid">Email</label>
     </div>
   </div>
   <div class="row">
     <div class="input-field col s12">
       <input id="password" type="password" class="valid" value="pwd">
       <label for="password" data-error="Password must be 5 chars length" data-success="Valid password">Password</label>
     </div>
   </div>
   <div class="row">
     <div class="input-field col s12">
       <input id="password_confirm" type="password" class="valid" value="pwd">
       <label for="password_confirm" data-error="Password not match" data-success="Password Match">Confirm Password</label>
     </div>
   </div>
   <button id="send" class="btn waves-effect waves-light" onclick="createAccount()">Submit
      <i class="material-icons right">send</i>
   </button>

	<!-- Modal that is shown to User after submitting the form -->
	<div id="submitted" class="modal modal-fixed-footer">
    <div class="modal-content">
      <h4 id="modal_title"></h4>
		<div class="progress">
			<div id="modal_progression" class="indeterminate"></div>
		</div>
      <p id="modal_message"></p>
    </div>
    <div class="modal-footer">
	 	<a id="btn_footer_close" class="modal-action modal-close waves-effect waves-green btn-flat ">Close</a>
      <a id="btn_footer_mail" target="_blank" href="http://www.gmail.com" class="modal-action modal-close waves-effect waves-green btn-flat ">Open Mail</a>
    </div>
  </div>
</div>';
      return parent::generatePage($content, array('FormConfirm', 'Subscribe'));
   }

   /**
   * Compute page for subscribtion validation
   * @param $token : Unique token associated with the subscribtion request
   */
   public function generatePageValidation($token)
   {
      $content = '
<div class="row">
   <form class="col s12" method="POST" action="Subscribe/'.$token.'">
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
            <input id="last_name" type="text" class="validate">
            <label for="last_name">Last Name</label>
         </div>
      </div>
      <button class="btn waves-effect waves-light" type="submit">Sumbit
         <i class="material-icons right">send</i>
      </button>
   </form>
</div>';
      return parent::generatePage($content);
   }

   // FIXME: Ajouter les informations dans la base de données
   /**
   * Insert information for subscription validation
   * @param (String)$params : data send in POST request
   */
   public function generatePageSubscribeEnd($params)
   {
      $content = 'Votre inscription est désormais finalisée, vous pouvez vous connecter';
      return parent::generatePage($content);
   }
}
