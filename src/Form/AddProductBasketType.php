<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProductBasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop_id', HiddenType::class, [
                "required" => true
            ])
            ->add('product_id', HiddenType::class, [
                "required" => true
            ])
            ->add('quantity', NumberType::class, [
                "required" => true,
                "attr" => [
                    "class" => "fr-input"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'translation_domain' => "basket"
        ]);
    }
}
