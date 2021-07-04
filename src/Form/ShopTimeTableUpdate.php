<?php


namespace App\Form;


use App\Entity\TimeTable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopTimeTableUpdate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shopId', HiddenType::class, [
                'mapped' => true,
                'required' => true
            ])
            ->add('monAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('monAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('monPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('monPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('tueAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('tueAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('tuePmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('tuePmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('wedAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('wedAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('wedPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('wedPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('thuAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('thuAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('thuPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('thuPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('friAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('friAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('friPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('friPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('satAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('satAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('satPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('satPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('sunAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('sunAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('sunPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ])
            ->add('sunPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'fr-input mb-0'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimeTable::class,
            'translation_domain' => 'forms'
        ]);
    }

}