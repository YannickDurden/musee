<?php
/**
 * Created by PhpStorm.
 * User: yannickfrancois
 * Date: 22/01/2018
 * Time: 14:30
 */

namespace App\Form;

use App\Entity\Route;
use App\Repository\MarkRepository;
use App\Repository\RouteRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectRouteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('route', EntityType::class, [
            'class' => Route::class,
            'choice_label' => 'name',
            'query_builder' => function (RouteRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.museum = 1') ;},
        ]);

        $builder->add('Description',SubmitType::class,
            [
                'label' => 'Voir la description'
            ]);
    }
}