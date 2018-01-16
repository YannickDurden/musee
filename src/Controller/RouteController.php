<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Form\AddRouteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouteController extends Controller
{
    /**
     * @Route("/route/add", name="add_route")
     */
    public function add(Request $request)
    {
        $form = $this->createForm(AddRouteType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $route = $form->getData();
            $em->persist($route);
            $em->flush();

            return new Response("Insertion faite");
        }
        return $this->render('Route/add.html.twig', [
            'formAdd' => $form->createView(),
        ]);
    }
}
