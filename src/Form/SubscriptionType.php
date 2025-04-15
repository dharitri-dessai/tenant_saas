<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('planId', ChoiceType::class, [
                'label' => 'Subscription Plan',
                'choices' => [
                    'Basic Plan' => 'price_basic',
                    'Pro Plan' => 'price_pro',
                    'Enterprise Plan' => 'price_enterprise',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a subscription plan',
                    ]),
                ],
            ])           
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Active' => 'active',
                    'Cancelled' => 'cancelled',
                    'Expired' => 'expired',
                    'Inactive' => 'inactive'
                ],
                'expanded' => true, // This makes it radio buttons
                'multiple' => false,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('paymentMethodId', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
} 