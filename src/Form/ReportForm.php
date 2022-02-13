<?php

namespace App\Form;

use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'mapped' => true,
                'required' => true,
                'label' => 'Le motif de votre signalement',
                'attr' => [
                    'rows' => 4,
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ],
            ])
            ->add('url', UrlType::class, [
                'mapped' => true,
                'required' => true,
                'label' => 'Le lien vers le contenu signalé',
                'attr' => [
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
            'translation_domain' => 'report'
        ]);
    }
}