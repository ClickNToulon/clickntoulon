<?php

namespace App\Form;

use App\Entity\Shop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'fr-input last-password'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('phone', NumberType::class, [
                'attr' => [
                    'class' => 'fr-input last-password'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'last-password fr-input'
                ]
            ])
            ->add('address', TextareaType::class, [
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('postal_code', TextType::class, [
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'fr-input'
                ]
            ])
            ->add('cover', FileType::class, [
                'attr' => [
                    'class' => 'form-control last-password'
                ],
                'required' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'minHeight' => '200',
                        'minWidth' => '300'
                    ])
                ]
            ])
            ->add('tag', ChoiceType::class, [
                'choices' => $this->getChoices(),
                'attr' => [
                    'class' => 'fr-select last-select'
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
