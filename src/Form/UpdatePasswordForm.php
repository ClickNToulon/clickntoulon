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
            'first_options' => ['label' => 'New Password', 'attr' => array_merge($htmlAttr, [
                'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
            ])],
            'second_options' => ['label' => 'Repeat Password', 'attr' => array_merge($htmlAttr, [
                'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
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