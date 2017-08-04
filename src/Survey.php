<?php

namespace Src;

class Survey extends Main
{

    public function __construct()
    {

    }

    // TODO: Supprimer l'autre fonction, revoir les noms de variables et regrouper dans les classes adaptées
    public function getSurveyMenu($email)
    {
        $db = Database::getInstance();
        $surveyType = \ORM::forTable('GenericSurvey')->findMany();
        $user = $db->getUser($email);
        $content = '
            <div class="container row">';
        foreach ($surveyType as $survey)
        {
            $iteration = \ORM::forTable('Iteration')->where('idGS', $survey->idGS)->findOne();
            $submittedAnswers = \ORM::forTable('Survey')->where('idU', $user->idU)->where('idIT', $iteration->idIT)->findMany();
            $submissionsCount = sizeof($submittedAnswers);
            switch ($survey->SubmissionLimit)
            {
                case 1:
                    $content .= ($survey->SubmissionLimit == $submissionsCount) ? $this->generateDisabledCard($survey) : $this->generateActiveCard($survey);
                    break;
                case $submissionsCount:
                    if ($submissionsCount == 0)
                        $content .= $this->generateActiveCard($survey);
                    else
                        $content .= $this->generateDisabledCard($survey);
                    break;
                case 0:
                    $content .= $this->generateActiveCard($survey, $submissionsCount);
                    break;
                default:
                    $content .= $this->generateActiveCard($survey, $submissionsCount);
                    break;
            }
        }
        $content .= '
            </div>';

        return parent::generatePage($content, array('Surveys'));
    }

    // TODO: Changer le post, pour l'instant envoyé au clic sur le bouton répondre pour les tests
    private function generateActiveCard($survey, $count = 0)
    {
        $floating = ($count > 0) ? '<a class="btn-floating halfway-fab waves-effect waves-light '. parent::SECONDARY_COLOR .'" onclick=""><p class="center" style="margin-top: 0%;">'. $count .'</p></a>' : '';
        $card = '
<div class="col s6">
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
            <!--<a class="survey-target" href="Surveys/'. $survey->idGS .'">Répondre au questionnaire</a>-->
            <a class="survey-target" id="'. $survey->idGS .'">Répondre au questionnaire</a>
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

    private function generateDisabledCard($survey)
    {
        $card = '
<div class="col s6">
    <div class="card hoverable sticky-action">
        <div class="card-image">
            <img src="img/surveys/'. 1 .'.jpg" style="opacity: 0.4">
        </div>

        <div class="card-content grey-text">
            <span class="card-title activator grey-text text-darken-1">'. $survey->Title .'<i class="material-icons right">more_vert</i></span>
            <p>'. $survey->Description .'</p>
        </div>

        <div class="card-action">
            <a class="grey-text" id="'. $survey->idGS .'" onclick=""><i class="material-icons right">done</i>Réponse envoyée</a>
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
        having_raw('HOUR(TIMEDIFF(NOW(), BeginAt)) < ?',
        array(180))
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

    }

}
