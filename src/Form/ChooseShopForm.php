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
        $builder->add('select', EntityType::class, [
            'class' => Shop::class,
            'mapped' => false,
            'required' => true,
            'label' => false,
            'query_builder' => function(ShopRepository $em) use($owner) {
                return $em->choose($owner);
            },
            'choice_label' => 'name',
            'attr' => [
                'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
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
            ->setRequired('user');
    }
}