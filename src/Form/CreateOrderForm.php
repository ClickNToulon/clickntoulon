<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateOrderForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', DateType::class, [
                'mapped' => true,
                'required' => true,
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ],
                'widget' => 'single_text',
            ])
            ->add('total', HiddenType::class, [
                'mapped' => true,
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'translation_domain' => 'order'
        ]);
    }
}