<?php

namespace Src;

// TODO: Refactor this view by putting out all the direct Database call
/** Class that's used to create form related pages */
class Survey extends Main
{

    public function __construct()
    {

    }

    /**
    * Retrieves all available surveys for the given user and generates a menu page
    * @param $email string User email
    * @return string HTML Code of the menu page
    */
    public function getSurveyMenu($email)
    {
        $genericSurveys = \ORM::forTable('GenericSurvey')->findMany();
        $user = Database::getInstance()->getUser($email);
        $content = '';
        foreach ($genericSurveys as $genericSurvey)
        {
            $iteration = \ORM::forTable('Iteration')->join('GenericSurvey', array('Iteration.idGS', '=', 'GenericSurvey.idGS'))
            ->where('idGS', $genericSurvey->idGS)
            ->having_raw('DATEDIFF(NOW(), Iteration.BeginAt) < ?', array($genericSurvey->Lifespan))
            ->findMany();
            if ($iteration != false)
            {
                $submittedAnswers = \ORM::forTable('Survey')->where('idU', $user->idU)->where('idIT', $iteration->idIT)->findMany();
                $submissionsCount = sizeof($submittedAnswers);
                switch ($genericSurvey->SubmissionLimit)
                {
                    case 1:     // Unique submission
                    $content .= ($genericSurvey->SubmissionLimit == $submissionsCount) ? $this->generateDisabledCard($genericSurvey) : $this->generateActiveCard($genericSurvey);
                    break;

                    case 0:     // Unlimited submissions
                    $content .= $this->generateActiveCard($genericSurvey, $submissionsCount);
                    break;

                    default:    // Multiple submissions
                    if ($submissionsCount < $genericSurvey->SubmissionLimit)
                    $content .= $this->generateActiveCard($genericSurvey, $submissionsCount);
                    else
                    $content .= $this->generateDisabledCard($genericSurvey, $submissionsCount);
                    break;
                }
            }
        }

        return parent::generatePage($content, array('Surveys'));
    }

    /**
    * Creates an active survey card for the given GenericSurvey
    * @param $survey Object(ORM) GenericSurvey associated to this card
    * @param $count int Current number of submissions
    * @return HTML Code of the card
    */
    private function generateActiveCard($survey, $count = 0)
    {
        $floating = ($count > 0) ? '<a class="btn-floating halfway-fab waves-light '. parent::SECONDARY_COLOR .'" onclick=""><p class="center" style="margin-top: 0%;">'. $count .'</p></a>' : '';
        $card = '
<div class="col s12 m6">
    <div class="card hoverable sticky-action">
        <div class="card-image">
            '. $floating .'
            <img src="img/surveys/'. 1 .'.jpg">
        </div>

        <div class="card-content">
            <span class="card-title activator grey-text text-darken-4">'. $survey->Title .'<i class="material-icons right">more_vert</i></span>
            <p>'. $survey->Description .'</p>
        </div>

        <div class="card-action">
            <!--<a class="" id="'. $survey->idGS .'">Répondre au questionnaire</a>-->
            <a class="survey-target" href="Surveys/'. $survey->idGS .'">Répondre au questionnaire</a>
        </div>

        <div class="card-reveal">
            <span class="card-title grey-text text-darken-4">'
                . $survey->Title .'<i class="material-icons right">close</i>
            </span>
            <p>'. $survey->More .'</p>
        </div>
    </div>
</div>';
        return $card;
    }

    /**
    * Creates a disabled survey card for the given GenericSurvey
    * @param $survey Object(ORM) GenericSurvey associated to this card
    * @param $count int Current number of submissions
    * @return HTML Code of the card
    */
    private function generateDisabledCard($survey, $count = 0)
    {
        $floating = ($count > 0) ? '<a class="btn-floating halfway-fab disabled waves-light '. parent::SECONDARY_COLOR .'" onclick=""><p class="center" style="margin-top: 0%;">'. $count .'</p></a>' : '';
        $icon = ($count > 0) ? 'done_all' : 'done';
        $link = ($count > 0) ? 'Réponses envoyées' : 'Réponse envoyée';
        $card = '
<div class="col s12 m6">
    <div class="card hoverable sticky-action">
        <div class="card-image">
            '. $floating .'
            <img src="img/surveys/'. 1 .'.jpg" style="opacity: 0.4">
        </div>

        <div class="card-content grey-text">
            <span class="card-title activator grey-text text-darken-1">'. $survey->Title .'<i class="material-icons right">more_vert</i></span>
            <p>'. $survey->Description .'</p>
        </div>

        <div class="card-action">
            <a class="grey-text" id="'. $survey->idGS .'" onclick=""><i class="material-icons right">'. $icon .'</i>'. $link .'</a>
        </div>

        <div class="card-reveal">
            <span class="card-title grey-text text-darken-4">'
                . $survey->Title .'<i class="material-icons right">close</i>
            </span>
            <p>'. $survey->More .'</p>
        </div>
    </div>
</div>';
        return $card;
    }

    // NOTE: Retirer cette fonction de test
    /** Submits an instance of the given GenericSurvey */
    public function submitSurvey($idGS)
    {
        $survey = \ORM::forTable('Survey')->create();
        $survey->set_expr('FinishedAt', 'NOW()');
        $survey->Document = 'Le document';
        $survey->idU = \ORM::forTable('Token')->findOne($_SESSION['token'])->idU;
        $db = Database::getInstance();

        $genericSurvey = $db->getGenericSurvey();
        $iteration = $db->getCurrentIteration($genericSurvey);
        $survey->idIT = $iteration->idIT;
        var_dump(array(
            $iteration->idIT,
            $survey
        ));
        $survey->save();
    }

    /**
    * Generates a form for the given GenericSurvey
    * @param $idGS int GenericSurvey id in the Database
    * @param $user Object(ORM)
    * @return $string HTML Code of the form
    */
    public function getSurvey($idGS, $user)
    {
        $survey = \ORM::forTable('GenericSurvey')->findOne($idGS);
        $questions = \ORM::forTable('GenericQuestion')->where('idGS', $idGS)->findMany();
        $content = '<p class="flow-text center-align">'. $survey->Title .'</p>'; // Header of the container
        // TODO: Ajouter la description du questionnaire
        foreach($questions as $question)
        {
            $answers = \ORM::forTable('GenericAnswer')->where('idGQ', $question->idGQ)->findMany();
            if ($answers != false || $question->Type == '6')
            {
                $content .= '
                <div class="row">
                '.$this->generateQuestionTitle($question).'
                    <div class=" col s12">';
                switch($question->Type)
                {
                    case '1':   // Multiple choice (Checkbox)
                        $content .= $this->generateCheckboxQuestion($question, $answers);
                        break;

                    case '2':   // Unique choice (Select)
                        $content .= $this->generateSelectQuestion($question, $answers);
                        break;

                    case '3':   // Multiple choice (Chips)
                        $content .= $this->generateChipsQuestion($question);
                        break;

                    case '4':   // Unique choice (Radio)
                        $content .= $this->generateRadioQuestion($question, $answers);
                        break;

                    case '5':   // Group question (checkbox for each member of group)
                        $content .= $this->generateGroupQuestion($question, $user);
                        break;

                    case '6':   // Text input question (field Survey.document)
                        $content .= $this->generateTextQuestion($question);
                        break;

                    default:
                        break;
                }
                $content .= '
                    </div>
                </div>';
            }
        }
        $content .= '
                <button id="send" class="btn waves-effect waves-light" disabled onclick="sendSurvey()">Envoyer
                    <i class="material-icons right">send</i>
                </button>
               	<div>
               		<label><p class="required_flag">*</p> Champs obligatoires</label>
               	</div>'; // Footer of the container
        return parent::generatePage($content, array('Surveys'));
    }

    /**
    * Generates a Text question
    * @param $question Object(ORM) GenericQuestion this question bloc represents
    * @return string HTML Code of the generated bloc
    */
    public function generateTextQuestion($question)
    {
        $content = '
        <p class="col s12">
            <input id="text-'. $question->idGQ .'" answersto="'. $question->idGQ .'" type="text" data-length="'. Database::FIELDS_LENGTH['Survey']['Document'] .'" name="document-'. $question->idGQ .'" placeholder="Texte court">
        </p>';
        return $content;
    }

    /**
    * Generates a Checkbox element with the given user group members
    * @param $answer Object(ORM) GenericAnswer this checkbox represents
    * @return string HTML Code of the generated bloc
    */
    private function generateGroupElement($answer)
    {
        // TODO: Refactor this one and generateCheckboxElement to use a common private function

        $content = '
        <p class="col s12 m6">
            <input id="'. $answer['idGA'] .'" answersto="'. $answer['idGQ'] .'" type="checkbox" name="'. $answer['Text'] .'" class="filled-in">
            <label for="'. $answer['idGA'] .'">'. $answer['Text'] .'</label>
        </p>';
        return $content;
    }

    /**
    * Generates a Checkbox question with the given user group members
    * @param $question Object(ORM) GenericQuestion this question bloc represents
    * @param $user Object(ORM) Connected user
    * @return string HTML Code of the generated bloc
    */
    public function generateGroupQuestion($question, $user)
    {
        // FIXME: Rendre la fonction générique avec différents groupes (init avec la BDD)

        $group = \ORM::forTable('Groups')->findOne($user->idG);
        $groupUsers = \ORM::forTable('Users')->where('idG', $group->idG)->findMany();
        $individual = \ORM::forTable('GenericAnswer')->where('idGQ', $question->idGQ)->findOne();
        $db = Database::getInstance();
        $answers = array();

        foreach ($groupUsers as $groupUser)
        {
            if ($groupUser != $user)
            {
                $answerText = $groupUser->FirstName .' '. $groupUser->LastName;
                $answers[] = $db->createGenericAnswer($question->idGQ, $answerText);
            }
        }
        $adminUsers = \ORM::forTable('Users')->where('Admin', 1)->findMany();
        foreach ($adminUsers as $adminUser)
        {
            $answerText = $adminUser->FirstName .' '. $adminUser->LastName;
            $genericAnswer = $db->createGenericAnswer($question->idGQ, $answerText);
            if (!in_array($genericAnswer, $answers))
                $answers[] = $genericAnswer;
        }
        $answers[] = $db->createGenericAnswer($question->idGQ, $individual->Text);

        $content = '';
        foreach ($answers as $answer)
        {
            $content .= $this->generateGroupElement($answer);
        }

        return $content;
    }

    /**
    * Generates a checkbox for the given answer associated to the given questionId
    * @param $answer Object(ORM) Answer this checkbox represents
    * @param $idGQ int GenericSurvey id in the Database
    * @return HTML Code of the generated Checkbox
    */
    private function generateCheckboxElement($answer)
    {
        $content = '
        <p class="col s12 m6">
            <input id="'. $answer['idGA'] .'" answersto="'. $answer['idGQ'] .'" type="checkbox" name="'. $answer['idGA'] .'" class="filled-in">
            <label for="'. $answer['idGA'] .'">'. $answer['Text'] .'</label>
        </p>';
        return $content;
    }

    /**
    * Generates a bloc for the given question with checkboxes based answers
    * @param $question Object(ORM) GenericQuestion this question bloc represents
    * @param $answers Object(ORM) Possible answers to this question
    * @return string HTML Code of the generated bloc
    */
    private function generateCheckboxQuestion($question, $answers)
    {
        $content = '';
        foreach ($answers as $answer)
            $content .= $this->generateCheckboxElement($answer);

        if ($question->Other == 1)
            $content .= $this->generateCheckboxElement(array('idGA' => 'other-'.$question->idGQ, 'Text' => 'Autre..'), $question->idGQ);

        return $content;
    }

    /**
    * Generates a select element for the given answer
    * @param $answer Object(ORM) Answer this checkbox represents
    * @return HTML Code of the generated select element
    */
    private function generateSelectElement($answer)
    {
        return '
        <option value="'. $answer->idGA .'">'. $answer->Text .'</option>';
    }

    /**
    * Generates a bloc for the given question with select based answers
    * @param $question Object(ORM) GenericQuestion this question bloc represents
    * @param $answers Object(ORM) Possible answers to this question
    * @return string HTML Code of the generated bloc
    */
    private function generateSelectQuestion($question, $answers)
    {
        $content = '
        <p class="col s12">
            <select name="'. $question->idGQ .'" answersto="'. $question->idGQ .'" id="select-'. $question->idGQ .'">
                <option disabled selected>Choississez une réponse</option>';

        foreach ($answers as $answer)
            $content .= $this->generateSelectElement($answer);

        $content .= '
            </select>
        </p>';

        return $content;
    }

    /**
    * Generates a bloc for the given question with chips based answers
    * @param $question Object(ORM) GenericQuestion this question bloc represents
    * @return string HTML Code of the generated bloc
    */
    private function generateChipsQuestion($question)
    {
        $content = '
        <div id="chips-'. $question->idGQ .'" class="chips chips-autocomplete" answersto="'. $question->idGQ .'" chips-data="'. $question->idGQ .'" data-index="0" data-initialized="true">
            <input id="chips-'. $question->idGQ .'" class="input">
        	<ul class="autocomplete-content dropdown-content"></ul>
        </div>';
        return $content;
    }

    /**
    * Generates a radiobutton for the given answer
    * @param $answer Object(ORM) Answer this checkbox represents
    * @return HTML Code of the generated select element
    */
    private function generateRadioElement($answer)
    {
        $content = '
        <p class="col s12 m6">
            <input value="'. $answer['idGA'] .'" class="with-gap" name="'. $answer['idGQ'] .'" answersto="'. $answer['idGQ'] .'" type="radio" id="'. $answer['idGA'] .'" />
            <label for="'. $answer['idGA'] .'">'. $answer['Text'] .'</label>
        </p>';
        return $content;
    }

    /**
    * Generates a bloc for the given question with radiobutton based answers
    * @param $question Object(ORM) GenericQuestion this question bloc represents
    * @return string HTML Code of the generated bloc
    */
    private function generateRadioQuestion($question, $answers)
    {
        $content = '';
        foreach ($answers as $answer)
            $content .= $this->generateRadioElement($answer->asArray());

        if ($question->Other == 1)
            $content .= $this->generateRadioElement(array('idGA' => 'other-'.$question->idGQ, 'Text' => 'Autre..'));

        return $content;
    }

    /**
    * Generates a title associated to the given question
    * @param $question Object(ORM) Question this title reprensents
    * @return HTML Code of the generated title
    */
    private function generateQuestionTitle($question)
    {
        $star = ($question->Required) ? '<p class="required_flag">*</p> ': '';
        $required = ($question->Required) ? 'required': ''; // FIXME: Automatize the add of red star on required field to clean this off
        $content = '<h4 id="question-'. $question->idGQ .'" class="question flow-text" '. $required .' type="'. $question->Type .'">'. $star . $question->Text .'</h4>';
        return $content;
    }

}
