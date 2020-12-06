<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Title')
            ->add('Description')
            ->add('Price')
            ->add('Quantity')
            ->add('Size')
            ->add('Materials')
            ->add('Categories', EntityType::class, [
                'class'        => Categories::class,
                'required' => false,
                'choice_label' => 'name',
                'label'        => 'Catégories',
                'expanded'     => false,
                'multiple'     => true,
                'attr' => array(
                    'multiple',
                    "searchable" => "Recherchez ici..",
                    'class' => 'selectpicker',
                    "data-live-search" => "true",
                    "title" => "Choisissez les catégories du produit..."
                )
            ])
            ->add('product_images', CollectionType::class, [
                'entry_type' => ProductImageType::class,
                'allow_add' => true,
                'allow_delete' => false,
                'prototype' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
