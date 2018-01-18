<?php

namespace App\Form;
use App\Entity\Description;
use App\Entity\Mark;
use Doctrine\ORM\EntityRepository;
use App\Entity\Media;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\DBAL\Types\DecimalType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Route;


class AddMarkAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('name',TextType ::class , [
				'label' => 'Ajouter un repère ',
				'constraints' => [
					new NotBlank(),
				]
			]
        );
        $builder->add('coordinateX', NumberType::class, [
				'label' => 'Coordonnées X',
				'constraints' => [
					new NotBlank(),
				]
			]
        );
        $builder->add('coordinateY', NumberType::class, [
				'label' => 'Coordonnées X ',
				'constraints' => [
					new NotBlank(),
				]
			]
        );
        $builder->add('image', FileType::class, [
            'constraints' => [
                new NotBlank(),
                new File(['mimeTypes' => ['image/jpeg', 'image/png', 'image/gif']]),
            ]
        ]);
        $builder->add('descriptions', CollectionType::class, [
            'entry_type'   => AddDescriptionType::class,
            'allow_add'    => true,
            'allow_delete' => true
        ]);
        $builder->add('questions', CollectionType::class, [
            'entry_type'   => AddQuestionType::class,
            'allow_add'    => true,
            'allow_delete' => true
        ]);
        
       $builder->add('save', SubmitType::class, ['label' => 'Ajouter']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            'data_class' => Mark::class,
        ]);
    }
    
    
}