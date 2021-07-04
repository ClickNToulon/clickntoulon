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
                    'class' => 'fr-input search-input',
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