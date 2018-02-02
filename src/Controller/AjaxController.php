<?php

namespace App\Controller;

use App\Entity\Description;
use App\Entity\Mark;
use App\Entity\Media;
use App\Entity\Museum;
use App\Entity\Question;
use App\Form\AddMarkAddType;
use App\Form\AddRouteType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class AjaxController extends Controller
{
    /**
     * @Route("ajax/edit-route", name="ajax_edit_route")
     */

    public function ajaxEditRoute(Request $request)
    {
        $id = $_POST['id'];
        $currentRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find($id);
        $currentMap = new File('C:\xampp\htdocs\musee\public\uploads\\' . $currentRoute->getMap());
        $currentRoute->setMap($currentMap);
        $form = $this->createForm(AddRouteType::class, $currentRoute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
        $durationArrayToString = strval($arrayObject['add_route']['duration']['hour']) . " " . strval($arrayObject['add_route']['duration']['minute']);
        //$updatedRoute->setMap($_POST['fileName']);
        $duration = \DateTime::createFromFormat('H i', $durationArrayToString);
        $duration = new \DateTime('now');
        $updatedRoute->setDuration($duration);
        $arrayMarks = new ArrayCollection();
        //Boucle permettant de récuperer tout les repères associés a une route pour update les modif de la route
        for ($i = 0; $i < count($arrayObject['add_route']['marks']); $i++) {
            $arrayMarks [] = $this->getDoctrine()->getRepository(Mark::class)->find($arrayObject['add_route']['marks'][$i]);
        }
        $updatedRoute->setMarks($arrayMarks);
        $em = $this->getDoctrine()->getManager();
        $em->merge($updatedRoute);
        $em->flush();

        return new Response("Modif effectuée");
    }

    /**
     * @route("ajax/saveMarkToSession", name="add_mark_session")
     * Créé un objet de type Mark avec les info envoyées et le stock en session
     */
    public function addMarkSession(SessionInterface $session)
    {
        parse_str($_POST['markInfo'], $decodedJson);
        if ($_POST['update'] === 'false') {
            $savedMark = new Mark();
        } else {
            $savedMark = $this->getDoctrine()->getRepository(Mark::class)->findOneBy(['name' => $_POST['update']]);
            $arrayMarks = $session->get('savedMarksNames');
            foreach ($arrayMarks as $key => $currentMark) {
                if ($currentMark = $_POST['update']) {
                    array_splice($arrayMarks,$key,1);
                }
            }
            $session->set('savedMarksNames', $arrayMarks);
        }

        $savedMark->setMuseum($this->getDoctrine()->getRepository(Museum::class)->find($session->get('museum')->getId()));
        $savedMark->setName($decodedJson['add_mark_add']['name']);
        $savedMark->setCoordinateX($decodedJson['add_mark_add']['coordinateX']);
        $savedMark->setCoordinateY($decodedJson['add_mark_add']['coordinateY']);
        $questions = new ArrayCollection();
        $descriptions = new ArrayCollection();
        foreach ($decodedJson['add_mark_add']['questions'] as $question) {
            $currentQuestion = new Question();
            $currentQuestion->setLabel($question['label']);
            if ($question['category'] == 1) {
                $category = 'adulte';
            } else {
                $category = 'enfant';
            }
            $currentQuestion->setCategory($category);
            $currentQuestion->setAnswers(json_encode($question['answers']));
            $currentQuestion->setMark($savedMark);
            $questions [] = $currentQuestion;
        }
        foreach ($decodedJson['add_mark_add']['descriptions'] as $description) {
            $currentDescription = new Description();
            if ($description['category'] == 1) {
                $category = 'adulte';
            } else {
                $category = 'enfant';
            }
            $currentDescription->setCategory($category);
            $currentDescription->setLabel($description['label']);
            $currentDescription->setMark($savedMark);
            $descriptions [] = $currentDescription;
        }
        $savedMark->setDescriptions($descriptions);
        $savedMark->setQuestions($questions);
        $savedMark->setMedias($this->getDoctrine()->getRepository(Media::class)->find($decodedJson['add_mark_add']['medias']));
        $savedMark->setImage('123456.jpeg');
        $sessionMarks = $session->get('savedMarksNames');
        // Avant de stocker en session il faut verifier que ça ne soit pas qu'un update d'un repère exisant dans le parcours
        if (!(in_array($savedMark->getName(), $sessionMarks))) {
            $sessionMarks [] = $savedMark->getName();
        }
        $session->set('savedMarksNames', $sessionMarks);
        print_r($session->get('savedMarksNames'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($savedMark);
        $em->flush();
        return new Response("Ok");
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
        $newRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findOneBy(['name' => $_POST['name']]);
        if ($newRoute == null) {
            $newRouteToSave = new \App\Entity\Route();
        } else {
            $newRouteToSave = $newRoute;
        }
        $newRouteToSave->setName($decodedJson['name']);
        $newRouteToSave->setDescription($decodedJson['description']);
        $durationArrayToString = strval($decodedJson['hours']) . ":" . strval($decodedJson['minutes']);
        print_r($durationArrayToString);
        //$updatedRoute->setMap($_POST['fileName']);
        $duration = \DateTime::createFromFormat('G:i', $durationArrayToString);
        var_dump($duration);
        //$duration = new \DateTime('now');
        $newRouteToSave->setDuration($duration);
        foreach ($session->get('savedMarksNames') as $mark) {
            $arrayMarks [] = $this->getDoctrine()->getRepository(Mark::class)->findOneBy(['name' => $mark]);
        }
        $newRouteToSave->setMarks($arrayMarks);
        $newRouteToSave->setMap('123456.jpeg');
        $newRouteToSave->setMuseum($this->getDoctrine()->getRepository(Museum::class)->find($session->get('museum')->getId()));

        $em = $this->getDoctrine()->getManager();
        $em->persist($newRouteToSave);
        $em->flush();

        //On reinitialise le tableau en session pour permettre l'ajout d'un nouveau parcours
        $session->set('savedMarksNames', []);

        return new Response("Ok");

    }

    /**
     * @route("ajax/getMarkInfo", name="get_mark_info")
     */
    public function getMarkInfo()
    {
        $name = $_POST['name'];
        $selectedMark = $this->getDoctrine()->getRepository(Mark::class)->findOneBy(['name' => $name]);
        $jsonReturn = [];
        $jsonReturn['name'] = $selectedMark->getName();
        $jsonReturn['coordinateX'] = $selectedMark->getCoordinateX();
        $jsonReturn['coordinateY'] = $selectedMark->getCoordinateY();
        $jsonReturn['medias'] = $selectedMark->getMedias()->getId();
        $description1 = $selectedMark->getDescriptions()[0];
        $description2 = $selectedMark->getDescriptions()[1];
        if ($description1->getCategory() == 'adulte') {
            $jsonReturn['description1']['category'] = 1;
            $jsonReturn['description2']['category'] = 2;
        } else {
            $jsonReturn['description1']['category'] = 2;
            $jsonReturn['description2']['category'] = 1;
        }
        $jsonReturn['description1']['label'] = $description1->getLabel();
        $jsonReturn['description2']['label'] = $description2->getLabel();

        $question1 = $selectedMark->getQuestions()[0];
        $question2 = $selectedMark->getQuestions()[1];
        if ($question1->getCategory() == 'adulte') {
            $jsonReturn['question1']['category'] = 1;
            $jsonReturn['question2']['category'] = 2;
        } else {
            $jsonReturn['question1']['category'] = 2;
            $jsonReturn['question2']['category'] = 1;
        }
        $jsonReturn['question1']['label'] = $question1->getLabel();
        $jsonReturn['question2']['label'] = $question2->getLabel();
        $jsonReturn['question1']['answers'] = json_decode($question1->getAnswers());
        $jsonReturn['question2']['answers'] = json_decode($question2->getAnswers());
        return new JsonResponse($jsonReturn);
    }

    /**
     * @route("ajax/deleteMarkFromSession", name="delete_mark_form_session")
     */
    public function deleteMarkSession(SessionInterface $session)
    {
        $name = $_POST['name'];
        $arrayMarks = $session->get('savedMarksNames');

        foreach ($arrayMarks as $key => $currentMark) {
            if ($currentMark == $name) {
                array_splice($arrayMarks,$key,1);
                break;
            }
        }
        $session->set('savedMarksNames', $arrayMarks);
        print_r($session->get('savedMarksNames'));
        return new Response("Done");

    }

    /**
     * @route("/ajax/getMarks", name="getMarks")
     */
    public function getMarks(Request $request, SessionInterface $session)
    {
        $name = $_POST['name'];
        $currentRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->findOneBy(['name' => $name]);
        $allMarks = $currentRoute->getMarks();
        $duration = $currentRoute->getDuration();
        $arrayMarks = [];
        $arrayNames = [];

        foreach ($allMarks as $mark) {
            $arrayMarks[$mark->getName()] = $mark->getId();
            $arrayNames [] = $mark->getName();
        }
        $session->set('savedMarksNames', $arrayNames);
        print_r($arrayNames);
        return $this->render('Back-Office/BackOffice-v2/mark-table.html.twig', [
            'marks' => $arrayMarks,
            'route' => $currentRoute,
            'duration' => $duration
        ]);
    }
}
