<?php

// TODO: Ajouter un champ Licence dans le composer.json
require "vendor/autoload.php";

// use Nom\De\Namespace\Trop\Long as court
use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Main;

$app = new App([
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true
    ]
]);

// TODO: Main ne doit être utilisée que par les classes spécifiques, vers lesquelles Slim redirige

$app->get('/Accueil', function (ServerRequestInterface $request, ResponseInterface $response) {
   $main = new Main();
   return $main->generateHome();
});

$app->get('/Profile/{num}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    return 'Profil numéro '.$args['num'];
});

$app->get('/Messages', function (ServerRequestInterface $request, ResponseInterface $response) {
    return 'Messages';
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
