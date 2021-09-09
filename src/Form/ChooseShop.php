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
                    'class' => 'bg-white dark:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-900 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
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