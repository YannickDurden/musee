<?php

namespace App\Controller;

use App\Entity\Description;
use App\Entity\Mark;
use App\Entity\Museum;
use App\Entity\Question;
use App\Form\AddRouteType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Route as r;



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
     * @Route("ajax/route/add", name="ajax_add_BDD")
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
     * @Route("ajax/saveMarkToSession", name="add_mark_session")
     * Créé un objet de type Mark avec les info envoyées et le stock en session
     */
    public function addMarkSession(SessionInterface $session)
    {
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
        $savedMark->setImage("123456.jpeg");


        $sessionMarks []= $session->get('savedMarksNames');

        //dump($sessionMarks);
        //exit();

        // Avant de stocker en session il faut verifier que ça ne soit pas qu'un update d'un repère exisant dans le parcours
        if(!(array_search($savedMark->getName(), $sessionMarks)))
        {
            $sessionMarks [] = $savedMark->getName();
        }
        $session->set('savedMarksNames', $sessionMarks);
        $em = $this->getDoctrine()->getManager();
        $em->persist($savedMark);
        $em->flush();
        return  new Response("Ok");
    }

    /**
     *    CRUD AJAX MARKER
     *
     */


    /**
     * @Route("/Mark/LoadIcon", name="load_icon")
     */
    public function CreateIcon(Request $request)
    {
        $RouteID = $request->request->get('RouteID');
        $routes =$this->getDoctrine()->getManager()->getRepository(r::class)->find($RouteID);
        $dataMark=$routes->getMarks();
        $jsonData = array();
        $idx = 0;
        foreach($dataMark as $mark)
        {
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


    /**
     * @Route("/Mark/UpdateIcon", name="update_icon")
     */
    public function UpdateIcon(Request $request)
    {
        $RouteID = $request->request->get('RouteID');

        $ValMarkId = $request->request->get('ValMarkId');
        $routes =$this->getDoctrine()->getManager()->getRepository(r::class)->find($RouteID);

        $dataMark=$routes->getMarks();
        foreach ($dataMark as $data)
        {
            if($data->getID() == $ValMarkId)
            {
                $temp=array
                (
                    "id" => $data->getId(),
                    "name" => $data->getName(),
                    "CoordinateX" => $data->getCoordinateX(),
                    "CoordinateY"=>$jsonData[]=$data->getCoordinateY()
                );
                return new JsonResponse($temp);
            }
        }
        return new Response('Data Delete');
    }


    /**
     * @Route("/Mark/DeleteIcon", name="delete_icon")
     */
    public function DeleteIcon(Request $request)
    {
        $RouteID = $request->request->get('RouteID');

        $ValMarkId = $request->request->get('ValMarkId');
        $routes =$this->getDoctrine()->getManager()->getRepository(r::class)->find($RouteID);

        $dataMark=$routes->getMarks();
        foreach ($dataMark as $data)
        {
            if($data->getID() == $ValMarkId)
            {
                $temp=array
                       (
                           "id" => $data->getId(),
                           "name" => $data->getName(),
                           "CoordinateX" => $data->getCoordinateX(),
                           "CoordinateY"=>$data->getCoordinateY()
                       );
                return new JsonResponse($temp);
            }
        }
        return new Response('Data Delete');
    }



}
