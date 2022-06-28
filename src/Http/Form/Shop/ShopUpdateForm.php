<?php

namespace App\Http\Form\Shop;

use App\Domain\Shop\Shop;
use App\Domain\Shop\Tag;
use App\Domain\Shop\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class ShopUpdateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 3
                ]
            ])
            ->add('phone', NumberType::class)
            ->add('email', EmailType::class)
            ->add('address', TextareaType::class, [
                'attr' => [
                    'rows' => 1
                ]
            ])
            ->add('postalCode', TextType::class)
            ->add('city', TextType::class)
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
                    'class' => 'w-full rounded-lg text-black dark:text-white font-bold bg-slate-300 dark:bg-slate-800 border-2 border-slate-700 dark:border-slate-700 focus:border-blue-600'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shop::class,
            'translation_domain' => 'shop'
        ]);
    }
}