<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Museum;
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
        $idMuseum = 1;
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);
        $form = $this->createForm(AddRouteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $route = $form->getData();
            $em->persist($route);
            $em->flush();

            return new Response("Insertion faite");
        }
        return $this->render('Route/add.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
        ]);
    }


        /**
         * @Route("/route/edit/{id}", name="edit_route")
         */
    public function edit(Request $request, $id)
    {
        $idMuseum = 1;
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);
        $form = $this->createForm(AddRouteType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository(\App\Entity\Route::class);
            $currentRoute = $repository->find($id);
            $object = $form->getData();
            $currentRoute->setName($object->getName());
            $currentRoute->setDuration($object->getDuration());
            $currentRoute->setCategory($object->getCategory());
            $currentRoute->setMarks($object->getMarks());
            $em->flush();

            return new Response("Modif faite");
        }
        return $this->render('Route/add.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
        ]);
    }
}
