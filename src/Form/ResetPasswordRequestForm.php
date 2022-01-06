<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autocomplete' => 'email',
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ],
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'security'
        ]);
    }
}
