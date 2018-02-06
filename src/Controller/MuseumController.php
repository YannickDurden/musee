<?php

namespace App\Controller;

use App\Entity\Museum;
use App\Form\AddMapType;
use App\Form\EditMuseumType;
use App\Form\MuseumType;
use App\Form\UserLogType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MuseumController extends Controller
{
    /**
     * @route("/museum", name="museum")
     */
    public function index()
    {
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }
     
    /**
     * @route("/museum/map", name="add_map")
     */
    public function addMap(Request $request, SessionInterface $session)
    {
        $form = $this->createForm(AddMapType::class);
        $form->handleRequest($request);

        //Penser à faire le cas ou aucun musée en BDD car setMap  sur null impossible
        $museum = $session->get('museum');

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Déplacement du fichier
            // $file contient le fichier uploadé, il est de type Symfony\Component\HttpFoundation\File\UploadedFile
            $file = $form->get('map')->getData();
            // Génération d'un nom aléatoire
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Déplacement du fichier dans un dossier paramétré à l'avance
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            $museum->setMap($fileName);
            $em->merge($museum);
            $em->flush();
        }
        return $this->render('Back-Office/museum/add-map.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
        ]);
    }

    /**
     * @Route("/museum/info", name="edit_info")
     */

    /*
     * Gère l'affichage du formulaire de modification des infos du musée et sa soumission
     */
    public function editInfo(Request $request, SessionInterface $session)
    {
        $museum = $session->get('museum');
        $form = $this->createForm(EditMuseumType::class, $museum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($museum);
            $em->flush();

            return new Response("Modif faite");
        }

        return $this->render('Back-office/museum/edit-info.html.twig', [
            'formEdit' => $form->createView()
        ]);
    }

    /**
     * @Route("/back-office/museum/edit", name="museum_edit")
     */

    public function museumEdit(Request $request, SessionInterface $session)
    {

        /*
         * Gère l'affichage du formulaire de modification des infos du musée et sa soumission
         */
        $museum = $session->get('museum');
        $form = $this->createForm(MuseumType::class, $museum);
        $form->handleRequest($request);
        $formMap = $this->createForm(AddMapType::class);
        $formMap->handleRequest($request);

        if ($formMap->isSubmitted() && $formMap->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            // Déplacement du fichier
            // $file contient le fichier uploadé, il est de type Symfony\Component\HttpFoundation\File\UploadedFile
            $file = $formMap->getData()->getMap();
            // Génération d'un nom aléatoire
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Déplacement du fichier dans un dossier paramétré à l'avance
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            $museum->setMap($fileName);

            $em->merge($museum);
            $em->flush();

            //addFlash ne fonctionne pas: faire afficher un message sur la page existante "modif enregistrées"
            //$this->addFlash('notice', 'Vos modifications ont bien été enregistrées.');
        }

        if($form->isSubmitted() && $form->isValid())
        {
            $museum = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->merge($museum);
            $em->flush();

            return new Response('okkkk');
        }

        return $this->render('Back-Office/BackOffice-v2/museum-edit.html.twig', [
            'formEditMuseum' => $form->createView(),
            'formAdd' => $formMap->createView(),
            'museum' => $museum
        ]);

    }

}
