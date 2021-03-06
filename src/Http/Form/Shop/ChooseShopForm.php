<?php

namespace App\Http\Form\Shop;

use App\Domain\Shop\Shop;
use App\Domain\Shop\ShopRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
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
            'choice_label' => 'name'
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