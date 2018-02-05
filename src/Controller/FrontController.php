<?php

namespace App\Controller;

use App\Entity\Score;
use App\Entity\User;
use App\Form\AddMapType;
use App\Form\AddMediaType;
use App\Form\MuseumType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class FrontController extends Controller
{
    /**
     * @Route("/mymuseum/end-results", name="end_results")
     */

    public function results(SessionInterface $session)
    {
        //récupération de la durée du parcours
        $endRouteTime = new \DateTime('now');
        $beginRoute = $session->get('startTime');
        $calculateTime = $endRouteTime->diff($beginRoute);

        $route = $session->get('nameRoute');
        $user = $session->get('firstname');
        $score = $session->get('correctAnswers');
        $totalMark = $session->get('totalMark');

        //$route = $session->get('nameRoute');
        //récup variables sessions pour les insérer en DB
        // puis les récupérer de la DB pour les afficher sur le twig
        //récup du score en DB
        //$user = New User();
        //$user->setFirstName($session->get('firstname'));
        //il faudra faire persist et flush de l'utilisateur pour récup en DB

        //$userScore = $this->getDoctrine()->getRepository(Score::class)->find(1);
        return $this->render('Front-Office/end-results.html.twig',[
            'duration' => $calculateTime,
            'nameRoute' => $route,
            'score' => $score,
            'totalMark' => $totalMark,
            'userName' => $user,
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

    /**
     * @Route("/back-office/media", name="media_edit")
     */
    public function media(Request $request, SessionInterface $session)
    {
        //$museum = $session->get('museum');
        $form = $this->createForm(AddMediaType::class);
        $form->handleRequest($request);


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

            //addFlash ne fonctionne pas: faire afficher un message sur la page existante "modif enregistrées"
            //$this->addFlash('notice', 'Vos modifications ont bien été enregistrées.');

            return new Response('okkk');
        }



        return $this->render('Back-Office/BackOffice-v2/media.html.twig', [
            'formMedia' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mymuseum/ajax-media", name="ajax_media", methods={"GET", "HEAD"})
     */
    public function ajaxDescription($action, $param)
    {
        /**
         * retourne l'image chargée
         */
        /*if($action == 'getdescription' && $param) {

            $getInfo = $this->getDoctrine()->getRepository(\App\Entity\Route::class);
            $info = $getInfo->find(intval($param));
            $description = $info->getDescription();
            $duration = $info->getDuration();
        }
        return $this->render('Front-Office/ajax.html.twig', [
            'description' => $description,
            'duration' => $duration,
        ]);*/
    }

}