<?php

namespace App\Form;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseShopForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $owner = $options['user'];
        $builder
            ->add('select', EntityType::class, [
                'class' => Shop::class,
                'mapped' => false,
                'required' => true,
                'label' => false,
                'query_builder' => function(ShopRepository $em) use($owner) {
                    return $em->choose($owner);
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 focus:ring-blue-200 dark:focus:ring-blue-500 text-base outline-none text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Shop::class,
                'translation_domain' => 'shop'
            ])
            ->setRequired('user');;
    }
}