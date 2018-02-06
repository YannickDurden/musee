<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Score;
use App\Entity\User;
use App\Form\AddMapType;
use App\Form\AddMediaType;
use App\Form\MuseumType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FrontController extends Controller
{
    /**
     * @route("/mymuseum/end-results", name="end_results")
     */

    public function results(SessionInterface $session)
    {
        //récupération de la durée du parcours
        $endRouteTime = new \DateTime('now');
        $beginRoute = $session->get('startTime');
        $calculateTime = $endRouteTime->diff($beginRoute);

        //reste infos : nom parcours, nom user, score total
        $route = $session->get('nameRoute');
        $user = $session->get('firstname');
        $score = $session->get('correctAnswers');
        $totalMark = $session->get('totalMark');

        return $this->render('Front-Office/end-results.html.twig',[
            'duration' => $calculateTime,
            'nameRoute' => $route,
            'score' => $score,
            'totalMark' => $totalMark,
            'userName' => $user,
        ]);
    }
}