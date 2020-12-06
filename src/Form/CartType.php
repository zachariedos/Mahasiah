<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\Products;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user')
            ->add('products', EntityType::class, [
                'class'        => Products::class,
                'required' => false,
                'choice_label' => 'Title',
                'label'        => 'Articles',
                'expanded'     => false,
                'multiple'     => true,
                'attr' => array(
                    'multiple',
                    "searchable" => "Recherchez ici..",
                    'class' => 'selectpicker',
                    "data-live-search" => "true",
                    "title" => "Choisissez les catégories du produit..."
                )
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}
