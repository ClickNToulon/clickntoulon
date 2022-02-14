<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class AddProductBasketForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop_id', HiddenType::class, [
                "required" => false
            ])
            ->add('product_id', HiddenType::class, [
                "required" => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => "basket"
        ]);
    }
}
