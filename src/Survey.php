<?php

namespace Src;

class Survey extends Main
{

    public function __construct()
    {

    }

    // TODO: Revoir les noms de variables et regrouper dans les classes adaptées
    public function getSurveyMenu($email)
    {
        $db = Database::getInstance();
        $surveyType = \ORM::forTable('GenericSurvey')->findMany();
        $user = $db->getUser($email);
        $content = '';
        foreach ($surveyType as $survey)
        {
            $iteration = \ORM::forTable('Iteration')->join('GenericSurvey', array('Iteration.idGS', '=', 'GenericSurvey.idGS'))
            ->where('idGS', $survey->idGS)
            ->having_raw('DATEDIFF(NOW(), Iteration.BeginAt) < ?', array($survey->Lifespan))
            ->findMany();
            if ($iteration != false)
            {
                $submittedAnswers = \ORM::forTable('Survey')->where('idU', $user->idU)->where('idIT', $iteration->idIT)->findMany();
                $submissionsCount = sizeof($submittedAnswers);
                switch ($survey->SubmissionLimit)
                {
                    case 1:     // Unique submission
                    $content .= ($survey->SubmissionLimit == $submissionsCount) ? $this->generateDisabledCard($survey) : $this->generateActiveCard($survey);
                    break;

                    case 0:     // Unlimited submissions
                    $content .= $this->generateActiveCard($survey, $submissionsCount);
                    break;

                    default:    // Multiple submissions
                    if ($submissionsCount < $survey->SubmissionLimit)
                    $content .= $this->generateActiveCard($survey, $submissionsCount);
                    else
                    $content .= $this->generateDisabledCard($survey, $submissionsCount);
                    break;
                }
            }
        }

        return parent::generatePage($content, array('Surveys'));
    }

    // TODO: Changer le post, pour l'instant envoyé au clic sur le bouton répondre pour les tests
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
    public function submitSurvey($idGS)
    {
        $survey = \ORM::forTable('Survey')->create();
        $survey->set_expr('StartedAt', 'NOW()');
        $survey->set_expr('FinishedAt', 'NOW()');
        $survey->Document = 'Le document';
        $survey->idU = \ORM::forTable('Token')->findOne($_SESSION['token'])->idU;

        $genericSurvey = \ORM::forTable('GenericSurvey')->findOne($idGS);
        $iteration = \ORM::forTable('Iteration')->where('idGS', $idGS)->
        having_raw('DATEDIFF(NOW(), Iteration.BeginAt) < ?', array($survey->Lifespan))
        ->findOne();
        $survey->idIT = $iteration->idIT;
        var_dump(array(
            $iteration->idIT,
            $survey
        ));
        $survey->save();
    }

    public function getSurvey($idGS)
    {
        $survey = \ORM::forTable('GenericSurvey')->findOne($idGS);
        $questions = \ORM::forTable('GenericQuestion')->where('idGS', $idGS)->findMany();
        $content = '<p class="flow-text">'. $survey->Title .'</p>
            <form method="POST">'; // Header of the container
        foreach($questions as $question)
        {
            $answers = \ORM::forTable('GenericAnswer')->where('idGQ', $question->idGQ)->findMany();
            if ($answers != false)
            {
                $content .= '
                <div class="row">
                    <div class=" col s12">'
                    .$this->generateQuestionTitle($question);
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

                    default:
                        break;
                }
                $content .= '
                    </div>
                </div>';
            }
        }
        $content .= '
                <button id="send" class="btn waves-effect waves-light" type="submit" name="action">Envoyer
                    <i class="material-icons right">send</i>
                </button>
               	<div>
               		<label><p class="required_flag">*</p> Champs obligatoires</label>
               	</div>
            </form>'; // Footer of the container
        return parent::generatePage($content, array('Surveys'));
    }

    // TODO: Voir pour refactor la fonction qui génère le select
    private function generateCheckboxElement($answer, $idGQ)
    {
        return '
        <p class="col s6">
            <input id="'. $answer['idGA'] .'" answersto="'. $idGQ .'" type="checkbox" name="'. $answer['idGA'] .'" class="filled-in">
            <label for="'. $answer['idGA'] .'">'. $answer['Text'] .'</label>
        </p>';
    }

    private function generateCheckboxQuestion($question, $answers)
    {
        foreach ($answers as $answer)
            $content .= $this->generateCheckboxElement($answer->asArray(), $question->idGQ);

        if ($question->Other == 1)
            $content .= $this->generateCheckboxElement(array('idGA' => 'other-'.$question->idGQ, 'Text' => 'Autre..'), $question->idGQ);

        return $content;
    }

    private function generateSelectElement($answer)
    {
        return '
        <option value="'. $answer->idGA .'">'. $answer->Text .'</option>';
    }

    private function generateSelectQuestion($question, $answers)
    {

        $content = '
            <select name="'. $question->idGQ .'" answersto="'. $question->idGQ .'" id="select-'. $question->idGQ .'">
                <option value="" disabled selected>Choississez une réponse</option>';

        foreach ($answers as $answer)
            $content .= $this->generateSelectElement($answer);

        $content .= '
            </select>';

        return $content;
    }

    private function generateChipsQuestion($question)
    {
        $content = '
        <div id="chips-'. $question->idGQ .'" answersto="'. $question->idGQ .'" chips-data="'. $question->idGQ .'"class="chips chips-autocomplete" data-index="0" data-initialized="true">
            <input id="other" class="input" placeholder="">
        	<ul class="autocomplete-content dropdown-content"></ul>
        </div>';
        return $content;
    }

    private function generateRadioElement($answer)
    {
        return '
        <p>
            <input class="with-gap" name="'. $answer['idGQ'] .'" answersto="'. $answer['idGQ'] .'" type="radio" id="'. $answer['idGA'] .'" />
            <label for="'. $answer['idGA'] .'">'. $answer['Text'] .'</label>
        </p>';
    }

    private function generateRadioQuestion($question, $answers)
    {
        foreach ($answers as $answer)
            $content .= $this->generateRadioElement($answer->asArray());

        if ($question->Other == 1)
            $content .= $this->generateRadioElement(array('idGA' => 'other-'.$question->idGQ, 'Text' => 'Autre..'));

        return $content;
    }

    private function generateQuestionTitle($question)
    {
        $star = ($question->Required) ? '<p class="required_flag">*</p> ': '';
        $required = ($question->Required) ? 'required': ''; // FIXME: Automatize the add of red star on required field to clean this off
        return '<h4 id="question-'. $question->idGQ .'" class="question flow-text" '. $required .' type="'. $question->Type .'">'. $star . $question->Text .'</h4>';
    }

}
