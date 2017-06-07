<?php

namespace Src;

/**
* Class used to create Page base
*/
class Main
{

   /**
   * Constructor of the Main Class
   */
   function __construct()
   {
      // TODO: Revoir l'architecture de la classe
      // TODO: Gestion hierachisée des erreurs pour factoriser au maximum les constantes
   }

   /**
   * Generate header of pages
   * @return (String):Computed header
   */
   private function header()
   {
      // TODO: Rediriger les href du header
      return '<header>
  <nav>
    <div class="nav-wrapper">
      <a href="Accueil" class="brand-logo">Logo</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="Home">Home</a></li>
        <li><a href="Profile/54">Profile</a></li>
        <li><a href="Messages">Messages</a></li>
        <li><a href="Settings">Settings</a></li>
      </ul>
    </div>
  </nav>
</header>';
   }

   /**
   * Example of Chips usage
   * @return (String):Computed content
   */
   private function content()
   {
      // TODO: Séparer les méthodes spécifiques aux pages dans des classes spécifiques
      return
      '<div class="chips chips-placeholder chips-autocomplete chips-initial" data-index="0" data-initialized="true">
         <input id="e2b78123-5a53-e67d-d60c-8293d451905a" class="input" placeholder="">
         <ul class="autocomplete-content dropdown-content"></ul>
      </div>';
   }

   /**
   * Generate footer of pages
   * @return (String):Computed Footer
   */
   private function footer()
   {
      // TODO: Ajouter les liens vers les autres pages
      // TODO: Ajouter une description
      return '<footer class="page-footer">
          <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <h5 class="white-text">SDC-Survey Web Platform</h5>
                <p class="grey-text text-lighten-4">Desciption</p>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Navigation</h5>
                <ul>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 1</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
                  <li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © 2017 Copyright Text
            <a class="grey-text text-lighten-4 right" href="https://www.list.lu/">LIST</a>
            </div>
          </div>
        </footer>';
   }

   /**
   * Final assembly of page elements
   * @return (String):Computed page
   */
   function generateHome()
   {
      if (empty($header = $this->header()))
         $this->logError("header is empty");
      if (empty($content = $this->content()))
         $this->logError("content is empty");
      if (empty($footer = $this->footer()))
         $this->logError("footer is empty");

      return '<!DOCTYPE html>
         <html>
            <head>
               <!--Initialize environment-->
               <base href="/bleacks/SDC-Survey/" />
               <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">

               <!--Import Google Icon Font-->
               <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

               <!--Import materialize.css-->
               <link type="text/css" rel="stylesheet" href="materialize/css/materialize.min.css"  media="screen,projection"/>

               <!--Let browser know website is optimized for mobile-->
               <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            </head>

            <body>
               '. $header
                . $content
                . $footer .'
               <!--Import jQuery before materialize.js-->
               <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
               <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.js"></script>
               <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
               <script type="text/javascript" src="js/chips.js"></script>
            </body>
         </html>';
   }

   /**
   * Send error log to Apache, factoring error context in one method
   * @param (String)$message : Error message
   */
   private function logError($message)
   {
      error_log('Error creating Main.php : ' . $message);
   }

   /**
   * Returns the work in progress page
   */
   public static function workInProgressPage()
   {
      // TODO: Pimper la page, voir même utiliser un système de gestion d'erreur pour factoriser
      return 'Work in progress';
   }
}
