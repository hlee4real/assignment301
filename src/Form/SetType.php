<?php

namespace App\Form;

use App\Entity\Set;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('setName', TextType::class,
            [
                'label' => 'Set Name',
                'required' => true,
                'attr' => 
                [
                    'minlength' => 3,
                    'maxlength' => 255
                ]
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Set::class,
        ]);
    }
}
