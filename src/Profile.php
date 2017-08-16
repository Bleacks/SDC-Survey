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
    <h5 id="title"> Bienvenue '.$user->FirstName.' '.$user->LastName.' </h5>
	<h5 id="title_modify" style="display:none;">Changer les informations de '.$user->FirstName.' '.$user->LastName.'</h5>

	<div class="row">
        <div class="input-field col s12">
          <select class = "required validate" id="group" name="group">';
            foreach($groups as $group)
			{
				$content .= '<option value="'. $group->idG .'" '. ($user->idG == $group->idG ? 'selected' : '') .'>'. $group->Name .'</option>';
			}
            $content .= '
          </select>
          <label>Groupe</label>
        </div>
    </div>

    <div class="row">
      <div class="input-field col s6">
        <input class="personnal-input required validate" disabled selected value="'.$user->Age.'" id="age" type="number" min="0" max="100" class="validate" name="age">
        <label for="age" data-error="Age non valide" data-success="Age valide">Age</label>
      </div>
    </div>

    <div class="row">
        <div class="input-field col s12">
          <select class="required validate" id="city" name="city">';
		  $isNancy = $user->City == 'Nancy';
		  $content .= '
		  	<option value="Nancy"  '. ($isNancy ? 'selected' : '') .'>Nancy</option>
            <option value="Liege" '. ($isNancy ? '' : 'selected') .'>Liège</option>
          </select>
          <label>Ville</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12">
          <input class="personnal-input required validate" disabled selected value="'.$user->Email.'"id="email" type="email" class="validate" name="email">
          <label for="email" data-error="Adresse email non valide" data-success="Adresse email valide">Email</label>
        </div>
      </div>

	  <button id="modify" class="btn waves-effect waves-light" type="submit" onclick="allowChanges()">Changer les informations
          <i class="material-icons right">send</i>
      </button>

	<button id="password" class="btn waves-effect waves-light right" type="submit" onclick="window.location=\'ChangePassword\'">Modifier le mot de passe
        <i class="material-icons right">send</i>
    </button>

   <button id="send" class="btn waves-effect waves-light hide" type="submit" onclick="sendChangeInformation()">Enregistrer
       <i class="material-icons right">send</i>
   </button>';

		return parent::generatePage($content, array('Profile'));
	}


}
