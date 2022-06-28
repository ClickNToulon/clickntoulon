<?php

namespace App\Http\Form\Shop;

use App\Domain\Shop\Tag;
use App\Helper\FilterData\SearchShopData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterShopForm extends AbstractType
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
            ->add('tag', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Tag::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('city', TextType::class, [
                'label' => "City",
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'label' => "Postal code",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchShopData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'translation_domain' => 'shop'
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

}