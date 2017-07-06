<?php

/* HTTP codes sent by this platform

200 : Success

401 : Unauthorized (unauthenticated)
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
//var_dump($_SESSION);

// TODO: Change middleware, apply it to group instead of filtering url
// FIXME: Ajouter un filtre pour éviter de demander l'accès pour une page qui n'existe pas ou n'en necessite pas
$app->add(
	function(ServerRequestInterface $request, ResponseInterface $response, Callable $next) {
		$url = $request->getUri()->getPath();
		$urlParts ='';
		$needsAuth = true;

		if (strpos($url, '/') !== false)
		{
			$urlParts = explode('/', $url);
			$needsAuth = !($urlParts[0] == 'Subscribe' && preg_match('/^[a-zA-Z0-9]{10}$/i', $urlParts[1]));
		} else {
			$needsAuth = !($url == 'Connect' || $url == 'Subscribe');
		}

		if ($needsAuth && !isset($_SESSION['token'])) // Utilisateur non connecté
		{
			$_SESSION['url'] = $url;
			$db = Database::getInstance();
			// TODO: Créer la table Token
			// TODO: Ajouter un token quand on coche la case "Remember me"
			if (!$db->verifyConnectionToken($_SESSION['token']))
				return $response = $response->withRedirect('Connect', 403);
		}
		return $next($request, $response);
	}
);

// NOTE: Main ne doit être utilisée que par les classes spécifiques, vers lesquelles Slim redirige

$app->get('/Home', function (ServerRequestInterface $request, ResponseInterface $response)
{
	/*
	$res = $response->withJson(var_dump('mot de passe'));
	$hash = password_hash('mot de passe', PASSWORD_BCRYPT);
	$res = $res->withJson(var_dump($hash));
	$res = $res->withJson(var_dump(password_verify('mot de pass', $hash)));
	*/
	return Main::workInProgressPage();
});

$app->get('/Demo', function (ServerRequestInterface $request, ResponseInterface $response)
{
   $main = new Main();
   return $main->generateDemo();
});

$app->get('/Profile/{num}', function (ServerRequestInterface $request, ResponseInterface $response, $args)
{
    #return 'Profil numéro '.$args['num'];
    return Main::workInProgressPage();
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

$app->get('/Messages', function (ServerRequestInterface $request, ResponseInterface $response)
{
	//MailManager::testMailer();
	return Main::workInProgressPage();
});

$app->get('/Test', function (ServerRequestInterface $request, ResponseInterface $response)
{
	return Main::workInProgressPage();
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
		if ($db->verifyToken($pendingSub))
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

			if ($db->verifyToken($pendingSub))
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

$app->get('/Login', function (ServerRequestInterface $request, ResponseInterface $response)
{
	return Main::workInProgressPage();
});

$app->get('/Deco', function (ServerRequestInterface $request, ResponseInterface $response)
{
	session_unset();
	session_destroy();
	sleep(1);
	return $response->withRedirect('Connect');
	// FIXME: Redirect to Connect page
	//return Main::workInProgressPage();
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
