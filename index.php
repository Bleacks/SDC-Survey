<?php

// TODO: Ajouter un champ Licence dans le composer.json
require_once "vendor/autoload.php";

use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Main;
use Src\Subscribe;
use Src\Database;
use Src\MailManager;

$app = new App([
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true
    ]
]);

// TODO: Main ne doit être utilisée que par les classes spécifiques, vers lesquelles Slim redirige
// TODO: Refactor au plus simple les méthodes app->*

$app->get('/Home', function (ServerRequestInterface $request, ResponseInterface $response)
{
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

$app->get('/Messages', function (ServerRequestInterface $request, ResponseInterface $response)
{
	MailManager::testMailer();
	return Main::workInProgressPage();
});

$app->get('/Settings', function (ServerRequestInterface $request, ResponseInterface $response)
{
	return Main::workInProgressPage();
});

$app->get('/Subscribe', function (ServerRequestInterface $request, ResponseInterface $response)
{
   $subscribe = new Subscribe();
   return $subscribe->getPageSubscribe();
});

$app->post('/Subscribe', function(ServerRequestInterface $request, ResponseInterface $response) use ($app)
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
	// TODO: Remettre en forme les accolades de ce fichier
	$subscribe = new Subscribe();
	$db = Database::getInstance();
	$token = $args['token'];
	$regex = '/^[a-zA-Z0-9]{10}$/i';

	// TODO: Ajouter une modale avec crécupéreation des données du serveur et en cas de non validité afficher l'erreur dedans, la fermer sinon et permettre l'inscription
	// TODO: Ajouter une suppression des tokens on register confirm

	if (preg_match($regex, $token))
	{
		if ($db->verifyToken($token))
		{
			$res = $subscribe->getPageSubscribeConfirmation($token);
		} else {
			$res = $subscribe->getPagePerishedConfirmation($token);
		}
	} else {
		$res = $response->withStatus(422);
	}

    return $res;
});

$app->get('/Login', function (ServerRequestInterface $request, ResponseInterface $response)
{
	return Main::workInProgressPage();
});

$app->run();

# Usefull $_SERVER indexes
  # REQUEST_METHOD : GET,POST,PUT,DELETE
  # PATH_INFO      : Path requested in URL (URI)

//var_dump($_SERVER);

if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '')
   $path_info = $_SERVER['PATH_INFO'];

// TODO: Optimiser le temps de chargement pour que la première page s'affiche rapidement
// TODO: Afficher un chargement lors de l'arrivée sur le site
// TODO: Ajouter un blocage par mot de passe .htpasswd pour les pages admins
// TODO: Ajouter une notification pour l'utilisation des coookies
// TODO: charger les scripts JS à la fin mis à part ceux qui influent sur l'UI
// TODO: Passer le code au validateur
// TODO: Faire des générateurs pour les headers et footers
// TODO: check Symfony/Validator
// TODO: Utiliser le materialize.js pour le Dev, materialize.min.js pour la release
