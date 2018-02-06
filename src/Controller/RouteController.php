<?php

namespace App\Controller;
use App\Entity\Mark;
use App\Entity\Museum;
use App\Form\AddMarkAddType;
use App\Form\AddRouteType;
use App\Form\DeleteRoutesType;
use App\Form\DeleteMarksType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class RouteController extends Controller
{
    /**
     * @Route("/route/add", name="new_route", methods="GET")
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
     * @Route("/route/add", name="create_route", methods="POST")
     */
    public function createRoute(Request $request, SessionInterface $session)
    {
        $em2 = $this->getDoctrine()->getManager();
        $newRoute = new \App\Entity\Route();
        $museum = $session->get('museum');
        $form = $this->generateCreateForm($newRoute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/route/edit", name="edit_route")
     */

    public function editAjax(Request $request, SessionInterface $session)
    {
        $museum = $session->get('museum');
        $allRoutes = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findBy(['museum' => $museum->getId()]);
        //Conversion du tableau d'objet en tableau associatif id => nom
        $arrayRoutes = [];
        foreach ($allRoutes as $route) {
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
     * @Route("/back-office/route/edit", name="edit_routev2")
     */
    public function editRoutev2(Request $request, SessionInterface $session)
    {
        //Récuperation en session du musée connecté pour les requètes en BDD

        $museum = $session->get('museum');

        $allRoutes = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findBy(['museum' => $museum->getId()]);
        $arrayRoutes = [];

        //Dans un second temps on récupère la liste complete des repères car en partant de $allRoutes nous n'aurions pas les repères orphelins
        $allMuseumMarks = $this->getDoctrine()->getRepository(Mark::class)->findBy(['museum' =>
            $museum->getId()]);
        $allMarks = [];
        //Création du tableau de session qui se remplira lors de l'ajout d'un repère au parcours par l'user
        $session->set('savedMarksNames', []);

        foreach ($allRoutes as $route) {
            $arrayRoutes[$route->getName()] = $route->getId();
        }
        foreach ($allMuseumMarks as $currentMark)
        {
            //Avant d'ajouter le repère au tableau on verifie que celui ci n'est pas deja présent pour eviter les doublons
            if(array_search($currentMark->getName(), $allMarks)=== false)
            {
                //Ajout au format tableau associatif 'name' => ['X=> coordonnéeX, 'Y'=> coordonnéeY]
                //Les coordonnées sont multipliées par 100 pour les avoir en %
                $currentNameEncode = str_replace(" ","%20", $currentMark->getName());
                $allMarks[$currentNameEncode]=['X'=>($currentMark->getCoordinateX()*100), 'Y'=>($currentMark->getCoordinateY()*100)];
            }
        }

        $formBuilder = $this->createFormBuilder()->add('route', ChoiceType::class, [
            'choices' => $arrayRoutes,
            'placeholder' => 'Choisir le parcours à modifier'
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
        ]);
    }

    /**
     * @Route("route/list", name="list_routes")
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
            $em = $this->getDoctrine()->getManager();
            $em->remove($currentRoute);
            $em->flush();
            return new Response('Suppression confirmée');

        }

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

        return $this->render('Back-Office/BackOffice-v2/delete-marks.html.twig',
            [
                'formMarks' => $form->createView()
            ]);
    }


}



