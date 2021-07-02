<?php


namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateOrder extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shop_id', HiddenType::class, [
            'mapped' => true,
            'required' => true
            ])
            ->add('productsId', HiddenType::class, [
                'mapped' => true,
                'required' => true
            ])
            ->add('buyer_id', HiddenType::class, [
                'mapped' => true,
                'required' => true
            ])
            ->add('quantity', HiddenType::class, [
                'mapped' => true,
                'required' => true
            ])
            ->add('status', HiddenType::class, [
                'mapped' => true,
                'required' => true
            ])
            ->add('day', DateType::class, [
                'mapped' => true,
                'required' => true,
                'attr' => [
                    'class' => 'fr-input'
                ],
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'translation_domain' => 'forms'
        ]);
    }
}