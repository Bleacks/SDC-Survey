<?php

Namespace Src;

require_once __DIR__ . '/../vendor/autoload.php';

Use \ORM;

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
      //ORM::configure('mysql:./sdc');
      ORM::configure(array(
         'connection_string' => "mysql:host=localhost;dbname=sdc",
         'username' => 'sdc-user',
         'password' => 'sdc-test'
      ));
   }

   /**
   * Used to retrieve the unique instance of the connector
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
   */
   public function subscribeUser($email, $password)
   {
      // TODO: Crypter le mot de passe dès l'envoi
      $user = ORM::forTable('Users')->create();

      $user->Email = $email;
      $user->Pass = $password;
      $user->set_expr('SubDate', 'NOW()');

      return $user->save();
   }

   /**
   * Retrieves the User associated with this email adress
   * @param $email Email of the searched User
   */
   public function getUser($email)
   {
      if (!is_null($email))
         $user = ORM::forTable('Users')->where('email', $email)->findOne();
      return $user;
   }
}
