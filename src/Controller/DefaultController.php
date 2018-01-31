<?php

namespace App\Controller;

use App\Entity\Museum;
use App\Entity\User;
use App\Form\AddSelectRouteType;
use App\Form\AddDescriptionType;
use App\Form\AddRouteType;
use App\Form\SelectRouteType;
use App\Form\UserRegisterType;
use App\Form\UserLogType;
use App\Repository\RouteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        return new Response("Bienvenue sur le panel admin");
    }
    /**
     * @Route("/admin/home", name="admin_home")
     */

    /*
     * Affiche la HomePage du panel admin et stock en session les infos sur le musée
     */
    public function adminHome(SessionInterface $session)
    {
        $user = $this->getUser();
        $museum = $this->getDoctrine()->getRepository(Museum::class)->findOneBy(['admin' => $user->getId()]);
        $session->set('museum', $museum);
        return $this->render('Back-Office/home-admin.html.twig');
    }

    /**
     * @Route("/testv2", name="test_v2")
     */
    public function testHome()
    {
        return $this->render('Back-Office/BackOffice-v2/base.back-officev2.html.twig');
    }
    /* FONCTIONS FRONT OFFICE */

    /**
     * @Route("/mymuseum", name="my_museum")
     */
    public function myMuseumHome(SessionInterface $session, Request $request)
    {
        $form = $this->createForm(UserLogType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $session->set('firstname', $user->getFirstName());
            $session->set('correctAnswers', 0);
            $session->set('answeredQuestions',0);
            $session->set('lastQuestion',null);
            return $this->redirectToRoute("my_museum_session");
        }
        return $this->render('Front-Office/home-front.html.twig', [
            'formFirstname' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mymuseum/start", name="my_museum_session")
     */
    public function myMuseumSession(SessionInterface $session, Request $request)
    {
        $form = $this->createForm(AddSelectRouteType::class);
        $form->handleRequest($request);
        $user = $session->get('firstname');

        if($form->isSubmitted() && $form->isValid())
        {
            $id = $form->getData();
            $session->set('selectedRoute',$id['route']->getMarks());
            $session->set('nameRoute', $id['route']->getName());
            $session->set('markCount',0);

            $visitedMarkArray = [];
            $session->set('visitedMarkArray',$visitedMarkArray);

            $totalMark = count($id['route']->getMarks());
            $session->set('totalMark', $totalMark);

            return $this->redirectToRoute('begin_route');
        }

        return $this->render('Front-Office/select-route.html.twig', [
            'formSelectRoute' => $form->createView(),
            'userName' => $user
        ]);
    }
    /**
     * @Route("/mymuseum/begin-route", name="begin_route")
     */
    public function beginRoute(SessionInterface $session)
    {
        $map = $this->getDoctrine()->getRepository(Museum::class)->find(1)->getMap();
        $idMark = $session->get('selectedRoute');
        return $this->render('Front-Office/newBeginRoute.html.twig',[
            'idMark' => $idMark,
            'map'=> $map,
            'nameRoute' => $session->get("nameRoute")
        ]);
    }

    /**
     * @Route("/mymuseum/newsletter", name="newsletter")
     */
    public function newsletter(Request $request, \Swift_Mailer $mailer)
    {
        $newUser = $this->createForm(UserRegisterType::class);

        $newUser->handleRequest($request);

        if ($newUser->isSubmitted() && $newUser->isValid()) {
            $register = $newUser->getData();
            $mail = $register->getEmail();

            $em = $this->getDoctrine()->getManager();
            $register->setRole(['ROLE_USER']);
            $register->setUsername($mail);
            $register->setPassword('visiteur');
            $em->persist($register);
            $em->flush();

            $message = (new \Swift_Message('MyMuseum'))
                ->setFrom('mymuseumwf3@gmail.com')
                ->setTo($mail)
                ->setBody(
                    $this->renderView('Front-Office/email.html.twig'),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute('my_museum');
        }

        return $this->render('Front-Office/newsletter.html.twig', [
            'formRegister' => $newUser->createView(),
        ]);
    }

    /**
     * @Route("/mymuseum/admin-ajax/{action}/{param}", name="admin_ajax", methods={"GET", "HEAD"})
     */
    public function ajaxDescription($action, $param)
    {
        /**
         * retourne la description d'un parcours
         */
        if($action == 'getdescription' && $param) {

            $getInfo = $this->getDoctrine()->getRepository(\App\Entity\Route::class);
            $info = $getInfo->find(intval($param));
            $description = $info->getDescription();
            $duration = $info->getDuration();
        }
        return $this->render('Front-Office/ajax.html.twig', [
            'description' => $description,
            'duration' => $duration,
        ]);
    }
}