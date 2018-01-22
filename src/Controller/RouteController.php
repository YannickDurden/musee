<?php

namespace App\Controller;

use App\Entity\Museum;
use App\Form\AddRouteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends Controller
{
    /**
     * @route("/route/add", name="new_route", methods="GET")
     */
    public function newRoute(Request $request)
    {
        $idMuseum = 1;
        $newRoute = new \App\Entity\Route();
        $form = $this->generateCreateForm($newRoute);
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);


        return $this->render('Back-office/route/add.html.twig', [
            'formAdd' => $form->createView(),
            'newRoute' => $newRoute,
            'museum' => $museum
        ]);
    }


    /**
     * @route("/route/add", name="create_route", methods="POST")
     */
    public function createRoute(Request $request, $id= null)
    {

        $newRoute = new \App\Entity\Route();
        $idMuseum = 1;
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);
        $form = $this->generateCreateForm($newRoute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newRoute);
            $em->flush();

            return new Response("Insertion faite");
        }
        return $this->render('Back-office/route/add.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
        ]);
    }

    /**
     * @route("/route/edit/{id}", defaults={"id"=1}, name="edit_route")
     */
    public function editPost(Request $request, $id)
    {

        $idMuseum = 1;
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);
        $allRoutes = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findBy(['museum' => $idMuseum]);
        $currentRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find($id);
        $form = $this->createForm(AddRouteType::class, $currentRoute);
        $form->handleRequest($request);


        //Conversion du tableau d'objet en tableau associatif id => nom
        $arrayRoutes = [];
        foreach ($allRoutes as $route)
        {
            $arrayRoutes[$route->getName()] = $route->getId();
        }
        $formBuilder = $this->createFormBuilder()->add('route', ChoiceType::class, [
            'choices' => $arrayRoutes
        ]);
        $formBuilder->add('Modifier', SubmitType::class);
        $form2 = $formBuilder->getForm();
        $form2->handlerequest($request);

        if($form2->isSubmitted() && $form2->isValid())
        {
            $data = $form2->getData();
            return $this->redirect($this->generateUrl('edit_route', ['id' => $data['route']]));
        }
        elseif($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $object = $form->getData();
            $currentRoute->setName($object->getName());
            $currentRoute->setDuration($object->getDuration());
            $currentRoute->setMarks($object->getMarks());
            $em->flush();
            return $this->render('Back-Office/modif-success.html.twig');
        }

        return $this->render('Back-Office/route/update.html.twig', [
            'formAdd' => $form->createView(),
            'formList' => $form2->createView(),
            'museum' => $museum
        ]);


    }


    /**
     * @route("/route/list", name="list_route")
     */
    public function list(Request $request)
    {

        $idMuseum = 1;
        $allRoutes = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findBy(['museum' => $idMuseum]);
        //Conversion du tableau d'objet en tableau associatif id => nom
        $arrayRoutes = [];
        foreach ($allRoutes as $currentRoute)
        {
            $arrayRoutes[$currentRoute->getName()] = $currentRoute->getId();
        }
        $formBuilder = $this->createFormBuilder()->add('route', ChoiceType::class, [
            'choices' => $arrayRoutes
        ]);
        $formBuilder->add('Modifier', SubmitType::class);
        $form = $formBuilder->getForm();
        $form->handlerequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            return $this->redirectToRoute('edit_route', ['id' => $data['route']]);
        }
        return $this->render('Back-Office/route/list.html.twig', [
            'formList' => $form->createView(),
        ]);
    }


    private function generateCreateForm(\APP\Entity\Route $newRoute)
    {
        $form = $this->createForm(AddRouteType::class, $newRoute, [
            'action' => $this->generateUrl('create_route'),
            'method' => 'POST'
        ]);
        return $form;
    }
}
