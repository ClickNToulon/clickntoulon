<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'bg-white px-4 py-3 dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md',
                    'placeholder' => 'Full name'
                ]
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Password',
                        'class' => 'bg-white px-4 py-3 dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Repeat Password',
                        'class' => 'bg-white px-4 py-3 dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                    ]
                ],
            ))
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'bg-white px-4 py-3 dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms'
        ]);
    }
}
