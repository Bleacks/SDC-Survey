<!DOCTYPE html>
  <html>
    <head>
      <!--Import Google Icon Font-->
      <base href="/bleacks/SDC-Survey/" />
      <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="materialize/css/materialize.min.css"  media="screen,projection"/>

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    </head>

    <body>

      <?php

      require "vendor/autoload.php";

      use Slim\App;
      use Psr\Http\Message\ServerRequestInterface;
      use Psr\Http\Message\ResponseInterface;

      $app = new App([
          'settings' => [
              'determineRouteBeforeAppMiddleware' => true,
              'addContentLengthHeader' => false,
              'displayErrorDetails' => true
          ]
      ]);

      $app->get('/Accueil', function (ServerRequestInterface $request, ResponseInterface $response) {
          return 'Accueil';
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

      #TODO: Faire un dépôt Github ou GitLab suivant les droits
      #TODO: Faire fonctionner l'extension de gestion de git
      #TODO: Optimiser le temps de chargement pour que la première page s'affiche rapidement
      #TODO: Afficher un chargement lors de l'arrivée sur le site
      #TODO: Ajouter un blocage par mot de passe .htpasswd pour les pages admins
      #TODO: Ajouter une notification pour l'utilisation des coookies
      #TODO: charger les scripts JS à la fin mis à part ceux qui influent sur l'UI
      #TODO: Passer le code au validateur
      #TODO: Faire des générateurs pour les headers et footers

echo
'<!DOCTYPE html>
<html>
   <head>
      <!--Import Google Icon Font-->
      <base href="/bleacks/SDC-Survey/" />
      <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="materialize/css/materialize.min.css"  media="screen,projection"/>

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
   </head>

   <body>
      '. $content .'
      <!--Import jQuery before materialize.js-->
      <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
      <script type="text/javascript" src="js/materialize.min.js"></script>
   </body>
</html>
';
