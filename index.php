<?php

// TODO: Ajouter un champ Licence dans le composer.json
require_once "vendor/autoload.php";

use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Main;
use Src\Subscribe;
use Src\Database;

$app = new App([
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true
    ]
]);

// TODO: Main ne doit être utilisée que par les classes spécifiques, vers lesquelles Slim redirige
// TODO: Refactor au plus simple les méthodes app->*

$app->get('/Home', function (ServerRequestInterface $request, ResponseInterface $response) {
   return Main::workInProgressPage();
});

$app->get('/Demo', function (ServerRequestInterface $request, ResponseInterface $response) {
   $main = new Main();
   return $main->generateDemo();
});

$app->get('/Profile/{num}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    #return 'Profil numéro '.$args['num'];
    return Main::workInProgressPage();
});

$app->get('/Messages', function (ServerRequestInterface $request, ResponseInterface $response) {
    return Main::workInProgressPage();
});

$app->get('/Settings', function (ServerRequestInterface $request, ResponseInterface $response) {
    return Main::workInProgressPage();
});

$app->get('/Subscribe', function (ServerRequestInterface $request, ResponseInterface $response) {
   $subscribe = new Subscribe();
   return $subscribe->generatePageGet();
});
/* TODO: Supprimer la méthode liée dans la classe Subscribe
$app->post('/Subscribe', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
   $subscribe = new Subscribe();
   $app->redirect();
   //return $subscribe->generatePagePost($request->getParsedBody());
});
*/
// TEST
$app->post('/Subscribe', function(ServerRequestInterface $request, ResponseInterface $response) use ($app) {
   // TODO: Lien avec la BDD (Envoi et réception de données fonctionnel)
   $db = Database::getInstance();
   $post = $request->getParsedBody();
   if (isset($post['email']) && !is_null($post['email']) && isset($post['password']) && !is_null($post['password']))
      $db->subscribeUser($post['email'], $post['password']);
});

$app->get('/Subscribe/{token}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $subscribe = new Subscribe();
    return $subscribe->generatePageValidation($args['token']);
});

$app->post('/Subscribe/{token}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $subscribe = new Subscribe();
    return $subscribe->generatePageSubscribeEnd($request->getParsedBody());
});

$app->get('/Login', function (ServerRequestInterface $request, ResponseInterface $response) {
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
