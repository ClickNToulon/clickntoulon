<?php

namespace App\Form;

use App\Entity\Shop;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ShopForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('phone', NumberType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('address', TextareaType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('image', FileType::class, [
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ],
                'required' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'minHeight' => '200',
                        'minWidth' => '300'
                    ])
                ]
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'mapped' => false,
                'required' => true,
                'label' => 'Tag',
                'query_builder' => function(TagRepository $em) {
                    return $em->findAllQuery();
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Shop::class,
                'translation_domain' => 'forms'
            ]);
    }
}
