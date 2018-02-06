<?php

namespace App\Controller;

use App\Form\AddMediaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MediaController extends Controller
{
    /**
     * @Route("/admin/media", name="media_edit")
     */
    public function media(Request $request, SessionInterface $session)
    {
        //creation formulaire
        $form = $this->createForm(AddMediaType::class);
        $form->handleRequest($request);

        // générer preview de l'image si form valid et soumis
        if ($form->isSubmitted() && $form->isValid())
        {
            $media = $form->getData();

            $em = $this->getDoctrine()->getManager();
            // Déplacement du fichier
            // $file contient le fichier uploadé, il est de type Symfony\Component\HttpFoundation\File\UploadedFile
            $file = $form->getData()->getFile();
            // Génération d'un nom aléatoire
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Déplacement du fichier dans un dossier paramétré à l'avance
            $file->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            $media->setFile($fileName);

            $em->persist($media);
            $em->flush();


        }

        $currentMedia = $this->getDoctrine()->getRepository(\App\Entity\Media::class)->findBy([], ['id' => 'DESC'], 1);

        return $this->render('Back-Office/BackOffice-v2/media.html.twig', [
            'formMedia' => $form->createView(),
            'currentMedia' => $currentMedia[0],
        ]);
    }
}
