<?php

namespace Src;

/**
* Class used to create generic Page wrapper
*
* Generates generic header, footer and main container wrapper to use in all child classes
*
* Every Class that has to generate pages and return HTML code should extend this Class and use parent method used to create generic wrapper
*
* @method string generateSideNavElement($link, $icon, $name)
* @method string header()
* @method string main()
* @method string footer()
* @method string generatePage($content, $scripts = aaray())
* @method string generateButton($onClickHandler)
*/
class Main
{

	/** Color displayed on main elements (Primary color as Material Design describes it) */
	const PRIMARY_COLOR = 'teal lighten-1';

	/** Color displayed on secondary elements  (secondary color as Material Design describes it) */
	const SECONDARY_COLOR = 'orange lighten-1';

	/** Pages listed on website and accessible
	Sorted by acces right
	Contains URI, Icon name and Displayed name in Nav Bar */
	const NAV_BAR_CONTENT = array(
		'Connected' => array(
			0 => array('/', 'home', 'Accueil'),
			1 => array('Demo', 'ondemand_video', 'Démonstration'),
			2 => array('Surveys', 'assignment', 'Questionnaires'),
			3 => array('Profile', 'account_box', 'Profil'),
			4 => array('Reset', 'cached', 'Changer le mot de passe'),
			5 => array('Disconnect', 'launch', 'Déconnexion')
		),
		'Disconnected' => array(
			0 => array('Connect', 'input', 'Connexion'),
			1 => array('Subscribe', 'assignment_ind', 'Inscription'),
			2 => array('Recovery', 'cached', 'Mot de passe oublié')
		)
	);

   /**
   * Constructor of the Main Class
   */
   function __construct()
   {
      // TODO: Revoir l'architecture de la classe
      // TODO: Gestion hierachisée des erreurs pour factoriser au maximum les constantes
   }

   	/**
   	* Generates NavBar with the given color
   	* @param string $color Color of icons
   	* @return string HTML Code of the grey NavBar
   	*/
   private function generateNavBar($color)
   {
		$navBar = '';
		$array = (isset($_SESSION['token']) && $_SESSION['token'] != '') ? Main::NAV_BAR_CONTENT['Connected'] : Main::NAV_BAR_CONTENT['Disconnected'];

		for ($i = 0; $i < sizeof($array); $i++)
		{
			$link = $array[$i][0];
			$icon = $array[$i][1];
			$name = $array[$i][2];
			$navBar .= '
			<li>
				<a class="flow-text" href='.$link.'>
					<i class="'.$color.'-text text-darken-2 left material-icons">'.$icon.'</i>'
				.$name.'</a>
			</li>
			';
		}

		return $navBar;
	}

	/**
	* Generates white NavBar
	* Calls generateNavBar
	* @return string HTML Code of the white NavBar
	*/
   private function generateNavBarElement()
   {
	   return $this->generateNavBar('white');
   }

	/**
	* Generates grey NavBar
	* Calls generateNavBar
	* @return string HTML Code of the grey NavBar
	*/
   private function generateSideBarElement()
   {
	   return $this->generateNavBar('grey');
   }

   /**
   * Creates generic header
   * @return $header Computed header
   */
	private function header()
	{
		// TODO: Rediriger les href du header
		/*$navContent = '';
		if (isset($_SESSION['token']))
      		$navContent .= $this->generateSideNavElement('/', 'home', 'Accueil')
						.  $this->generateSideNavElement('Demo', 'ondemand_video', 'Démonstration')
						.  $this->generateSideNavElement('Surveys', 'assignement', 'Questionnaires')
						.  $this->generateSideNavElement('Profile', 'account_box', 'Profil')
						.  $this->generateSideNavElement('Reset', 'cached', 'Changer le mot de passe')
						.  $this->generateSideNavElement('Disconnect', 'launch', 'Déconnexion');
		else
			$navContent .= $this->generateSideNavElement('Connect', 'input', 'Connexion')
						.  $this->generateSideNavElement('Subscribe', 'assignment_ind', 'Inscription')
						.  $this->generateSideNavElement('Recovery', 'cached', 'Mot de passe oublié');*/
		$header = '
<header>
	<nav class="'.Main::PRIMARY_COLOR.'">
    	<div class="nav-wrapper">
      		<a href="Accueil" class="brand-logo">SDC-Survey</a>
			<a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
			<ul id="nav-mobile" class="right hide-on-med-and-down">
				'. $this->generateNavBarElement() .'
			</ul>
			<ul class="side-nav" id="mobile-demo">
				<h4 class="center-align teal-text">SDC-Survey</h4>
				'. $this->generateSideBarElement() .'
			</ul>
		</div>
	</nav>

	<div id="notification" class="red z-depth-2" hidden>
		<p id="notification_text" class="white-text center-align flow-text"><p>
	</div>
</header>';
		return $header;
   }

   /**
   * Example of Chips usage
   * @return (String):Computed content
   */
   private function demoContent()
   {
      // TODO: Séparer les méthodes spécifiques aux pages dans des classes spécifiques
      return '
<div class="chips chips-placeholder chips-autocomplete chips-initial" data-index="0" data-initialized="true">
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
      // TODO: Complete footer informations
      // TODO: Ajouter une description
      return '
<footer class="page-footer '.Main::PRIMARY_COLOR.'">
	<div class="container">
    	<div class="row">
			<div class="col l6 s12">
				<h5 class="flow-text white-text">SDC-Survey Web Platform</h5>
				<p class="grey-text text-lighten-4">Desciption</p>
			</div>

			<div class="col l4 offset-l2 s12">
				<h5 class="white-text">Navigation</h5>
				<ul hidden>
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
   function generateDemo()
   {
      return $this->generatePage($this->demoContent(), array('chips'));
   }

   /**
   * Wraps content created by specific sub-class with general header and footer
   */
   protected function generatePage($content, $scripts = array())
   {
	   $script = '';
	   $scripts[] = 'Loading';
	   foreach ($scripts as $name)
	   		$script .= '<script type="text/javascript" src="js/'.$name.'.js"></script>
			';

	   return '<!DOCTYPE html>
		 <html>
		    <head>
		       <!--Initialize environment-->
		       <base href="/SDC-Survey/" />
		       <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">

		       <!--Import Google Icon Font-->
		       <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

			   <!--Import materialize.css-->
			   <link type="text/css" rel="stylesheet" href="materialize/css/materialize.min.css" media="screen,projection">

			   <!--Import personnal CSS File-->
			   <link type="text/css" rel="stylesheet" href="css/Stylesheet.css" media="screen,projection">

			   <!--Let browser know website is optimized for mobile-->
		       <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		    </head>

		    <body class="loading">
		       '. $this->header()
		        . '
			<main>
				'
				. $content
				. '
			</main>
			'
		        . $this->footer() .'
		       <!--Import jQuery before materialize.js-->
		       <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		       <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
		       <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
		       '.$script.'
		    </body>
		 </html>';
   }


   	/**
   	* Generates submit button for subscription forms
   	* @param $onclickHandler : JS Function called when this button is clicked
   	* @return : Button with given handler
   	*/
   	protected function getButton($onclickHandler)
   	{
   	return '
   	<button id="send" class="btn waves-effect waves-light disabled" type="submit" onclick="'.$onclickHandler.'">Envoyer
   		<i class="material-icons right">send</i>
   	</button>
   	<div>
   		<label><p class="required_flag">*</p> Champs obligatoires</label>
   	</div>';
   	}

   /**
   * Send error log to Apache, factoring error context in one method
   * @param (String)$message : Error message
   */
   public function logError($message)
   {
      error_log('Error creating Main.php : ' . $message);
   }
	
	public function generateDefaultErrorPage($message)
	{
		return $this->generatePage($message, array());
	}
}
