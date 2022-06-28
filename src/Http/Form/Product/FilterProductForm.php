<?php

namespace App\Http\Form\Product;

use App\Domain\Product\ProductType;
use App\Domain\Shop\Shop;
use App\Helper\FilterData\SearchProductData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterProductForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("q", TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('types', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => ProductType::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('shop', EntityType::class, [
                'label' => "Boutique",
                'required' => false,
                'class' => Shop::class,
                'placeholder' => '-- Choisir une boutique --',
                'attr' => [
                    'class' => 'w-full'
                ]
            ])
            ->add('min', HiddenType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min'
                ]
            ])
            ->add('max', HiddenType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchProductData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'translation_domain' => 'products'
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}