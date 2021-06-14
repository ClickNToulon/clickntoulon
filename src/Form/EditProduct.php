<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\CategoryRepository;
use App\Repository\ShopRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EditProduct extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'fr-input',
                    'rows' => 3
                ]
            ])
            ->add('image',FileType::class, [
                'attr' => [
                    'class' => 'form-control last-password'
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
            ->add('deal_price', NumberType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('deal_start', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('deal_end', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'fr-input'
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
                    'class' => 'fr-select'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'translation_domain' => 'forms'
        ])
        ->setRequired('id');
    }
}