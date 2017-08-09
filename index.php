<?php

/* HTTP codes sent by this platform

200 : Success

401 : Unauthorized (unauthenticated)
403 : Access denied
404 : Not found
405 : Method not allowed
409 : Conflict
422 : Unprocessable entry
424 : Method failure
429 : Too many request

500 : Internal server error
*/


# Usefull $_SERVER indexes
  # REQUEST_METHOD : GET,POST,PUT,DELETE
  # PATH_INFO      : Path requested in URL (URI)

//var_dump($_SERVER);


// TODO: Ajouter un champ Licence dans le composer.json
require_once "vendor/autoload.php";

use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Main;
use Src\Subscribe;
use Src\Database;
use Src\Connect;
use Src\PasswordRecovery;
use Src\Email;
use Src\Survey;
use Src\Profile;
//use Src\MailManager;

$app = new App([
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true
    ]
]);

// TODO: Ajouter explicitement le contenu généré à $response avant de la return

session_start();

$app->add(function(ServerRequestInterface $request, ResponseInterface $response, Callable $next) {
        $url = $request->getUri()->getPath();
        $publicPaths = array('Connect', 'Subscribe', 'Recovery', 'Kill');
        // TODO: Retirer Test des URI autorisées
        $privatePaths = array('', 'Demo', 'Surveys', 'Profile', 'Reset', 'Disconnect', 'ChangePassword', 'Test');
        $path = 'Connect';
        $code = 404;

		if (strpos($url, '/') !== false)
			$url = explode('/', $url)[0];

		if (in_array($url, $privatePaths))
		{
            if (isset($_SESSION['token']))
            {
                $db = Database::getInstance();
                if ($db->verifyConnectionToken($_SESSION['token']))
                {
                    return $next($request, $response);
                } else
                {
                    unset($_SESSION['token']);
                }
            }

            $_SESSION['url'] = $url;
            $code = 403;
		} else
        {
            // TODO: Voir pour changer par une page not found générique intégrée au site
            if (in_array($url, $publicPaths))
                return $next($request, $response);
            else
                $path = $request->getUri()->getBasePath();
        }
        return $response->withRedirect($path, $code);
});

// NOTE: Main ne doit être utilisée que par les classes spécifiques, vers lesquelles Slim redirige



$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response)
{

	/*$res = $response->withJson(var_dump('mot de passe'));
	$hash = password_hash('mot de passe', PASSWORD_BCRYPT);
	$res = $res->withJson(var_dump($hash));
	$res = $res->withJson(var_dump(password_verify('mot de passe', $hash)));*/

	// FIXME: Write test for the dateDiffNow function
	//return Main::workInProgressPage();
});


$app->get('/Demo', function (ServerRequestInterface $request, ResponseInterface $response)
{
   $main = new Main();
   return $main->generateDemo();
});



$app->get('/Profile', function (ServerRequestInterface $request, ResponseInterface $response)
{
    $db = Database::getInstance();
    $profile = new Profile();

    $user = $db->getUser($_SESSION['email']);
    $groups = $db->getGroups();

    $response = $profile->getPageProfile($user, $groups);
    return $response;
});

$app->put('/Profile', function (ServerRequestInterface $request, ResponseInterface $response)
{

});




$app->get('/Connect', function (ServerRequestInterface $request, ResponseInterface $response)
{
    $con = new Connect();
	if (isset($_SESSION['token']))
	{
		return $response->withRedirect('Home');
	}
	else
	{
		return $con->getPageConnect();
	}
})->setName('auth');


$app->post('/Connect', function (ServerRequestInterface $request, ResponseInterface $response)
{
	$post = $request->getParsedBody();
	$db = Database::getInstance();
	$res = $response->withStatus(424);

	if (isset($post['email']) && isset($post['password']) && $post['email'] != '' && $post['password'] != '')
	{
		$user = $db->getUser($post['email']);
		$err = array('error' => "Email or password not valid");
		if ($user != null)
		{
			// TODO: Faire changer le statut de l'utilisateur à en ligne : $db->changeStatut();
			if (password_verify($post['password'], $user->Pass))
			{
				if ($post['remember']) // Saves authentication's informations in a cookie
				{
					$token = $db->createConnectionToken($user);
					if ($token != false)
						$_SESSION['token'] = $token;
				}
				else // Deletes eventual existing cookie
				{
					$db->deleteConnectionToken($_SESSION['token']);
					unset($_SESSION['token']);
				}
				$res = $response->withStatus(200);
				$_SESSION['email'] = $user->Email;
			}
			else
			{
				$res = $response->withJson($err, 403);
			}
		}
		else
			$res = $response->withJson($err, 422);

		if (isset($_SESSION['url']) && $_SESSION['url'] != '')
		{
			$redir = array('url' => $_SESSION['url']);
			unset($_SESSION['url']);
			$res = $res->withJson($redir);
		}
	}
	return $res;
});


$app->get('/Recovery',function(ServerRequestInterface $request, ResponseInterface $response)
{
	$recoveryPw = new PasswordRecovery();
	$response = $recoveryPw->getPageFormRecoveryEmail();
	return $response;

});


$app->post('/Recovery', function (ServerRequestInterface $request, ResponseInterface $response)
{
	$post = $request->getParsedBody();
	$db = Database::getInstance();
	$err = array('error' => "Email do not exist");
	//$res = $response->withStatus(424);      // TODO : faire erreur!!!!!

	if ($post['recovery_email'] && !empty($post['recovery_email']))
	{
		$email = $post['recovery_email'];
		$recovery_email = htmlspecialchars($email) ;
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
		{
			$user = $db->getUser($email);
			// TODO: TESTER
			if($user != false)
			{
				$firstName = $user->firstName;
				$lastName = $user->lastName;
				// to avoid duplicate data
				$recovery_code = $db->updateCode($email);

				if ($recovery_code != false)
				{
				$res = $response->withStatus(200);
		//mail($recovery_email, "Récupération de mot de passe", $message, $header);
				}
				//$email = new Email();
				//$email->sendMail($firstName, $lastName);

			//$res = $response->withJson($err, 424);
			}
			else
			{
				$res = $response->withJson($err, 424);
			}
		}

				//$res = $response->withJson(var_dump($user),424);
	}
	return $res;
});


$app->get('/Recovery/{code}', function(ServerRequestInterface $request, ResponseInterface $response, $args)
{
	$db = Database::getInstance();
	$code = $args['code'];
	$page = '';

	// Ajouter une validation du code (voir verification du token de /Subscribe)
	if ($db->verifyRecoveryCode($code))
	{
		$recoveryNewPassword = new PasswordRecovery();
		$page = $recoveryNewPassword->getPageFormRecoveryPw($code);
	}
	else
	{
		//$res = $response->withJson($err, 403);
		$main = new Main();
		$page = $main->generateDefaultErrorPage("Votre code a expiré ou n'est pas valide, merci de bien vouloir soumettre votre demande de reinitialisation de mot de passe.");
	}
	return $page;
});


$app->post('/Recovery/{code}', function(ServerRequestInterface $request, ResponseInterface $response,$args)
{
	$post = $request->getParsedBody();
	$db = Database::getInstance();
	$code = $args['code'];
	$err = array('error' => "");

	if(isset($post['change_pw']) && isset ($post['change_pwc']) && $post['change_pw'] != '' && $post['change_pwc'] != '')
	{
		$change_pw = $post['change_pw'];
		$change_pwc = $post['change_pwc'];

		// $res = $response->withJson(var_dump($post));
		$pw = htmlspecialchars($change_pw);
		$pwc = htmlspecialchars($change_pwc);

		$up_pass = $db->updatePassword($code,$pw);
		$del_recovery = $db->deleteRecovery($code);

		if ($up_pass != false && $del_recovery != false)
		{
			$res = $response->withStatus(200);
		}
		else
		{
			$res = $response->withJson($err, 424);
		}
	}
	else
	{
		$res = $response->withJson($err, 409);
	}
	return $res;
});

$app->get('/ChangePassword',function(ServerRequestInterface $request, ResponseInterface $response)
{
	$recoveryPw = new PasswordRecovery();
	$response = $recoveryPw->getFormChangePassword();
	return $response;

});

$app->post('/ChangePassword', function(ServerRequestInterface $request, ResponseInterface $response)
{
	$post = $request->getParsedBody();
	$db = Database::getInstance();
	$err = array('error' => "");
	$res = $response->withStatus(424);

	if (isset($post['old_pw']))
	{
		//$old_pw = htmlspecialchars($post['old_pw']);


		$old_pw = substr($post['old_pw'], 0, 60);

		$email_user = $_SESSION['email'];

		$user = $db->getUser($email_user);
		//$bite = $user->Pass;

		/*$choses = array(
			password_verify($old_pw, substr(settype($bite, 'string'), 0, 60)),
			password_verify('azer1', password_hash($old_pw, PASSWORD_BCRYPT))
		);*/
		//$res = $response->withJson(var_dump(password_verify($old_pw, '$2y$10$g2Wk.XsyOvxXKzKrmzVYwefOFbuiX3GNP3pHq0D23SjrWlaNqUgNa')));


		if (password_verify($old_pw, $user->Pass))
		{
			if(isset($post['change_pw']) && isset ($post['change_pwc']) && $post['change_pw'] != '' && $post['change_pwc'] != '')
			{
				$change_pw = $post['change_pw'];
				$change_pwc = $post['change_pwc'];

				/*$blabla = "je suis la";
				$res = $response->withJson(var_dump($blabla));*/

				// $res = $response->withJson(var_dump($post));
				$pw = htmlspecialchars($change_pw);
				$pwc = htmlspecialchars($change_pwc);

				$up_pass = $db->updatePassword($code,$pw);
				$del_recovery = $db->deleteRecovery($code);

				if ($up_pass != false && $del_recovery != false)
				{
					$res = $response->withStatus(200);
				}
				else
				{
					$res = $response->withJson($err, 424);
				}
			}
			else
			{
				$res = $response->withJson($err, 409);
			}
		}
		//$res = $response->withJson(var_dump($choses));
	}
	return $res;

});

$app->get('/Messages', function (ServerRequestInterface $request, ResponseInterface $response)
{
	//MailManager::testMailer();
	return Main::workInProgressPage();
});



$app->get('/Kill', function (ServerRequestInterface $request, ResponseInterface $response)
{
    session_destroy();
    session_unset();
});



$app->get('/Test', function (ServerRequestInterface $request, ResponseInterface $response)
{
    $switch = 'ui';
    $val = 'ui';
    switch ($switch)
    {
        case 1:
            break;
        case $val:
            var_dump('Les switch avec "case $val:" fonctionnent');
            break;
        default:
            break;
    }
    var_dump(password_hash('azer1', PASSWORD_BCRYPT));
    var_dump($_SESSION);
    $user = Database::getInstance()->getUser($_SESSION['email']);
    $values = array();
    $mdp = 'azer2';
    $values['Vérfication mot de passe'] = password_verify('azer1', '$2y$10$8VysYMpTUn/pDBHELLB5EuJZWCWdbfQYc5Qx9/maqGb.eF7N0BtPC');
    $values['Mot de passe de départ'] = $user->Pass;
    $values['Nouveau mot de passe'] = $mdp;
    $user->Pass = password_hash($mdp, PASSWORD_BCRYPT);
    $values['Nouveau hash'] = $user->Pass;
    $user->save();
    $user = Database::getInstance()->getUser($_SESSION['email']);
    $values['Nouveau hash lu'] = $user->Pass;
    $values['Nouvelle verification'] = password_verify('azer2', $user->Pass);
    var_dump($values);
});



$app->get('/Subscribe', function (ServerRequestInterface $request, ResponseInterface $response)
{
   $subscribe = new Subscribe();
   return $subscribe->getPageSubscribe();
});



$app->post('/Subscribe', function(ServerRequestInterface $request, ResponseInterface $response)
{
	sleep(2);
	$db = Database::getInstance();
   	$post = $request->getParsedBody();
   	if (isset($post['email']) && isset($post['password']) && !is_null($post['email']) && !is_null($post['password'])) {
      	if ($db->getUser($post['email']) == false)
      	{
         	if ($db->subscribeUser($post['email'], $post['password']))
            	$res = $response->withStatus(200);
         	else
            	$res = $response->withStatus(424);
      	} else
         	$res = $response->withStatus(409);
   	}
   	return $res;
});



$app->get('/Subscribe/{token}', function (ServerRequestInterface $request, ResponseInterface $response, $args)
{
	// TODO: Ajouter une modale avec crécupéreation des données du serveur et en cas de non validité afficher l'erreur dedans, la fermer sinon et permettre l'inscription
	// TODO: Ajouter une suppression des tokens on register confirm

	$token = $args['token'];

	if (preg_match('/^[a-zA-Z0-9]{10}$/i', $token))
	{
		$subscribe = new Subscribe();
		$db = Database::getInstance();

		$pendingSub = $db->getPendingSubscription($token);
		if ($db->verifySubscriptionToken($pendingSub))
		{
			$res = $subscribe->getPageSubscribeConfirmation($token);
		} else {
			$res = $response->withStatus(422);
			$db->deletePerishedSubscription($pendingSub);
		}
	} else {
		$res = $response->withStatus(422);
	}

    return $res;
});



$app->post('/Subscribe/{token}', function (ServerRequestInterface $request, ResponseInterface $response, $args)
{
	$token = $args['token'];
	$res = $response->withStatus(422);

	if (preg_match('/^[a-zA-Z0-9]{10}$/i', $token))
	{
		$subscribe = new Subscribe();
		$db = Database::getInstance();
		$pendingSub = $db->getPendingSubscription($token);
		if ($pendingSub != false)
		{
			$post = $request->getParsedBody();

			if ($db->verifySubscriptionToken($pendingSub))
			{
				if (isset($post['first_name']) && isset($post['last_name']) &&
					isset($post['city']) && isset($post['age']))
				{
					if ($db->confirmUser($pendingSub, $post['first_name'], $post['last_name'], $post['city'], $post['age']))
						$res = $response->withStatus(200);
					else
						$res = $response->withStatus(424);
				}
			}
		} else
			$res = $response->withStatus(424);
	}
    return $res;
});



$app->get('/Surveys', function (ServerRequestInterface $request, ResponseInterface $response)
{
    $survey = new Survey();
    // FIXME: Changer en id, et ajouter l'id dans la globale SESSION
    return $survey->getSurveyMenu($_SESSION['email']);
})->setName('Surveys');



$app->get('/Surveys/{survey}', function (ServerRequestInterface $request, ResponseInterface $response, $args)
{
    $survey = new Survey();
    $survey->submitSurvey($args['survey']);
    $uri = $request->getUri()->withPath($this->router->pathFor('Surveys'));
    //return $response->withRedirect((string)$uri);
});



$app->post('/Surveys/{survey}', function (ServerRequestInterface $request, ResponseInterface $response, $args)
{
    $survey = new Survey();
    $survey->submitSurvey($args['survey']);
    //return $survey->getSurvey($args['survey']);
});



$app->get('/Disconnect', function (ServerRequestInterface $request, ResponseInterface $response)
{
	if (isset($_SESSION['token']))
	{
		$db = Database::getInstance();
		$db->deleteConnectionToken($_SESSION['token']);
	}
	session_unset();
	session_destroy();
	sleep(1);
	return $response->withRedirect('Connect');
});



$app->run();

// TODO: Optimiser le temps de chargement pour que la première page s'affiche rapidement
// TODO: Afficher un chargement lors de l'arrivée sur le site
// TODO: Ajouter un blocage par mot de passe .htpasswd pour les pages admins
// TODO: Ajouter une notification pour l'utilisation des coookies
// TODO: charger les scripts JS à la fin mis à part ceux qui influent sur l'UI
// NOTE: Passer le code au validateur
// TODO: check Symfony/Validator
// NOTE: Utiliser le materialize.js pour le Dev, materialize.min.js pour la release
