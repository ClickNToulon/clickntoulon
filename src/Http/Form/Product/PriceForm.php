<?php

namespace App\Http\Form\Product;

use App\Domain\Product\PriceHistory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */

class PriceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unitPrice', NumberType::class, [
                'required' => true
            ]);
            /**
            ->add('vat', NumberType::class, [
                'required' => false
            ])
            ->add('date_start', DateType::class, [
                'required' => true
            ])
            ->add('date_end', DateType::class, [
                'required' => true
            ]);
             **/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PriceHistory::class,
            'translation_domain' => 'products'
        ]);
    }
}