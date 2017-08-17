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

	/** Informations about fields length in the Database */
    const FIELDS_LENGTH = array(
        'GenericSurvey'         => array(
            'Titre'         => 100,
            'Description'   => 100,
            'More'          => 250
        ),
        'GenericQuestion'       => array(
            'Text'          => 100
        ),
        'GenericAnswer'         => array(
            'Text'          => 50
        ),
        'Survey'                => array(
            'Document'      => 200
        ),
        'Users'                 => array(
            'FirstName'     => 40,
            'LastName'      => 40,
            'Email'         => 40,
            'Pass'          => 60,
            'City'          => 40
        )
    );

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
			'Groups'			=> 'idG',
			'Recovery'			=> 'code',
			'Token'				=> 'idT',
			'Answer'			=> array('idGQ', 'idS', 'idGA'),
			'Survey'			=> 'idS',
			'Iteration'			=> 'idIT',
			'PendingSub'		=> 'idPS',
			'GenericSurvey'		=> 'idGS',
			'GenericQuestion'	=> 'idGQ',
			'GenericAnswer'		=> 'idGA'
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
		$user->Email = $email;
		$user->Pass = password_hash($password, PASSWORD_BCRYPT);
		$res = $user->save();

		if ($res)
		{
			$sub = ORM::forTable('PendingSub')->create();
			$sub->idPS = $this->generateToken(10);
			$sub->idU = $user->id();
			$sub->set_expr('SubscribedAt', 'NOW()');
			$res = $sub->save();
		}

		return $res;
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
			$string .= $alphabet[mt_rand(0,61)];
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
			$code = $pendingSub->delete();

		return $code;
	}

	/**
	* Retrieves the User associated with this email adress from database
	* @param string $email Email of the searched User
	* @return Object(ORM) User associated to the given email
	*/
	public function getUser($email)
	{
		$user = false;
		if (!is_null($email))
			$user = ORM::forTable('Users')->where('email', $email)->findOne();
		return $user;
	}

	/**
	* Indicates if the User associated to this email is an admin or not
	* @param string $email Email of the User
	* @return bool True only if User is an admin
	*/
	public function isAdmin($email)
	{
		return $this->getUser($email)->Admin == 1;
	}

	/**
	* Retrieves all the groups
	* @return Object(ORM) All groups
	*/
	public function getGroups()
	{
		return ORM::forTable('Groups')->findMany();
	}

	/**
	* Updates the code of the recovery associated to the given email adress
	* @param string $email Email adress of the user
	* @return bool True if the update is successfull
	*/
	function updateCode($recovery_email)
	{
		$recovery_demand = ORM::forTable('Recovery')->findOne($recovery_email);
		if ($recovery_demand == false)
		{
			$recovery_demand = ORM::forTable('Recovery')->create();
			$recovery_demand->Email = $recovery_email;
		}
		$recovery_demand->set_expr('GeneratedAt', 'NOW()');
		$recovery_demand->Code = $this->generateToken(10);

		return $recovery_demand->save() ? $recovery_demand->Code : false;
	}

	/**
	* Delete in Recovery table the row associate to the given code
	* @param varchar $code generate code for the recovery password
	* @return bool True if the delete is successfull
	*/
	public function verifyRecoveryCode($code){
		$res = false;
		$recovery = ORM::forTable('Recovery')->findOne($code);
		if ($recovery != false)
		{
			$res = $this->dateDiffNow($recovery->generatedAt)->days == 0;
			if (!$res)
			{
				$recovery->delete();
			}
		}
		return $res;
	}

	/**
	* Update the database with the new password
	* @param varchar $code generate code for the recovery password
	* @param varchar $password Password of the user
	* @return bool True if the password was successfully update
	*/
	public function updatePassword($code, $password)
	{
		$recovery = ORM::forTable('Recovery')->findOne($code);
		$user = ORM::forTable('Users')->findOne($recovery->idU);
		$password = password_hash($password, PASSWORD_BCRYPT);
		$user->Pass = $password;
		return $user->save();
	}

	/**
	* Delete from Recovery table the row associate to the given code
	* @param varchar $code generate code for the recovery password
	* @return True if the delete is successfull
	*/
	public function deleteRecovery($code)
	{
		$del_rec = ORM::forTable('Recovery')->where ('code',$code)->findOne();
		return $del_rec->delete();
	}

	// A REVOIRRRRRRR
	public function getGeneratedDateRecovery($email)
	{
		return ORM::forTable('Recovery')->findOne($email);
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

	/**
	* Retrieves all possible answers for a given question
	* @param int $questionId Id of the genericQuestion
	* @return Object(ORM) List of possible answers to this question
	*/
	public function getAllAnswers($questionId)
	{
		return ORM::forTable('GenericAnswer')->where('idGQ', $questionId)->findMany();
	}

	/**
	* Retrieves the Generic Survey associated to the given idGS
	* @param $idGS int Database id of the GenericSurvey
	* @return Object(ORM) GenericSurvey
	*/
	public function findGenericSurvey($idGS)
	{
		return ORM::forTable('GenericSurvey')->findOne($idGS);
	}

	/**
	* Retrieves the GenericQuestion associated to the given idGQ
	* @param $idGQ int Database id of the GenericQuestion
	* @return Object(ORM) GenericQuestion
	*/
	public function findGenericQuestion($idGQ)
	{
		return ORM::forTable('GenericQuestion')->findOne($idGQ);
	}

	/**
	* Retrieves the current iteration for the given idGS
	* @param $idGS int Database id of the GenericSurvey
	* @return Object(ORM) current iteration for the given survey
	*/
	public function findCurrentIteration($genericSurvey)
	{
		return ORM::forTable('Iteration')->where('idGS', $genericSurvey->idGS)
		->having_raw('DATEDIFF(NOW(), Iteration.BeginAt) < ?', array($genericSurvey->Lifespan))
		->findOne();
	}

	/**
	* Create a new answer associated to the given Survey, GenericQuestion and GenericAnswer
	* @param $idGQ int Database id of GenericQuestion
	* @param $idS int Database id of Survey
	* @param $idGA int Database id of GenericAnswer
	*/
	public function createAnswer($idGQ, $idS, $idGA)
	{
		$answer = ORM::forTable('Answers')->create();
		$answer->idGQ = $idGQ;
		$answer->idS = $idS;
		$answer->idGA = $idGA;
		return $answer->save();
	}

	/**
	* Creates (or find) a GenericAnswer with the given text associated to the given GenericQuestion
	* @param $idGQ int Database id of GenericQuestion
	* @param $text string Displayed answer in the form
	* @return Object(ORM) GenericAnswer created or found
	*/
	public function createGenericAnswer($idGQ, $text)
	{
		$genericAnswer = ORM::forTable('GenericAnswer')->where('idGQ', $idGQ)->where('Text', $text)->findOne();
		if (!$genericAnswer)
		{
			$genericAnswer = ORM::forTable('GenericAnswer')->create();
			$genericAnswer->idGQ = $idGQ;
			$genericAnswer->Text = $text;
			$genericAnswer->save();
		}
		return $genericAnswer;
	}

	/**
	* Retrieves a GenericAnswer using the given text and type
	* @param $answer Object(ORM) GenericAnswer idGA or Text
	* @param $question int idGQ of the associated GenericQuestion
	* @return Object(ORM) GenericAnswer with the given parameters
	*/
	public function findGenericAnswer($answer, $question = 0)
	{
		$genericAnswer = false;
		if (intval($answer))
		{
			// TODO: Ajouter filtre question
			$genericAnswer = ORM::forTable('GenericAnswer')->findOne($answer);
		} else
		{
			if ($question != 0)
				$genericAnswer = ORM::forTable('GenericAnswer')->where('idGQ', $question)->where('Text', $answer)->findOne();
			else
				$genericAnswer = ORM::forTable('GenericAnswer')->where('Text', $answer)->findOne();
		}
		return $genericAnswer;
	}

	/**
	* Creates an empty survey and initializes his id
	* @return Object(ORM) Survey created
	*/
	public function createEmptySurvey()
	{
		$survey = ORM::forTable('Survey')->create();
		$survey->save();
		return $survey;
	}

	/**
	* Updates the given Survey with the given informations
	* @param $survey Object(ORM) Survey we want to update
	* @param $document string Document field of the Survey
	* @param $idU int Database id of the associated user
	* @param $idIT int Database id of the associated Iteration
	* @return bool True if insert is successfull
	*/
	public function submitSurvey($survey, $document, $idU, $idIT)
	{
		$survey->set_expr('FinishedAt', 'NOW()');
		$survey->idU = $idU;
		$survey->idIT = $idIT;
		$survey->document = $document;
		return $survey->save();
	}
}
