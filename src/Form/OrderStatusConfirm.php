<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class OrderStatusConfirm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('date', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('begin', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('end', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('message', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Votre message (optionel)',
                'attr' => [
                    'class' => 'fr-input'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'translation_domain' => 'forms'
            ]);
    }
}