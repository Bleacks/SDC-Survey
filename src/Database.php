<?php

Namespace Src;

require_once __DIR__ . '/../vendor/autoload.php';

Use \ORM;
Use \DateTime;
Use \DateTimeZone;

/**
* Class that interacts with the Database (Singleton Pattern)
*/
class Database
{
   /** @var $instance:Unique instance of the Connector */
   private static $instance;

   /**
   * Private constructor of the connector
   */
   private function __construct ()
   {
      // Base configuration of idiorm
      ORM::configure(array(
         'connection_string' => "mysql:host=localhost;dbname=sdc",
         'username' => 'sdc-user',
         'password' => 'sdc-test'
      ));
	  // Overrides id for any table that has PK different from 'id'
	  ORM::configure('id_column_overrides', array(
		  'Users'			=> 'idS',
		  'Question'		=> 'idQ',
		  'Answer'			=> 'idA',
		  'AnsweredSurvey'	=> array('idS', 'idU'),
		  'OtherSurvey'		=> array('idQ', 'idU'),
		  'Users'			=> 'idU',
		  'PendingSub'		=> 'Token',
		  'Groups'			=> 'idG'
	  ));
   }

   // TODO: Faire des index pour accélérer le traitement des données

   /**
   * Used to retrieve the unique instance of the connector
   * @return self::$instance Unique instance of Database
   */
   public static function getInstance ()
   {
      if (is_null(self::$instance))
         self::$instance = new Database();
      return self::$instance;
   }

   /**
   * Insert a new User in the Database
   * @param $email Email of the new user
   * @param $password Password of the new user
   * @return $user->save() Results of the insert function
   */
   public function subscribeUser($email, $password)
   {
      // TODO: Crypter le mot de passe dès l'envoi
      $user = ORM::forTable('Users')->create();

      $user->Email = $email;
      $user->Pass = $password;
	  $res = $user->save();

	  $sub = ORM::forTable('PendingSub')->create();

	  $alphabet = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
	  $token = '';
	  for ($i = 0; $i < 10; $i++)
	  {
		  $token .= $alphabet[rand(0,61)];
	  }
	  $sub->Token = $token;
	  $sub->idU = $user->id();
	  $sub->set_expr('SubscribedAt', 'NOW()');

      return $res && $sub->save();
   }

   /**
   * Retrieves the User associated with this email adress
   * @param $email Email of the searched User
   * @return $user User associated to the given email
   */
   public function getUser($email)
   {
      if (!is_null($email))
         $user = ORM::forTable('Users')->where('email', $email)->findOne();
      return $user;
   }

	/**
	* Verifies if given token is still valid (i.e. created in the last 24h)
	* Deletes the token if it's perished (i.e. created more than 24h ago)
	* @param $token Subscription token associated to the user
	* @return $isValid True if given token is still valid
	*/
   public function verifyToken($token)
   {
	   	$sub = ORM::forTable('PendingSub')->where('Token', $token)->findOne();
	   	$date = new DateTime($sub->SubscribedAt, new DateTimeZone("Europe/Paris"));
	   	$now = new DateTime("now", new DateTimeZone("Europe/Paris"));
		$isValid = $now->diff($date)->days == 0 && $sub != false;

	   	if ($isValid)
	   	{
			$user = ORM::forTable('Users')->findOne($sub->idU);
	   		$sub->delete();
			$user->delete();
	   	}
	   	return $isValid;
   }
}
