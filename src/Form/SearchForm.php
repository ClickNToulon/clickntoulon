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
                    'autocomplete' => 'new-password',
                    'class' => 'w-full rounded-xl mt-1 mb-4 text-black dark:text-white font-bold bg-darkblue-200 dark:bg-darkblue-800 border-2 border-blue-700 dark:border-blue-800 focus:border-blue-600 dark:focus:border-blue-500 placeholder:text-gray-700 dark:placeholder:text-gray-400'
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