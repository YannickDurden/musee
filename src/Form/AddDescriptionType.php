<?php

namespace App\Form;

use App\Entity\AddQuestion;
use App\Entity\Description;
use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddDescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', TextType::class, [
            'label' => 'Description : ',
            'constraints' => [
                new notBlank()
            ]
        ]);
        $builder->add('category', ChoiceType::class, [
            'label' => 'Categorie de la description :',
            'choices' => [
                'Choisir la categorie' => null,
                'Adulte' => 'adulte',
                'Enfant' => 'enfant',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Description::class,
        ]);
    }
}