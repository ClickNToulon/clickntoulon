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
        $builder->add('email', EmailType::class, [
            'attr' => [
                'autocomplete' => 'email',
                'class' => 'w-full rounded-xl mt-1 mb-4 font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500'
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
