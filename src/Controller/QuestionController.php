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
     * @Route("/mymuseum/quiz/{id}", name="quiz")
     * Affiche la vue du questionnaire
     */
    public function question(SessionInterface $session,Request $request, $id)
    {
        //Récupération de l'image de l'oeuvre
        $repository = $this->getDoctrine()->getRepository(Mark::class);
        $currentMark = $repository->find($id);

        $question = $currentMark->getQuestions();

        //décodage du json pour envoyer les réponses dans la vue
        $answers = $question[0]->getAnswers();
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
            /*$answer = new Answer();
            $answer->setValue($userAnswer['answers']);
            $answer->setQuestion($question[0]);*/

            $answeredQuestions = $session->get('answeredQuestions');
            $answeredQuestions++;
            $session->set('answeredQuestions',$answeredQuestions);

            if($json['goodAnswer'] == $userAnswer['answers'])
            {
                //$answer->setCorrect(true);
                $session->set('lastQuestion', true);
                $currentReponsePositive = $session->get('correctAnswers');
                $currentReponsePositive++ ;
                $session->set('correctAnswers',$currentReponsePositive);

            } else {
                //$answer->setCorrect(false);
                $session->set('lastQuestion', false);
            }

            /*$em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();*/

            if(!(array_search($id,$session->get('visitedMarkArray'))))
            {
                $visitedMarkArray = $session->get('visitedMarkArray');
                array_push($visitedMarkArray,$id);
                $session->set('visitedMarkArray',$visitedMarkArray);
                $markCount = $session->get('markCount');
                $markCount++;
                $session->set('markCount',$markCount);
            }

            if(($session->get('markCount')) == ($session->get("totalMark"))){
                return $this->redirectToRoute("end_results");
            }

            return $this->redirectToRoute('score_quiz');
        }


        //Affichage de la carte avec l'id
        $mapRespository = $this->getDoctrine()->getRepository(Museum::class);
        $map = $mapRespository->find(1);

        return $this->render('Front-Office/quiz.html.twig',[
            'map' => $map->getMap(),
            'question' => $question[0],
            'currentMark' => $currentMark,
            'formQ'=> $formBuilder->createView(),
            'id' => $id,
        ]);

    }

    /**
     * @Route("/mymuseum/score-quiz", name="score_quiz")
     * Affiche le score après le questionnaire de l'oeuvre vue
     */
    public function scoreQuiz(SessionInterface $session)
    {
        $map = $this->getDoctrine()->getRepository(Museum::class)->find(1)->getMap();
        $idMark = $session->get('selectedRoute');

        //Affichage du score
        if($session->get('lastQuestion') == true )
        {
            $message = 'Vous avez trouvé la bonne réponse !';

        } else {
            $message = 'Dommage, ce n\'était pas la bonne réponse';
        }

        $progression = (($session->get('answeredQuestions')) / ($session->get('totalMark'))) * 100;

        return $this->render("/Front-Office/score_quiz.html.twig",[
            'map' => $map,
            'idMark'=> $idMark,
            'message' => $message,
            'progression' => $progression,
            'answeredQuestions' => $session->get('answeredQuestions'),
            'score' => $session->get('correctAnswers'),
        ]);
    }
}