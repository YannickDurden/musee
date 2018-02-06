<?php
    namespace App\Form;

    use App\Entity\Mark;
    use App\Entity\Route;
    use App\Entity\Task;
    use App\Repository\MarkRepository;
    use App\Repository\RouteRepository;
    use Doctrine\ORM\Mapping\Entity;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\Extension\Core\Type\TimeType;
    use Symfony\Component\Form\Form;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\Image;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\NotNull;

    class AddRouteType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'route.name.not_blank']),
                    new Length(['max' => 50]),
                ]
            ]);
            $builder->add('map', FileType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'route.map.not_blank']),
                    new Image(
                        ['mimeTypes' => ['image/jpeg', 'image/png', 'image/gif']


                        ])
                ]
            ]);
            $builder->add('duration', TimeType::class, [
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank(),
                ]
            ]);
            $builder->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'route.description.not_blank']),
                ]
            ]);
            $builder->add('marks', EntityType::class, [
                'class' => Mark::class,
                'choice_label' => 'name',
                'query_builder' => function (MarkRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.museum = 1') ;},
                'expanded' => true,
                'multiple' => true

            ]);
            $builder->add('save', SubmitType::class, ['attr' => ['class' =>'btn btn-primary']]);

        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Route::class,
            ]);
        }
    }