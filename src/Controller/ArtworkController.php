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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ArtworkController extends Controller
{

    /*public function displayArtwork()
    {
        return $this->render('Front-Office/artwork.html.twig');
    }*/

    /**
     * @Route("/artwork/{id}", name="artwork")
     */
    public function displayTitle($id)
    {
        //la catégorie est passée en <dur>
        $category = "adulte";

        //récupération en base de l'oeuvre via l'entité Mark
        $repository = $this->getDoctrine()->getRepository(Mark::class);
        $currentMark = $repository->find($id);

        //récupération en base de la description de l'oeuvre via l'entité Description
        $categoryRepository = $this->getDoctrine()->getRepository(Description::class);
        $description = $categoryRepository->findBy([
            'id'=> $id,
            'category' => $category
            ]);

        return $this->render('Front-Office/artwork.html.twig',[
            'currentMark' => $currentMark,
            'description' => $description[0], //description est un tableau, index spécifié
        ]);
    }
}