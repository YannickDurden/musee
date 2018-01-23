<?php
/**
 * Created by PhpStorm.
 * User: yannickfrancois
 * Date: 18/01/2018
 * Time: 13:51
 */

namespace App\Controller;


use App\Entity\Answer;
use App\Entity\Museum;
use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends Controller
{
    /**
     * @Route("/mymuseum/quiz", name="quiz")
     * Affiche la vue du questionnaire
     */
    public function question()
    {
        //Affichage de la question
        $questionRepository = $this->getDoctrine()->getRepository(Question::class);
        $question = $questionRepository->find(1);

        //Affichage de la réponse
        $answerRepository = $this->getDoctrine()->getRepository(Answer::class);
        $answer = $answerRepository->find(1);

        //Affichage de la carte
        $mapRespository = $this->getDoctrine()->getRepository(Museum::class);
        $map = $mapRespository->find(1);

        //Affichage de l'image de l'oeuvre


        return $this->render('Front-Office/quiz.html.twig',[
            'map' => $map->getMap(),
            'question' =>$question,
            'answer' => $answer,
        ]);

    }

    /**
     * @Route("/mymuseum/score-quiz", name="score_quiz")
     * Affiche le score après le questionnaire de l'oeuvre vue
     */
    public function scoreQuiz()
    {
        //Affichage de la carte du musée
        $mapRepository = $this->getDoctrine()->getRepository(Museum::class);
        $map = $mapRepository->find(1);

        return $this->render("/Front-Office/score_quiz.html.twig",[
            'map' => $map->getMap()
        ]);
    }
}