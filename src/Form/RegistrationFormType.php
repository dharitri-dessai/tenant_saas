<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\TenantRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Tenant;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                ],
            ])
            ->add('email', EmailType::class)
            ->add('userType', ChoiceType::class, [
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Tenant' => 'tenant',
                    'User' => 'user'
                ],
                'data' => 'tenant',
                'attr' => [
                    'class' => 'user-type-radio'
                ]
            ])            
            ->add('tenant', EntityType::class, [
                'mapped' => false,
                'class' => Tenant::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a tenant',
                'required' => false,
                'query_builder' => function (TenantRepository $tenantRepository) {
                    return $tenantRepository->createQueryBuilder('t')
                        ->where('t.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('t.name', 'ASC');
                },
                'attr' => [
                    'class' => 'tenant-select'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('subdomain', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'tenant-subdomain'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
} 