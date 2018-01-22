<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        return $this->render('home.html.twig');
    }


     /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->render('Back-Office/Mark/Sidebar.html.twig');
    }


}