<?php
/**
 * Created by PhpStorm.
 * User: vankocko
 * Date: 16/01/2018
 * Time: 11:42
 */

namespace App\Controller;

use App\Entity\Mark;
use App\Form\AddMarkAddType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Museum;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class AddMarkController extends  Controller
{
    /**
     * @Route("Mark/AddMark/add", name="AddMark_add")
     */
    public function add(Request $request, SessionInterface $session)
    {
        $form = $this->createForm(AddMarkAddType::class);
        //recuperer la map
        $em=$this->getDoctrine()->getManager();
        $maps=$em->getRepository(Museum::class)->findBy([],['id'=>'desc'],1);

        // Validation du formulaire
        $form->handleRequest($request);
        $museum = $session->get('museum');

        if ($form->isSubmitted() && $form->isValid()) {

            $markSave = $form->getData();
            $markQuestions = $markSave->getQuestions();
            $markDescriptions = $markSave->getDescriptions();

            $file =$markSave->getImage();
            // Générer le nom de fichier
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            // Déplacer le fichier temporaire vers le dossier uploads/

            $markSave->setImage($fileName);

            $file->move($this->getParameter('uploads_directory'), $fileName);




            $em = $this->getDoctrine()->getManager();
            $markSave->setMuseum($museum);
            $em->merge($markSave);
            $em->flush();

            $lastId = $this->getDoctrine()->getRepository(Mark::class)->findBy([],['id'=>'desc'],1);

            foreach($markQuestions as $currentQuestion)
            {
                $jsonAnswer = json_encode($currentQuestion->getAnswers());
                $currentQuestion->setAnswers($jsonAnswer);
                $currentQuestion->setMark($lastId[0]);
                $em = $this->getDoctrine()->getManager();
                $em->persist($currentQuestion);
                $em->flush();
            }

            foreach($markDescriptions as $currentDescription)
            {
                $currentDescription->setMark($lastId[0]);
                $em = $this->getDoctrine()->getManager();
                $em->persist($currentDescription);
                $em->flush();
            }
            return $this->redirectToRoute('mark_create_confirmation');
        }

        return $this->render('Back-Office/Mark/AddMark.html.twig', [
            'formAdd' => $form->createView(),
            'map'=>$maps
        ]);

    }

    /**
     * @Route("AddMark/add/ok", name="mark_create_confirmation")
     */
    public function addOk() 
    {
        return $this->render('Back-Office/Mark/add-Markconfirmation.html.twig'
        );
    }





}