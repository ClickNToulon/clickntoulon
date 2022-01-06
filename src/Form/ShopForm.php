<?php

namespace App\Form;

use App\Entity\Shop;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('phone', NumberType::class, [
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ],
                "required" => false,
            ])
            ->add('address', TextareaType::class, [
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ])
            ->add('image', FileType::class, [
                'attr' => [
                    'class' => 'block w-full font-semibold text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 file:cursor-pointer'
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
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Shop::class,
                'translation_domain' => 'shop'
            ]);
    }
}
