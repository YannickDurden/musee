<?php

namespace App\Controller;

use App\Entity\Museum;
use App\Entity\User;
use App\Form\AddDescriptionType;
use App\Form\AddRouteType;
use App\Form\SelectRouteType;
use App\Form\UserRegisterType;
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
    public function adminHome(SessionInterface $session)
    {
        $user = $this->getUser();
        $museum = $this->getDoctrine()->getRepository(Museum::class)->findOneBy(['admin' => $user->getId()]);
        $session->set('museum', $museum);
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
        return $this->render('Front-Office/home-front.html.twig', [
            'formFirstname' => $form->createView(),
        ]);

    }

    /**
     * @Route("/mymuseum/start", name="my_museum_session")
     */
    public function myMuseumSession(SessionInterface $session, Request $request)
    {
        $form = $this->createForm(SelectRouteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentRoute = $form->getData();
            return $this->render('Front-Office/select-route.html.twig', [
                'formRoute' => $form->createView(),
                'description' => $currentRoute['route']->getDescription(),
            ]);
        }

        return $this->render('Front-Office/select-route.html.twig', [
            'formRoute' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mymuseum/begin-route", name="begin_route")
     */
    public function beginRoute()
    {
        return $this->render('Front-Office/begin-route.html.twig');
    }

    /**
     * @Route("/mymuseum/end-route", name="end_route")
     */
    public function endRoute()
    {
        return $this->render('Front-Office/end-route.html.twig');
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
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->render('Back-Office/Mark/Sidebar.html.twig');
    }


}