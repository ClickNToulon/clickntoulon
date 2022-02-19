<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductType;
use App\Repository\ProductTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ]
            ])
            ->add('unitPrice', NumberType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400',
                    'rows' => 3
                ]
            ])
            ->add('unitPriceDiscount', NumberType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ]
            ])
            ->add('images',FileType::class, [
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'block w-full font-semibold text-black dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 file:cursor-pointer'
                ],
                'multiple' => true,
                'mapped' => false,
                'required' => true,
            ])
            ->add('type', EntityType::class, [
                'class' => ProductType::class,
                'mapped' => false,
                'required' => true,
                'label' => 'Type',
                'query_builder' => function(ProductTypeRepository $em) {
                    return $em->findAllQuery();
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Product::class,
                'translation_domain' => 'products'
            ])
            ->setRequired('id');
    }
}
