<?php

namespace App\Form;

use App\Entity\AddAnswers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddAnswersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('goodAnswer', TextType::class,[
                'label' => 'Bonne réponse:',
                'constraints' =>[
                    new NotBlank(),
                ]
            ])
            ->add('answer1', TextType::class,[
                'label' => 'Réponse 2:',
                'constraints' =>[
                    new NotBlank(),
                ]
            ])
            ->add('answer2', TextType::class,[
                'label' => 'Réponse 3:',
                'constraints' =>[
                    new NotBlank(),
                ]
            ])
            ->add('answer3', TextType::class,[
                'label' => 'Réponse 4:',
                'constraints' =>[
                    new NotBlank(),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            //'data_class' => AddAnswers::class,
        ]);
    }
}
