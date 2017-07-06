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
			'Groups'			=> 'idG',
			'Token'				=> 'idT'
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
		$user->Pass = password_hash($password, PASSWORD_BCRYPT);
		$res = $user->save();

		$sub = ORM::forTable('PendingSub')->create();

		$sub->Token = $this->generateToken(10);
		$sub->idU = $user->id();
		$sub->set_expr('SubscribedAt', 'NOW()');

		return $res && $sub->save();
	}

	/**
	* Generates random string of the given length from a 62 chars alphabet
	* @param $stringLength Length of the returned random string
	* @return $string Random string
	*/
	private function generateToken($stringLength)
	{
		$alphabet = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
		$string = '';
		for ($i = 0; $i < $stringLength; $i++)
		{
			$string .= $alphabet[rand(0,61)];
	  	}
		return $string;
	}

	/**
	* Confirms Subscription by completing given pendingSub's user with given info
	* @param $pendingSub Pending subscription
	* @param $firstName First name of the user associated with the
	* @param $lastName Last name of the user
	* @param $city City of the user
	* @param $age
	*/
	public function confirmUser($pendingSub, $FirstName, $lastName, $city, $age)
	{
		$user = ORM::forTable('Users')->select('idU')->findOne($pendingSub->idU);

		$user->FirstName = $FirstName;
		$user->LastName = $lastName;
		$user->City = $city;
		$user->Age = $age;

		$code = $user->save();
		if ($code)
			$code = $code; //&& $pendingSub->delete();
		return $code;
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
	* @param $subscriptionToken Subscription token associated to the user
	* @return $isValid True if given token is still valid
	*/
	// FIXME: Rename en verifySubscriptionToken
	public function verifyToken($subscriptionToken)
	{
		$date = new DateTime($subscriptionToken->SubscribedAt, new DateTimeZone("Europe/Paris"));
		$now = new DateTime("now", new DateTimeZone("Europe/Paris"));
		return $isValid = $now->diff($date)->days == 0;
	}

	/**
	* Deletes the given pending subscription and the user's info that are associated to it
	* @param $pendingSub Pending subscription's token
	* @return delete() True if row are successfully deleted
	*/
	public function deletePerishedSubscription($pendingSub)
	{
		$user = ORM::forTable('Users')->findOne($pendingSub->idU);
		return $pendingSub->delete() && $user->delete();
	}

	/**
	* Verifies that the given token is still valid (i.e. created or refreshed in the last 7 days)
	* @param $connectionToken Token used to authenticate instead of credentials
	* @return $connection True if the given token is still valid
	*/
	public function verifyConnectionToken($connectionToken, $userId)
	{
		$connection = ORM::forTable('Token')->where('idU', $userId)->findOne();
		return $connection != false;
	}

	/**
	* Creates a connection token for the given user
	* @param $user User associated to the created token
	* @return $token Token generated (false in case of error)
	*/
	public function createConnectionToken($user)
	{
		// TODO: Chiffrement de l'email avec sel
		$token = $this->generateToken(25);
		// TODO: Factoriser la création du sel avec le token d'inscription
		// Ajout du token à la BDD
		$connectionToken = ORM::forTable('Token')->create();
		$connectionToken->idT = $token;
		$connectionToken->idU = $user->idU;
		$connectionToken->set_expr('lastUsed', 'NOW()');

		return ($connectionToken->save()) ? $token : false;
	}

	/**
	* Deletes given connection token
	* @param $token Token to delete
	* @return delete()
	*/
	public function deleteConnectionToken($token)
	{
		return ORM::forTable('Token')->findOne($token)->delete();
	}
}
