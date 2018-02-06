<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'Nom du média: ',
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('file', FileType::class, [
            'label' => 'Choisir une image',
            'constraints' => [
                new NotBlank(),
                new Image(['mimeTypes' => ['image/jpeg', 'image/png', 'image/gif']])
            ]
        ]);
        $builder->add('submit', SubmitType::class, ['label' => 'Ajouter l\'image']);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
