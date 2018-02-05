<?php

namespace App\Controller;

use App\Entity\Score;
use App\Entity\User;
use App\Form\AddMapType;
use App\Form\AddMediaType;
use App\Form\MuseumType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class FrontController extends Controller
{
    /**
     * @Route("/mymuseum/end-results", name="end_results")
     */

    public function results(SessionInterface $session)
    {
        //récupération de la durée du parcours
        $endRouteTime = new \DateTime('now');
        $beginRoute = $session->get('startTime');
        $calculateTime = $endRouteTime->diff($beginRoute);

        $route = $session->get('nameRoute');
        $user = $session->get('firstname');
        $score = $session->get('correctAnswers');
        $totalMark = $session->get('totalMark');

        //$route = $session->get('nameRoute');
        //récup variables sessions pour les insérer en DB
        // puis les récupérer de la DB pour les afficher sur le twig
        //récup du score en DB
        //$user = New User();
        //$user->setFirstName($session->get('firstname'));
        //il faudra faire persist et flush de l'utilisateur pour récup en DB

        //$userScore = $this->getDoctrine()->getRepository(Score::class)->find(1);
        return $this->render('Front-Office/end-results.html.twig',[
            'duration' => $calculateTime,
            'nameRoute' => $route,
            'score' => $score,
            'totalMark' => $totalMark,
            'userName' => $user,
        ]);
    }
}