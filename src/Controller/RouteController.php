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
     * @Route("/admin/route/edit", name="edit_routev2")
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
     * @Route("/admin/route/list", name="list_routes")
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
     * @route("/admin/route/delete-routes", name="delete_routes")
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
     * @route("/admin/route/delete-marks", name="delete_marks")
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



