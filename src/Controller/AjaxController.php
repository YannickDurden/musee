<?php

namespace App\Controller;

use App\Entity\Description;
use App\Entity\Mark;
use App\Entity\Museum;
use App\Entity\Question;
use App\Form\AddMarkAddType;
use App\Form\AddRouteType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax", name="ajax")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @Route("ajax/edit-route", name="ajax_edit_route")
     */

    /*
     * Route permettant
     */
    public function ajaxEditRoute(Request $request)
    {
        $id = $_POST['id'];
        $currentRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find($id);
        $currentMap = new File('C:\xampp\htdocs\musee\public\uploads\\'.$currentRoute->getMap());
        $currentRoute->setMap($currentMap);
        $form = $this->createForm(AddRouteType::class, $currentRoute);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            return new Response("OK");

            $updatedRoute = $currentRoute;
            $em = $this->getDoctrine()->getManager();
            $em->persist($updatedRoute);
            $em->flush();

        }
        return $this->render('Back-Office/Route/AddRoute.html.twig', [
            'formAdd' => $form->createView()
        ]);
    }

    /**
     * @route("ajax/route/add", name="ajax_add_BDD")
     */

    /*
     * Route permettant d'ajouter en BDD une route via AJAX
     */
    public function addAjaxBdd()
    {
        //Décompose le json recu en tableau
        parse_str($_POST['form'], $arrayObject);

        //Recuperation de la route en BDD et mise à jour des valeurs
        $updatedRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find($_POST['id']);
        $updatedRoute->setName($arrayObject['add_route']['name']);
        $updatedRoute->setDescription($arrayObject['add_route']['description']);
        $durationArrayToString = $arrayObject['add_route']['duration']['hour']." ".$arrayObject['add_route']['duration']['minute'];
        //$updatedRoute->setMap($_POST['fileName']);
        $duration = \DateTime::createFromFormat('H i', $durationArrayToString);
        $duration = new \DateTime('now');
        $updatedRoute->setDuration($duration);
        $arrayMarks = new ArrayCollection();
        //Boucle permettant de récuperer tout les repères associés a une route pour update les modif de la route
        for($i=0; $i<count($arrayObject['add_route']['marks']); $i++)
        {
            $arrayMarks []= $this->getDoctrine()->getRepository(Mark::class)->find($arrayObject['add_route']['marks'][$i]);
        }
        $updatedRoute->setMarks($arrayMarks);
        $em =$this->getDoctrine()->getManager();
        $em->merge($updatedRoute);
        $em->flush();

        return new Response("Modif effectuée");
    }

    /**
     * @route("ajax/saveMarkToSession", name="add_mark_session")
     * Créé un objet de type Mark avec les info envoyées et le stock en session
     */
    public function addMarkSession(SessionInterface $session, Request $request)
    {
        /*
        $newMark = new Mark();
        $form = $this->createForm(AddMarkAddType::class, $newMark);
        $form->handleRequest($request);

        $newMark = $form->getData();
        print_r($newMark);
        exit;

        $file =$newMark->getImage();
        // Générer le nom de fichier
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        // Déplacer le fichier temporaire vers le dossier uploads/
        $newMark->setImage($fileName);
        $file->move($this->getParameter('uploads_directory'), $fileName);
        $em = $this->getDoctrine()->getManager();
        $em->persist($newMark);
        $em->flush();
        return  new Response("Ok");
        */




        parse_str($_POST['markInfo'], $decodedJson);

        $savedMark = new Mark();
        $savedMark->setMuseum($this->getDoctrine()->getRepository(Museum::class)->find($session->get('museum')->getId()));
        $savedMark->setName($decodedJson['add_mark_add']['name']);
        $savedMark->setCoordinateX($decodedJson['add_mark_add']['coordinateX']);
        $savedMark->setCoordinateY($decodedJson['add_mark_add']['coordinateY']);
        $questions = new ArrayCollection();
        $descriptions = new ArrayCollection();
        foreach($decodedJson['add_mark_add']['questions'] as $question)
        {
            $currentQuestion = new Question();
            $currentQuestion->setLabel($question['label']);
            if($question['category'] == 1)
            {
                $category = 'adulte';
            }
            else
            {
                $category = 'enfant';
            }
            $currentQuestion->setCategory($category);
            $currentQuestion->setAnswers(json_encode($question['answers']));
            $currentQuestion->setMark($savedMark);
            $questions []=$currentQuestion;
        }
        foreach($decodedJson['add_mark_add']['descriptions'] as $description)
        {
            $currentDescription = new Description();
            if($description['category'] == 1)
            {
                $category = 'adulte';
            }
            else
            {
                $category = 'enfant';
            }
            $currentDescription->setCategory($category);
            $currentDescription->setLabel($description['label']);
            $currentDescription->setMark($savedMark);
            $descriptions []= $currentDescription;
        }
        $savedMark->setDescriptions($descriptions);
        $savedMark->setQuestions($questions);
        $savedMark->setImage('123456.jpeg');
        //$savedMark->setImage($decodedJson['add_mark_add']['image']);
        //$file =$savedMark->getImage();
        // Générer le nom de fichier
        //$fileName = md5(uniqid()) . '.' . $file->guessExtension();
        // Déplacer le fichier temporaire vers le dossier uploads/
        //$savedMark->setImage($fileName);
        //$file->move($this->getParameter('uploads_directory'), $fileName);
        $sessionMarks = $session->get('savedMarksNames');
        if(empty($sessionMarks))
        {
            $session->set('savedMarksNames', []);
            $sessionMarks = [];
        }
        // Avant de stocker en session il faut verifier que ça ne soit pas qu'un update d'un repère exisant dans le parcours
        if(!(in_array($savedMark->getName(), $sessionMarks)))
        {
            $sessionMarks []= $savedMark->getName();
        }
        $session->set('savedMarksNames', $sessionMarks);
        print_r($session->get('savedMarksNames'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($savedMark);
        $em->flush();
        return  new Response("Ok");
    }

    /**
     * @route("ajax/saveRoutetoBDD", name="add_route_to_BDD")
     */
    public function addRouteToBDD(Request $request, SessionInterface $session)
    {
        /*
        $newRoute = new \App\Entity\Route();
        $form = $this->createForm(AddRouteType::class, $newRoute);
        $form->handleRequest($request);
        $newRouteToSave = $form->getData();
        */
        parse_str($_POST['routeInfo'], $decodedJson);
        $arrayMarks = [];
        $newRouteToSave = new \App\Entity\Route();
        $newRouteToSave->setName($decodedJson['name']);
        $newRouteToSave->setDescription($decodedJson['description']);
        $durationArrayToString = $decodedJson['hours']." ".$decodedJson['minutes'];
        //$updatedRoute->setMap($_POST['fileName']);
        $duration = new \DateTime();
        $duration->createFromFormat('H i', $durationArrayToString);
        //$duration = new \DateTime('now');
        $newRouteToSave->setDuration($duration);
        foreach($session->get('savedMarksNames') as $mark)
        {
            $arrayMarks [] = $this->getDoctrine()->getRepository(Mark::class)->findOneBy(['name'=>$mark]);
        }
        $newRouteToSave->setMarks($arrayMarks);
        $newRouteToSave->setMap('123456.jpeg');

        $em = $this->getDoctrine()->getManager();
        $em->persist($newRouteToSave);
        $em->flush();

        return new Response("Ok");

    }
}
