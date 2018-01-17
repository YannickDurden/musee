<?php
    namespace App\Form;

    use App\Entity\Mark;
    use App\Entity\Route;
    use App\Entity\Task;
    use App\Repository\MarkRepository;
    use Doctrine\ORM\Mapping\Entity;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\TimeType;
    use Symfony\Component\Form\Form;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\NotNull;

    class AddRouteType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 50]),
                ]
            ]);
            $builder->add('duration', TimeType::class, [
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank(),
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
            $builder->add('save', SubmitType::class, ['label' => 'Ajouter le parcours']);

        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Route::class,
            ]);
        }
    }