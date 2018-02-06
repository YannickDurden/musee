<?php

namespace App\Form;

use App\Entity\Route;
use App\Repository\RouteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddSelectRouteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //champ select en fonction de l'id du musée
        $builder->add('route', EntityType::class, [
            'class' => Route::class,
            'choice_label' => 'name',
            'query_builder' => function (RouteRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.museum = 1') ;},
        ]);

        $builder->add('submit',SubmitType::class,[
            'label'=> 'Démarrer'
        ]);

    }
}