<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ]
            ])
            ->add('unitPrice', NumberType::class, [
                'required' => true,
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                    'rows' => 3
                ]
            ])
            ->add('unitPriceDiscount', NumberType::class, [
                'required' => false,
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ]
            ])
            ->add('images',FileType::class, [
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'multiple' => true,
                'mapped' => false,
                'required' => true,
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'mapped' => false,
                'required' => true,
                'label' => 'Categories',
                'label_attr' => [
                    'class' => 'block text-base font-bold text-black'
                ],
                'query_builder' => function(CategoryRepository $em) use ($id) {
                    return $em->findAllByShopQuery($id);
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'products'
        ])
        ->setRequired('id');
    }
}
