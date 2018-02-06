<?php
/**
 * Created by PhpStorm.
 * User: yannick
 * Date: 17/01/2018
 * Time: 18:50
 */

namespace App\Controller;


use App\Entity\Description;
use App\Entity\Mark;
use App\Entity\Media;
use App\Entity\Museum;
use App\Entity\Route as r;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class MarkController extends Controller
{
    /**
     * @Route("/mymuseum/artwork/{id}", name="artwork")
     */
    public function displayMark(SessionInterface $session, $id)
    {
        $nameRoute = $session->get('nameRoute');

        /**
         * Récupération de l'oeuvre et de sa description
         */
        $repository = $this->getDoctrine()->getRepository(Mark::class);
        $currentMark = $repository->find($id);

        $currentMedia = $this->getDoctrine()->getRepository(Media::class)->find
        ($currentMark->getMedias()->getId());

        $descriptions = $currentMark->getDescriptions();

        $session->set('currentMark', $currentMark->getName());

        foreach( $descriptions as $description)
        {
            if($description->getCategory() == 'adulte')
            {
                $goodDescription = $description->getLabel();
            }
        }

        return $this->render('Front-Office/newArtwork.html.twig',[
            'currentMark' => $currentMark,
            'currentMedia' => $currentMedia,
            'description' => $goodDescription,
            'nameRoute' => $nameRoute,
            'id' => $id,
        ]);
    }





}