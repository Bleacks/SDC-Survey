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
      // TODO: Gestion centralisée des erreurs pour chaque génération de code
      if (empty($content))
      {
         return 'Error no document created';
      }
   }

   /**
   * Generate header of pages
   */
   function header()
   {
      // TODO: Rediriger les href du header
      return '<header>
  <nav>
    <div class="nav-wrapper">
      <a href="#" class="brand-logo">Logo</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="sass.html">Sass</a></li>
        <li><a href="badges.html">Components</a></li>
        <li><a href="collapsible.html">JavaScript</a></li>
      </ul>
    </div>
  </nav>
</header>';
   }

   /**
   * Example of Chips usage
   */
   function content()
   {
      // TODO: Séparer les méthodes spécifiques aux pages dans des classes spécifiques
      return
      '<div class="chips chips-autocomplete" data-index="0" data-initialized="true">
         <input id="e2b78123-5a53-e67d-d60c-8293d451905a" class="input" placeholder="">
         <ul class="autocomplete-content dropdown-content"></ul>
      </div>';
   }

   /**
   * Generate footer of pages
   */
   function footer()
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
   */
   function generateHome()
   {
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
               '. $this->header()
                . $this->content()
                . $this->footer() .'
               <!--Import jQuery before materialize.js-->
               <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
               <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
               <script type="text/javascript" src="materialize/js/materialize.min.js"></script>
               <script type="text/javascript" src="js/chips.js"></script>
            </body>
         </html>';
   }
}
