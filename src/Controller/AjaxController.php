<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Museum;
use App\Form\AddRouteType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

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
     * @route("ajax/route/add", name="ajax_add_BDD")
     */
    public function addAjaxBdd()
    {
        parse_str($_POST['form'], $arrayObject);
        $updatedRoute = $this->getDoctrine()->getRepository(\App\Entity\Route::class)->find($_POST['id']);
        $updatedRoute->setName($arrayObject['add_route']['name']);
        $durationArrayToString = $arrayObject['add_route']['duration']['hour']." ".$arrayObject['add_route']['duration']['minute'];
        $duration = \DateTime::createFromFormat('H i', $durationArrayToString);
        //$duration = new \DateTime('now');
        $updatedRoute->setDuration($duration);
        $arrayMarks = new ArrayCollection();
        for($i=0; $i<count($arrayObject['add_route']['marks']); $i++)
        {
            $arrayMarks []= $this->getDoctrine()->getRepository(Mark::class)->find($arrayObject['add_route']['marks'][$i]);
        }
        $updatedRoute->setMarks($arrayMarks);
        $em =$this->getDoctrine()->getManager();
        $em->merge($updatedRoute);
        $em->flush();
        return new Response("ok");
    }
}
