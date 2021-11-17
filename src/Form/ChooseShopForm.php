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
                    'class' => 'w-full bg-white rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-black py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
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
            ->setRequired('user');;
    }
}