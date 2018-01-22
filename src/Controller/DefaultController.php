<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLogType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

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
    public function adminHome()
    {
        return $this->render('Back-Office/home-admin.html.twig');
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
            return $this->redirectToRoute("my_museum_session");
        }
        return $this->render('front-office/home-front.html.twig', [
            'formFirstname' => $form->createView(),
        ]);

    }


    /**
     * @Route("/mymuseum/start", name="my_museum_session")
     */

    public function myMuseumSession(SessionInterface $session)
    {
        $this->render('front-office/select-route.html.twig');
    }

    /**
     * @Route("/mymuseum/begin-route", name="begin_route")
     */

    public function beginRoute()
    {
        $this->render('front-office/begin-route.html.twig');
    }

    /**
     * @Route("/mymuseum/end-route", name="end_route")
     */

    public function endRoute()
    {
        $this->render('front-office/end-route.html.twig');
    }

    /**
     * @Route("/mymuseum/newsletter", name="newsletter")
     */

    public function newsletter()
    {
        $this->render('front-office/newsletter.html.twig');
    }


     /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->render('Back-Office/Mark/Sidebar.html.twig');
    }


}