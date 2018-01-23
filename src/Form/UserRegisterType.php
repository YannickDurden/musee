<?php
/**
 * Created by PhpStorm.
 * User: yannickfrancois
 * Date: 19/01/2018
 * Time: 16:24
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserRegisterType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 5]),
            ],
            'label'=> 'Votre prénom',

        ]);

        $builder->add('lastname',TextType::class,[
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 5]),
            ],
            'label' => 'Votre nom',
        ]);

        $builder->add('email',EmailType::class,[
            'constraints' => [
                new NotBlank(),
                new Email(),
            ],
            'label'=> 'Votre adresse e-mail',
        ]);

        $builder->add('newsletter',CheckboxType::class,[
            'required'=> false,
            'label'=> 'Je m\'inscris à la newsletter',
        ]);

        $builder->add('submit', SubmitType::class, [
            'label'=> 'Envoyer',
        ]);
    }

        public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}