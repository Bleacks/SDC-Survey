<?php

Namespace Src;

/* Form to to get the email of the user to be able to send him a code to update pw
and form to get the his new password*/

class Profile extends Main {

	public function __construct()
	{

	}
	// Form to get the email

	public function getPageProfile($user, $groups)
	{
        // tant qu'on a pas parcouru tout le tableau && Qu'on a pas trouvé l'element
            // POur chaque groupe on regarde si l'id est le meme que l'idG de l'user, s oui on sort en changeant le boolean

            $groupName = '';
            for ( $i = 0; empty($groupName) && $i < sizeof($groups); $i++ )
                if ($groups[$i]->idG == $user->idG)
                    $groupName = $groups[$i]->Name;
		$content =
		'
    <h5>'.$user->FirstName.' '.$user->LastName.'</h5>

    <div class="row">
        <div class="input-field col s12">
          <select id="loltuchangeras">
            <option class="personnal-input" value="" disabled selected  >'.$groupName.'</option>';
            foreach($groups as $group)
                $content .= '<option value="'. $group->idG .'" >'. $group->Name .'</option>';
            $content .= '
          </select>
          <label>Groupe</label>
        </div>
    </div>

    <div class="row">
      <div class="input-field col s6">
        <input class="personnal-input" disabled selected value="'.$user->Age.'" id="age" type="number" class="validate">
        <label for="age">Age</label>
      </div>
    </div>

    <div class="row">
        <div class="input-field col s12">
          <select id="personnal-input">
            <option class="personnal-input" value="" disabled selected  >'.$user->City.'</option>
            <option value="Liege" >Liège</option>
            <option value="Nancy" >Nancy</option>
          </select>
          <label>Ville</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12">
          <input class="personnal-input" disabled selected value="'.$user->Email.'"id="email" type="email" class="validate">
          <label for="email" data-error="wrong" data-success="right">Email</label>
        </div>
      </div>

    <button id="password" class="btn waves-effect waves-light" type="submit" onclick="window.location=\'ChangePassword\'">Modifier le mot de passe
        <i class="material-icons right">send</i>
    </button>

   <button id="modify" class="btn-floating btn-large red waves-effect right" onclick="allowChanges()">
     <i class="large material-icons">mode_edit</i>
   </button>

   <button id="send" class="btn waves-effect waves-light disabled hide" type="submit" onclick="'.$onclickHandler.'">Enregistrer
       <i class="material-icons right">send</i>
   </button>';

		return parent::generatePage($content, array('Profile'));
	}


}
