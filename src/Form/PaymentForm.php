<?php


namespace App\Form;


use App\Entity\Shop;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checkbox', EntityType::class, [
                'class' => Shop::class,
                'mapped' => false,
                'required' => true,
                'label' => false,
                'query_builder' => function (PaymentRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.shop_id = :shop_id')
                        ->setParameter('shop_id', 15);
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shop::class,
            'translation_domain' => 'forms'
        ]);
    }

}