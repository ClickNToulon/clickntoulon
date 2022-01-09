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
                    'autocomplete' => 'new-password',
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ]
            ])
            ->add('surname', TextType::class, [
                'label' => 'Surname',
                'attr' => [
                    'placeholder' => 'John',
                    'autocomplete' => 'new-password',
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ]
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500'
                    ]
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500'
                    ]
                ],
            ))
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'johndoe@exemple.com',
                    'autocomplete' => 'new-password',
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
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
