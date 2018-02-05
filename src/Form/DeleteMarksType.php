<?php

namespace App\Form;

use App\Entity\Mark;
use App\Entity\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TimeType;



class DeleteMarksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', EntityType::class, [
            'class' => Mark::class,
            'choice_label' => 'name',
            'label' => 'Nom du repère',
            'constraints' => [
                new NotBlank(),
            ]
        ]);

        $builder->add('delete', SubmitType::class, [
            'label' => 'Supprimer',
            'attr' => ['class' => 'btn btn-primary'],
        ]);


    }}