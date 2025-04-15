<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name',
                'attr' => [
                    'placeholder' => 'Enter product name',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Product Description',
                'attr' => [
                    'placeholder' => 'Enter product description',
                    'rows' => 4,
                    'class' => 'form-control'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Electronics' => 1,
                    'Clothing' => 2,
                    'Books' => 3,
                    'Home & Garden' => 4,
                    'Sports' => 5
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('size', ChoiceType::class, [
                'label' => 'Size',
                'choices' => [
                    'Small' => 1,
                    'Medium' => 2,
                    'Large' => 3,
                    'Extra Large' => 4
                ],
                'expanded' => true, // This makes it radio buttons
                'multiple' => false,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('inStock', CheckboxType::class, [
                'label' => 'In Stock',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'attr' => [
                    'placeholder' => 'Enter price',
                    'class' => 'form-control'
                ]
            ])
            ->add('color', ColorType::class, [
                'label' => 'Product Color',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Release Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('image', FileType::class, [
                'label' => 'Product Image',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('location', ChoiceType::class, [
                'label' => 'Available Locations',
                'choices' => [
                    'North America' => 'NA',
                    'Europe' => 'EU',
                    'Asia' => 'AS',
                    'Australia' => 'AU'
                ],
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-check-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
