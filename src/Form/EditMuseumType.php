<?php

namespace App\Form;

use App\Entity\EditMuseum;
use App\Entity\Museum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditMuseumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'Nom du musée:',
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('address', TextType::class, [
            'label' => 'Adresse du musée:',
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('website', UrlType::class, [
            'label' => 'Site web du musée:',
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('facebook', UrlType::class, [
            'label' => 'Facebook du musée:',
            'constraints' => [
            ]
        ]);
        $builder->add('twitter', UrlType::class, [
            'label' => 'Twitter du musée:',
            'constraints' => [
            ]
        ]);
        $builder->add('instagram', UrlType::class, [
            'label' => 'Instagram du musée:',
            'constraints' => [
            ]
        ]);
        $builder->add('youtube', UrlType::class, [
            'label' => 'Youtube du musée:',
            'constraints' => [
            ]
        ]);
        $builder->add('phoneNumber', IntegerType::class, [
            'label' => 'Numéro de téléphone du musée:',
            'constraints' => [
                new NotBlank()
            ]
        ]);

        $builder->add('Modifier', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Museum::class,
        ]);
    }
}
