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

        function randomAnswers($array = []) {
            $newArray = [];
            while (count($array)) {
                // takes a rand array elements by its key
                $element = array_rand($array);
                // assign the array and its value to an another array
                $newArray[$element] = $array[$element];
                //delete the element from source array
                unset($array[$element]);
            }
            return $newArray;
        }

        //Formulaire
        $formBuilder = $this->createFormBuilder()
            ->add('answers',ChoiceType::class,[
                'choices' => randomAnswers($jsonFinal),
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
            $answeredQuestions = $session->get('answeredQuestions');
            $answeredQuestions++;
            $session->set('answeredQuestions',$answeredQuestions);

            /*
            if($json['goodAnswer'] == $userAnswer['answers'])
            {
                $session->set('lastQuestion', true);
                $currentReponsePositive = $session->get('correctAnswers');
                $currentReponsePositive++ ;
                $session->set('correctAnswers',$currentReponsePositive);

            } else {
                $session->set('lastQuestion', false);
            }
            */


            if((!(in_array($id,$session->get('visitedMarkArray')))) && ($json['goodAnswer'] == $userAnswer['answers']))
            {
                $visitedMarkArray = $session->get('visitedMarkArray');
                array_push($visitedMarkArray,$id);

                $session->set('visitedMarkArray',$visitedMarkArray);

                $markCount = $session->get('markCount');
                $markCount++;
                $session->set('markCount',$markCount);

                $session->set('lastQuestion', true);
                $currentReponsePositive = $session->get('correctAnswers');
                $currentReponsePositive++ ;
                $session->set('correctAnswers',$currentReponsePositive);

            } else {
                $session->set('lastQuestion', false);

                $this->addFlash(
                    'erreur',
                    'Vous avez déjà répondu à ce quiz.'
                );
            }

            if(($session->get('markCount')) == ($session->get("totalMark"))){
                return $this->redirectToRoute("end_results");
            }

            return $this->redirectToRoute('score_quiz');
        }


        //Affichage de la carte avec l'id
        $mapRespository = $this->getDoctrine()->getRepository(Museum::class);
        $map = $mapRespository->find(1);

        return $this->render('Front-Office/newQuiz.html.twig',[
            'map' => $map->getMap(),
            'question' => $question[0],
            'currentMark' => $currentMark,
            'formQ'=> $formBuilder->createView(),
            'id' => $id,
            'nameRoute' => $session->get('nameRoute'),
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

        return $this->render("/Front-Office/newScoreQuiz.html.twig",[
            'map' => $map,
            'idMark'=> $idMark,
            'message' => $message,
            'progression' => $progression,
            'totalMark' => $session->get('totalMark'),
            'answeredQuestions' => $session->get('answeredQuestions'),
            'correctAnswers' => $session->get('correctAnswers'),
            'nameRoute' => $session->get('nameRoute'),
            'currentMark' => $session->get('currentMark'),
        ]);
    }
}