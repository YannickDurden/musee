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
        return new Response("Bienvenue sur le panel admin");
    }

    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function adminHome()
    {
        return new Response("Bienvenue sur le panel admin");
    }
}