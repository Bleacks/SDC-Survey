<?php

Namespace Src;

require_once __DIR__ . '/../vendor/autoload.php';

Use \ORM;
Use \DateTime;
Use \DateTimeZone;
Use \DateInterval;

/**
* Class that wraps all the Database interactions related methods
*
* Implements Singleton pattern (use Database::getInstance() to create)
*
* @method __construct()
* @method Database getInstance()
* @method bool subscribeUser($email, $password)
* @method generateToken($tokenLength)
* @method bool confirmUser($pendingSub, $firstName, $lastName, $city, $age)
* @method Object(ORM) getuser($email)
* @method Object(ORM) getPendingSubscription($token)
* @method bool verifySubscriptionToken($subscriptionToken)
* @method DateInterval dateDiffNow($date)
* @method bool verifyConnectionToken($connectionToken)
* @method bool deletePerishedSubscription($pendingSub)
* @method string createConnectionToken($user)
* @method bool deleteConnectionToken($token)
*/
class Database
{
	/** @var $instance:Unique instance of the Connector */
	private static $instance;

	/** Length in days for a connection token to perish if not used */
	const CONNECT_TOKEN_EXPIRATION_LENGTH = 7;

	/**
	* Private constructor of the Database connector
	* Initializes ORM parameters and id names override
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
			'PendingSub'		=> 'idPS',
			'Groups'			=> 'idG',
			'Token'				=> 'idT'
		));
	}

	/**
	* Used to retrieve the unique instance of the connector
	* @return Database Unique instance of Database
	*/
	public static function getInstance ()
	{
		if (is_null(self::$instance))
			self::$instance = new Database();
		return self::$instance;
	}

	/**
	* Creates a base user with informations given and creates a pending subscription for him (valid for 24h)
	* @param string $email Email of the new user
	* @param string $password Password of the new user
	* @return bool True if user's pending subscription is successfully created
	*/
	public function subscribeUser($email, $password)
	{
		// TODO: Crypter le mot de passe dès l'envoi
		$user = ORM::forTable('Users')->create();
		
		$sub = ORM::forTable('PendingSub')->create();
		$sub->idPS = $this->generateToken(10);
		$sub->set_expr('SubscribedAt', 'NOW()');
		$res = $sub->save();

		$user->Email = $email;
		$user->Pass = password_hash($password, PASSWORD_BCRYPT);
		$user->idPS = $sub->idPS;

		return $res && $user->save();
	}

	/**
	* Generates random string of the given length from a 62 chars alphabet
	* @param int $tokenLength Length of the returned random string
	* @return string Generated random string
	*/
	private function generateToken($tokenLength)
	{
		$alphabet = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN0123456789';
		$string = '';
		for ($i = 0; $i < $tokenLength; $i++)
		{
			$string .= $alphabet[rand(0,61)];
	  	}
		return $string;
	}

	/**
	* Confirms Subscription by completing given pendingSub's user with those informations
	* @param Object(ORM) $pendingSub Pending subscription associated to the user
	* @param string $firstName First name of the user
	* @param string $lastName Last name of the user
	* @param int $city City of the user
	* @param int $age Age of the user
	* @return bool True only if subscription is confirmed and pending subscription successfully deleted
	*/
	public function confirmUser($pendingSub, $firstName, $lastName, $city, $age)
	{
		$user = ORM::forTable('Users')->select('idU')->findOne($pendingSub->idU);

		$user->FirstName = $firstName;
		$user->LastName = $lastName;
		$user->City = $city;
		$user->Age = $age;

		$code = $user->save();
		
		if ($code)
		{
			$user->idPS = null;
			$code = $code && $user->save();
			if ($code)
				$code = $code && $pendingSub->delete();
		}

		return $code;
	}

	/**
	* Retrieves the User associated with this email adress from database
	* @param string $email Email of the searched User
	* @return Object(ORM) User associated to the given email
	*/
	public function getUser($email)
	{
		if (!is_null($email))
			$user = ORM::forTable('Users')->where('email', $email)->findOne();
		return $user;
	}

	/**
	* Retrieves the pending subscription associated to the given token
	* @param string $token Pending subscription token
	* @return Object(ORM) Pending Subscription
	*/
	public function getPendingSubscription($token)
	{
		return ORM::forTable('PendingSub')->findOne($token);
	}

	/**
	* Verifies if given subscription token is still valid (i.e. created in the last 24h)
	* @param string $subscriptionToken Subscription token associated to the user's pending subscription
	* @return bool True if given token is still valid
	*/
	public function verifySubscriptionToken($subscriptionToken)
	{
		// FIXME: Rename en verifySubscriptionToken
		/*
		$date = new DateTime($subscriptionToken->SubscribedAt, new DateTimeZone("Europe/Paris"));
		$now = new DateTime("now", new DateTimeZone("Europe/Paris"));
		return $isValid = $now->diff($date)->days == 0;*/
		return $this->dateDiffNow($subscriptionToken->SubscibedAt)->days == 0;
	}

	/**
	* Returns Datetime diff between given now and given date
	* @param DateTime $date Date to compare with php's now
	* @return DateInterval Diff between given date and php'now
	*/
	private function dateDiffNow($date)
	{
		// FIXME: Voir si la méthode fonctionne
		$new_date = new DateTime($date, new DateTimeZone("Europe/Paris"));
		$now = new DateTime("now", new DateTimeZone("Europe/Paris"));
		return $now->diff($new_date, true); // TODO: See if absolute stays at true
	}

	/**
	* Verifies that the given token is still valid (i.e. created or refreshed in less than Database::CONNECT_TOKEN_EXPIRATION_LENGTH (default : 7 days))
	* @param string $connectionToken Token used to authenticate instead of credentials
	* @return bool True only if given token is still valid for this user
	*/
	public function verifyConnectionToken($connectionToken)
	{
		$response = false;
		$connection = ORM::forTable('Token')->findOne($connectionToken);
		// TODO: Voir pour prendre ne compte l'utilisateur aussi pas la suite
		if ($connection != false) // && $connection->idU == $userId)
		{
			if ($this->dateDiffNow($connection->lastUsed)->days > Database::CONNECT_TOKEN_EXPIRATION_LENGTH)
				$connection->delete();
			else
				$response = true;
		}
		return $response;
	}

	/**
	* Deletes the given pending subscription and the base user associated to it
	* @param Object(ORM) $pendingSub Pending subscription token
	* @return bool True if both user and pending subscription are successfully deleted
	*/
	public function deletePerishedSubscription($pendingSub)
	{
		$user = ORM::forTable('Users')->findOne($pendingSub->idU);
		return $pendingSub->delete() && $user->delete();
	}

	/**
	* Creates a connection token for the given user
	* @param Object(ORM) $user User which will be associated to the created token
	* @return string Token generated (false in case of error)
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
	* @param string $token Token to delete
	* @return bool True only if the token is successfully deleted
	*/
	public function deleteConnectionToken($token)
	{
		return ORM::forTable('Token')->findOne($token)->delete();
	}
}
