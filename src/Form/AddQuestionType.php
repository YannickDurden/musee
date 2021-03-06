<?php

namespace App\Form;

use App\Entity\AddQuestion;
use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', TextType::class, [
            'label' => 'Question: ',
            'constraints' => [
            new NotBlank(),
            ]
        ]);
        $builder->add('answers', AddAnswersType::class, [
            'label' => 'Ajouter les reponses',
            'constraints' => [
                new NotBlank(),
            ]
        ]);
        $builder->add('category', ChoiceType::class, [
            'label' => 'Categorie de la question:',
            'choices'  => [
                'Choisir la categorie' => null,
                'Adulte' => 'adulte',
                'Enfant' => 'enfant',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
