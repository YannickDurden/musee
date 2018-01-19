<?php

namespace App\Controller;

use App\Entity\Museum;
use App\Form\AddRouteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends Controller
{
    /**
     * @route("/route/add", name="add_route")
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
        return $this->render('Back-office/Route/add.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
        ]);
    }


    /**
     * @route("/route/edit/{id}", name="edit_route")
     */
    public function edit(Request $request, $id)
    {
        $idMuseum = 1;
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);
        $currentRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find($id);
        $form = $this->createForm(AddRouteType::class, $currentRoute);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $object = $form->getData();
            $currentRoute->setName($object->getName());
            $currentRoute->setDuration($object->getDuration());
            $currentRoute->setMarks($object->getMarks());
            $em->flush();

            return new Response("Modif faite");
        }
        return $this->render('Back-Office/Route/add.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
        ]);
    }
}
