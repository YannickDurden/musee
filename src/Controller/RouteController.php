<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Museum;
use App\Entity\User;
use App\Form\AddMarkAddType;
use App\Form\AddRouteType;
use App\Form\DeleteRoutesType;
use App\Form\DeleteMarksType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


class RouteController extends Controller
{
    /**
     * @route("/route/add", name="new_route", methods="GET")
     */
    public function newRoute(Request $request, SessionInterface $session)
    {
        $newRoute = new \App\Entity\Route();
        $form = $this->generateCreateForm($newRoute);
        //$museum = $session->get('museum');

        //dump($museum);
        //exit;

        return $this->render('Back-office/route/add.html.twig', [
            'formAdd' => $form->createView(),
            'newRoute' => $newRoute,
          //  'museum' => $museum
        ]);
    }


    /**
     * @route("/route/add", name="create_route", methods="POST")
     */
    public function createRoute(Request $request, SessionInterface $session)
    {
        $em2 = $this->getDoctrine()->getManager();
        $newRoute = new \App\Entity\Route();
        $museum = $session->get('museum');
        $form = $this->generateCreateForm($newRoute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $currentMuseumId = $museum->getId();
            $newRoute->setMuseum($this->getDoctrine()->getRepository(Museum::class)->find($currentMuseumId));

            $file = $form->get('map')->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );
            $newRoute->setMap($fileName);

            $em2->persist($newRoute);
            $em2->flush();

            return new Response("Insertion faite");
        }
        return $this->render('Back-office/route/add.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
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


    /**
     * @route("/route/edit", name="edit_route")
     */
    public function editAjax(Request $request, SessionInterface $session)
    {
        $museum = $session->get('museum');
        $allRoutes = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findBy(['museum' => $museum->getId()]);
        //Conversion du tableau d'objet en tableau associatif id => nom
        $arrayRoutes = [];
        foreach ($allRoutes as $route)
        {
            $arrayRoutes[$route->getName()] = $route->getId();
        }
        $formBuilder = $this->createFormBuilder()->add('route', ChoiceType::class, [
            'choices' => $arrayRoutes
        ]);
        $form2 = $formBuilder->getForm();
        $form2->handlerequest($request);

        return $this->render('Back-Office/route/update2.html.twig', [

            'formList' => $form2->createView(),
            'museum' => $museum
        ]);
    }

    /**
     * @route("/back-office/route/edit", name="edit_routev2")
     */
    public function editRoutev2(Request $request, SessionInterface $session)
    {
        $em=$this->getDoctrine()->getManager();
        $map=$em->getRepository(Museum::class)->findBy([],['id'=>'desc'], 1);


        //requête ajax pour récupérer les data

        $marks = $this->getDoctrine()
            ->getRepository('App\Entity\Mark')
            ->findAll();

        //dump($marks);
        //exit;

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $jsonData = array();
            $idx = 0;
            foreach($marks as $mark) {
                $temp = array(
                    'id' =>$mark->getId(),
                    'name' => $mark->getName(),
                    'coordinateX'=>$mark->getCoordinateX(),
                    'coordinateY'=>$mark->getCoordinateY(),
                    'image'=>$mark->getImage(),
                );
                $jsonData[$idx++] = $temp;

            }

            return new JsonResponse($jsonData);

        }
        //else
       //{
            //return $this->render('Back-Office/Mark/testAjax.html.twig');
        //}

        $museum = $session->get('museum');

        $allRoutes = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findBy(['museum' => $museum->getId()]);
        $arrayRoutes = [];
        $allMarks = [];

        foreach ($allRoutes as $route) {
            $arrayRoutes[$route->getName()] = $route->getId();
            $marksInRoute = $route->getMarks();
            foreach($marksInRoute as $currentMark)
            {
                if(array_search($currentMark->getName(), $allMarks)=== false)
                {
                    $allMarks[$currentMark->getName()]=['X'=>$currentMark->getCoordinateX(), 'Y'=>$currentMark->getCoordinateY()];
                }
            }
        }
        $formBuilder = $this->createFormBuilder()->add('route', ChoiceType::class, [
            'choices' => $arrayRoutes
        ]);
        $form2 = $formBuilder->getForm();
        $form2->handlerequest($request);
        $formMark = $this->createForm(AddMarkAddType::class);
        $formMark->handleRequest($request);

        return $this->render('Back-Office/BackOffice-v2/base.back-officev2.html.twig', [
            'allMarks' => $allMarks,
            'formList' => $form2->createView(),
            'formMark' => $formMark->createView(),
            'museum' => $museum,
            'map'=>$map
        ]);
    }

        /**
         * @route("/ajax/getMarks", name="getMarks")
         */
    public function getMarks(Request $request, SessionInterface $session)
    {
        $id = $_POST['id'];
        $currentRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find(['id' => $id]);
        $allMarks = $currentRoute->getMarks();
        $arrayMarks = [];

        foreach ($allMarks as $mark) {
            $arrayMarks[$mark->getName()] = $mark->getId();
        }

        return $this->render('Back-Office/BackOffice-v2/mark-table.html.twig', [
            'marks' => $arrayMarks
        ]);
    }

    /**
     * @route("route/list", name="list_routes")
     */
    public function listRoutes(SessionInterface $session)
    {
        $idMuseum = $session->get('museum')->getId();
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);
        $allRoutes = $museum->getRoutes();
        return $this->render('Back-Office/BackOffice-v2/list-routes.html.twig', [
            'allRoutes' => $allRoutes
        ]);
    }


    /**
     * @route("route/delete-routes", name="delete_routes")
     */

    public function deleteRoutes(Request $request, SessionInterface $session)
    {
        $form = $this->createForm( DeleteRoutesType::class
        );
        $form->handlerequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $id = $form->getData();
            $currentRoute= $id['name'];
            //$currentRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($currentRoute);
            $em->flush();
            return new Response('Suppression confirmée');

        }

        //dump($currentRoute);
        //exit;

        return $this->render('Back-Office/BackOffice-v2/delete-routes.html.twig',
            [
                'formRoute' => $form->createView()
            ]);
    }

    /**
     * @route("route/delete-marks", name="delete_marks")
     */

    public function deleteMarks(Request $request, SessionInterface $session)
    {
        $form = $this->createForm( DeleteMarksType::class
        );
        $form->handlerequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $id = $form->getData();
            $currentMark = $id['name'];
            //$currentMark = $this->getDoctrine()->getRepository(\App\Entity\Mark::class)->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($currentMark);
            $em->flush();
            return new Response('Suppression confirmée');

        }

        //dump($currentMark);
        //exit;

        return $this->render('Back-Office/BackOffice-v2/delete-marks.html.twig',
            [
                'formMarks' => $form->createView()
            ]);
    }


}
