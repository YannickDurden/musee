<?php

namespace App\Controller;

use App\Entity\Score;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FrontController extends Controller
{
    /**
     * @Route("/mymuseum/end-results", name="end_results")
     */

    public function results(SessionInterface $session)
    {
        //récupération de la durée du parcours (pas sure)
        $dateTime = $session->getMetadataBag()->getLifetime();
        $durate = $session->get('dateTime', $dateTime);
        //$route = $session->get('nameRoute');
        //récup variables sessions pour les insérer en DB
        // puis les récupérer de la DB pour les afficher sur le twig
        //récup du score en DB
        //$user = New User();
        //$user->setFirstName($session->get('firstname'));
        //il faudra faire persist et flush de l'utilisateur pour récup en DB
        $user = $this->getUser();

        $userScore = $this->getDoctrine()->getRepository(Score::class)->find(1);
        return $this->render('Front-Office/end-results.html.twig',[
            'duration' => $durate,
         //   'nameRoute' => $route,
           // 'score' => $userScore->getValue(),
        ]);
    }
}