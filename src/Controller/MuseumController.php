<?php

namespace App\Controller;

use App\Entity\Museum;
use App\Form\AddMapType;
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
    public function addMap(Request $request)
    {
        $idMuseum = 1;
        $form = $this->createForm(AddMapType::class);
        $form->handleRequest($request);

        //Penser à faire le cas ou aucun musée en BDD car setMap  sur null impossible
        $museum = $this->getDoctrine()->getRepository(Museum::class)->find($idMuseum);

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
            $em->flush();
        }
        return $this->render('Back-Office/museum/add-map.html.twig', [
            'formAdd' => $form->createView(),
            'museum' => $museum
        ]);
    }
}
