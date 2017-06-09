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

   public function generatePageGet()
   {
      $content =
'<div class="row">
   <div class="row">
     <div class="input-field col s12">
     <input name="email" id="email" type="email" class="validate tooltipped" value="m.m@m.m">
       <label for="email" class="tooltipped">Email</label>
     </div>
   </div>
   <div class="row">
     <div class="input-field col s12">
       <input id="password" type="password" class="validate" value="pwd">
       <label for="password">Password</label>
     </div>
   </div>
   <div class="row">
     <div class="input-field col s12">
       <input id="password_confirm" type="password" class="validate">
       <label for="password_confirm">Confirm Password</label>
     </div>
   </div>
   <script type="text/javascript" src="js/Subscribe.js"></script>
   <button class="btn waves-effect waves-light" onclick="createAccount()">Submit
      <i class="material-icons right">send</i>
   </button>
</div>';
      return parent::generatePage($content);
   }
/*
   public function generatePagePost($params)
   {
      // FIXME: Check if email already exists in database
      // Create unique token
      $token = '';
      $chars = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
      for ($i = 0; $i < 8; $i++)
         $token .= $chars[rand(0,61)];

      // Initialize mail parameters
      $email = $params['email'];
      $subject = '[SDC-Survey] Please confirm your registration';
      $message = 'Please confirm your registration by clicking on this link :\nlocalhost/bleacks/SDC-Survey/Subscribe/'. //\Slim\App::getInstance()->getName()
      $token. '\nThis link will be active for the next 24h, it will then be automatically deleted from our servers.\nRegards';

      //mail($email, $subject, $message);
      var_dump($token);
      // TODO: Ajouter les données dans la base et créer un trigger qui les supprime sous 24h
      // TODO: Voir pour ajouter container + dependencies injection pour acceder à Slim et avoir le nom du site de manière dynamique

      $content = 'Un mail vient de vous être envoyé à l\'adresse : ' . $email;
      return parent::generatePage($content);
   }
*/
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
