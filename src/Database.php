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
			'Users'				=> 'idU',
			'Question'			=> 'idQ',
			'Answer'			=> 'idA',
			'AnsweredSurvey'	=> array('idS', 'idU'),
			'OtherSurvey'		=> array('idQ', 'idU'),
			'Users'				=> 'idU',
			'PendingSub'		=> 'Token',
			'Groups'			=> 'idG'
		));
	}

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
	* Confirms Subscription by completing given pendingSub's user with given info
	*/
	public function confirmUser($pendingSub, $first_name, $last_name, $city, $age)
	{
		$user = ORM::forTable('Users')->select('idU')->findOne($pendingSub->idU);

		$user->FirstName = $first_name;
		$user->LastName = $last_name;
		$user->City = $city;
		$user->Age = $age;

		return $user->save() && $pendingSub->delete();
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
	* Retrieves the pending subscription associated to the given token
	* @param $token
	* @return ORM::forTable('PendingSub')
	*/
	public function getPendingSubscription($token)
	{
		return ORM::forTable('PendingSub')->findOne($token);
	}

	/**
	* Verifies if given token is still valid (i.e. created in the last 24h)
	* @param $token Subscription token associated to the user
	* @return $isValid True if given token is still valid
	*/
	public function verifyToken($pendingSub)
	{
		$date = new DateTime($pendingSub->SubscribedAt, new DateTimeZone("Europe/Paris"));
		$now = new DateTime("now", new DateTimeZone("Europe/Paris"));
		return $isValid = $now->diff($date)->days == 0;
	}

	/**
	* Deletes the given pending subscription and the user's info that are associated to it
	*/
	public function deletePerishedSubscription($pendingSub)
	{
		$user = ORM::forTable('Users')->findOne($pendingSub->idU);
		return $user->delete() && $pendingSub->delete();
	}
}
