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
            'first_options' => [
                'label' => 'New Password',
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-500 text-base outline-none text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ],
            'second_options' => [
                'label' => 'Repeat Password',
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-500 text-base outline-none text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdatePasswordForm::class,
            'translation_domain' => 'user'
        ]);
    }
}