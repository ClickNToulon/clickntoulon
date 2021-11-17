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
            'first_options' => [
                'label' => 'New Password',
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ],
                'attr' => array_merge($htmlAttr, [
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ])
            ],
            'second_options' => [
                'label' => 'Repeat Password',
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ],
                'attr' => array_merge($htmlAttr, [
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ])
            ],
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