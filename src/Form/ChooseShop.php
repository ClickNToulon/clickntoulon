<?php

namespace App\Form;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseShop extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $id = $options['id'];
        $builder
            ->add('select', EntityType::class, [
                'class' => Shop::class,
                'mapped' => false,
                'required' => true,
                'label' => false,
                'query_builder' => function(ShopRepository $em) use($id) {
                    return $em->choose($id);
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'block w-full my-1 dark:bg-gray-800 dark:text-white rounded-md border-gray-300 dark:border-gray-900 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
            'data_class' => Shop::class,
            'translation_domain' => 'forms'
            ])
            ->setRequired('id');;
    }
}