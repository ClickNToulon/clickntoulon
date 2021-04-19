<?php

namespace App\Form;

use App\Entity\Shop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopUpdateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'last-password'
                ]
            ])
            ->add('description')
            ->add('phone', NumberType::class, [
                'attr' => [
                    'class' => 'last-password'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'last-password form-control'
                ]
            ])
            ->add('address', TextareaType::class)
            ->add('postal_code')
            ->add('city')
            ->add('tag', ChoiceType::class, [
                'choices' => $this->getChoices(),
                'attr' => [
                    'class' => 'form-select last-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shop::class,
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices(): array
    {
        $choices = Shop::Tag;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }
}