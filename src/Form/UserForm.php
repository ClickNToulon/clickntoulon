<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Doe',
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-500 text-base outline-none text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('surname', TextType::class, [
                'label' => 'Surname',
                'attr' => [
                    'placeholder' => 'John',
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-500 text-base outline-none text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
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
            ))
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'johndoe@exemple.com',
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-500 text-base outline-none text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'user'
        ]);
    }
}
