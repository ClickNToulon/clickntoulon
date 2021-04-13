<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $htmlAttr = [
            'minlength' => 4,
            'maxlength' => 4096,
        ];
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'constraints' => [
                new NotBlank(['normalizer' => 'trim']),
                new Length([
                    'min' => 4,
                    'max' => 4096,
                ]),
            ],
            'mapped' => false,
            'first_options' => ['label' => false, 'attr' => array_merge($htmlAttr, [
                'placeholder' => 'New Password',
                'class' => 'last-password'
            ])],
            'second_options' => ['label' => false, 'attr' => array_merge($htmlAttr, [
                'placeholder' => 'Repeat Password',
                'class' => 'last-password'
            ])],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdatePasswordForm::class,
            'translation_domain' => 'forms'
        ]);
    }
}