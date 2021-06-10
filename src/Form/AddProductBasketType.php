<?php

namespace App\Form;

use App\Entity\Basket;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProductBasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop_id', HiddenType::class, [
                "required" => true
            ])
            ->add('products_id', HiddenType::class, [
                "required" => true
            ])
            ->add('quantity', IntegerType::class, [
                "required" => true
            ])
            ->add("add", SubmitType::class, [
                "label" => "Add to the basket",
                "attr" => [
                    "class" => "btn btn-add"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Basket::class,
            'translation_domain' => "basket"
        ]);
    }
}
