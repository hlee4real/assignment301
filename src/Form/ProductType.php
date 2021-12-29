<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Set;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
            [
                'label' => 'Product Name',
                'required' => true,
                'attr' =>
                [
                    'minlength' => 5,
                    'maxlength' =>30
                ]
            ]
            )
            ->add('price', MoneyType::class,
            [
                'label' => 'Product Price',
                'required' => true,
                'currency' => 'USD'
            ]
            )
            ->add('quantity', IntegerType::class,
            [
                'label' => 'Quantity',
                'required' => true,
                'attr' =>
                [
                    'min' => 1,
                    'max' => 100
                ]
            ]
            )
            ->add('image', FileType::class,
            [
                'label' => 'Image',
                'data_class' => null,
                'required' => is_null($builder->getData()->getImage())
            ]
            )
            ->add('description', TextType::class,
            [
                'label' => 'Description',
                'required' => true,
                'attr' =>
                [
                    'minlength' => 5,
                    'maxlength' => 255 
                ]
            ])
            ->add('category', EntityType::class,
            [
                'label' => 'Category',
                'class' => Category::class,
                'choice_label' => 'category_name',
                'multiple' => false,
                'expanded' => false
            ]
            )
            ->add('setName', EntityType::class,
            [
                'label' => 'Set',
                'class' => Set::class,
                'choice_label' => 'set_name',
                'multiple' => false,
                'expanded' => false
            ]
            )
            // ->add('cart')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
