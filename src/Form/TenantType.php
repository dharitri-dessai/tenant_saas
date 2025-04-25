<?php

namespace App\Form;

use App\Entity\Tenant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class TenantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a tenant name',
                    ]),
                ],
            ])
            ->add('subdomain', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a subdomain',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z0-9-]+$/',
                        'message' => 'Please enter a valid subdomain',
                    ]),
                ],
            ])
           
            ->add('email', EmailType::class, [ 
                'mapped' => false,
                'data' => $options['data']?->getUsers()->count() > 0 ? $options['data']?->getUsers()?->filter(function($user) {
                    return in_array('ROLE_TENANT_ADMIN', $user->getRoles(), true);
                })?->first()?->getEmail() : '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                ],
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'Active',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tenant::class,
        ]);
    }
} 