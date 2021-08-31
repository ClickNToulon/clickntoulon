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
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('monAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('monPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('monPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('tueAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('tueAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('tuePmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('tuePmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('wedAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('wedAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('wedPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('wedPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('thuAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('thuAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('thuPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('thuPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('friAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('friAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('friPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('friPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('satAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('satAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('satPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('satPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('sunAmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('sunAmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('sunPmOp', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
                ]
            ])
            ->add('sunPmCl', TimeType::class, [
                'mapped' => true,
                'required' => false,
                'widget' => 'single_text',
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-700 focus:bg-gray-200 dark:focus:bg-gray-800 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md'
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