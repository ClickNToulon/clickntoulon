<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Rechercher',
                'attr' => [
                    'name' => 'q',
                    'type' => 'search',
                    'class' => 'w-full bg-white dark:bg-darkblue-700 rounded-xl border-2 border-blue-800 focus:border-blue-500 text-gray-700 dark:text-gray-100 py-2 px-3 mt-1 mb-4 placeholder:text-gray-400'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchForm::class,
            'translation_domain' => 'search'
        ]);
    }
}