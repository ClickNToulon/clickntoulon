<?php

namespace App\Http\Form;

use App\Domain\Report\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
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
                    'rows' => 4
                ],
            ])
            ->add('email', EmailType::class, [
                'mapped' => true,
                'required' => true,
                'label' => 'Email',
            ])
            ->add('url', UrlType::class, [
                'mapped' => true,
                'required' => true,
                'label' => 'Le lien vers le contenu signalé'
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