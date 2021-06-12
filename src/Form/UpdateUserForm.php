<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'fr-input last-input'
                ]
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'fr-input last-input'
                ]
            ])
            ->add('address', TextareaType::class, [
                'attr' => [
                    'class' => 'fr-input last-input'
                ]
            ])
            ->add('postal_code', TextType::class, [
                'attr' => [
                    'class' => 'fr-input last-input'
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'fr-input last-input'
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