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
use App\Entity\Museum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MarkController extends Controller
{
    /**
     * @Route("/mymuseum/artwork/{id}", name="artwork")
     */
    public function displayMark(SessionInterface $session, $id)
    {
        $map = $this->getDoctrine()->getRepository(Museum::class)->find(1)->getMap();
        $idMark = $session->get('selectedRoute');

        //récupération en base de l'oeuvre via l'entité Mark
        $repository = $this->getDoctrine()->getRepository(Mark::class);
        $currentMark = $repository->find($id);
        $descriptions = $currentMark->getDescriptions();

        foreach( $descriptions as $description)
        {
            if($description->getCategory() == 'adulte')
            {
                $goodDescription = $description->getLabel();
            }
        }

        return $this->render('Front-Office/artwork.html.twig',[
            'currentMark' => $currentMark,
            'description' => $goodDescription,
            'map' => $map,
            'idMark' => $idMark,
            'id' => $id,
        ]);
    }
}