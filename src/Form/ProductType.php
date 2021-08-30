<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md',
                    'rows' => 3
                ]
            ])
            ->add('deal_price', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('deal_start', DateType::class, [
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('deal_end', DateType::class, [
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('image',FileType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ],
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'mapped' => false,
                'required' => true,
                'label' => 'Categories',
                'query_builder' => function(CategoryRepository $em) use ($id) {
                    return $em->findAllByShopQuery($id);
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-800 text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
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
