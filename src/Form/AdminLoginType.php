<?php

namespace App\Form;

use App\Entity\AdminLogin;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AdminLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
            'constraints' => [
                new Regex(['pattern' => '/^[\w_]*$/'])],
            'label' => 'Login'
        ]);
        $builder->add('password', PasswordType::class, [
            'constraints' => [
                new NotBlank()
            ],
            'label' => 'Mot de Passe'
        ]);
        $builder->add('login', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
