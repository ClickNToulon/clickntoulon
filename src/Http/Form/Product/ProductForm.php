<?php

namespace App\Http\Form\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductType;
use App\Domain\Product\ProductTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('unitPrice', NumberType::class, [
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'rows' => 3
                ]
            ])
            ->add('unitPriceDiscount', NumberType::class, [
                'required' => false
            ])
            ->add('images', FileType::class, [
                'attr' => [
                    'accept' => 'image/*'
                ],
                'multiple' => true,
                'mapped' => false,
                'required' => true,
            ])
            ->add('type', EntityType::class, [
                'class' => ProductType::class,
                'required' => true,
                'label' => 'Type',
                'query_builder' => function(ProductTypeRepository $em) {
                    return $em->findAllQuery();
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'w-full rounded-lg text-black dark:text-white font-bold bg-slate-300 dark:bg-slate-800 border-2 border-slate-700 dark:border-slate-700 focus:border-blue-600'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'products'
        ]);
    }
}
