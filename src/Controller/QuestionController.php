<?php
/**
 * Created by PhpStorm.
 * User: yannickfrancois
 * Date: 18/01/2018
 * Time: 13:51
 */

namespace App\Controller;


use App\Entity\Answer;
use App\Entity\Mark;
use App\Entity\Museum;
use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends Controller
{
    /**
     * @Route("/mymuseum/quiz", name="quiz")
     * Affiche la vue du questionnaire
     */
    public function question(SessionInterface $session,Request $request)
    {
        //Récupération des questions et réponses
        $questionRepository = $this->getDoctrine()->getRepository(Question::class);
        $question = $questionRepository->find(2);

        //Récupération de l'image de l'oeuvre
        $repository = $this->getDoctrine()->getRepository(Mark::class);
        $currentMark = $repository->find(2);

        //décodage du json pour envoyer les réponses dans la vue
        $answers = $question->getAnswers();
        $json = json_decode($answers,true);

        $jsonFinal = [];
        foreach ($json as $a){
           $jsonFinal[$a] = $a;
        }

        //Formulaire
        $formBuilder = $this->createFormBuilder()
            ->add('answers',ChoiceType::class,[
                'choices' => $jsonFinal,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class,
                ['label' => 'Valider'])
            ->getForm();

        $formBuilder->handleRequest($request);

        if($formBuilder->isSubmitted() && $formBuilder->isValid())
        {
            $userAnswer = $formBuilder->getData();
            $answer = new Answer();
            $answer->setValue($userAnswer['answers']);
            $answer->setQuestion($question);

            $answeredQuestions = $session->get('answeredQuestions');
            $answeredQuestions++;
            $session->set('answeredQuestions',$answeredQuestions);

            if($json['goodAnswer'] == $userAnswer['answers'])
            {
                $answer->setCorrect(true);
                $session->set('lastQuestion', true);
                $currentReponsePositive = $session->get('correctAnswers');
                $currentReponsePositive++ ;
                $session->set('correctAnswers',$currentReponsePositive);

            } else {
                $answer->setCorrect(false);
                $session->set('lastQuestion', false);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();


            $routeArray = [];
            array_push($routeArray,[]);
            $session->set('routeArray',$routeArray);

        }

        //Affichage de la carte avec l'id 1
        $mapRespository = $this->getDoctrine()->getRepository(Museum::class);
        $map = $mapRespository->find(1);

        return $this->render('Front-Office/quiz.html.twig',[
            'map' => $map->getMap(),
            'question' => $question,
            'currentMark' => $currentMark,
            'formQ'=> $formBuilder->createView(),
        ]);

    }

    /**
     * @Route("/mymuseum/score-quiz", name="score_quiz")
     * Affiche le score après le questionnaire de l'oeuvre vue
     */
    public function scoreQuiz(SessionInterface $session)
    {
        //Affichage de la carte du musée
        $mapRepository = $this->getDoctrine()->getRepository(Museum::class);
        $map = $mapRepository->find(1);

        //Affichage du score
        if($session->get('lastQuestion') == true )
        {
            $message = 'Vous avez trouvé la bonne réponse !';

        } else {
            $message = 'Dommage, ce n\'était pas la bonne réponse';
        }


        return $this->render("/Front-Office/score_quiz.html.twig",[
            'map' => $map->getMap(),
            'message' => $message,
            'progression' => $session->get('answeredQuestions'),
            'score' => $session->get('correctAnswers'),
        ]);
    }
}