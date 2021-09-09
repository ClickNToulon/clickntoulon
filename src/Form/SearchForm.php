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
                'label' => false,
                'attr' => [
                    'class' => 'bg-white dark:bg-gray-800 focus:bg-gray-200 dark:focus:bg-gray-900 text-black dark:text-white shadow dark:shadow-none focus:ring-yellow-500 focus:border-yellow-500 mt-1 block w-full sm:text-sm border border-gray-700 rounded-md',
                    'name' => 'q',
                    'type' => 'search',
                    'placeholder' => 'Rechercher'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchForm::class,
            'translation_domain' => 'forms'
        ]);
    }
}