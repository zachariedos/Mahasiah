<?php

namespace App\Form;

use App\Entity\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('SearchPrixMin')
            ->add('SearchPrixMax')
            // ->add('SearchCategorie', ChoiceType::class, [
            //     'placeholder' => 'Choisir une catégorie',
            //     'choices' => [
            //         'Vente' => 'vente',
            //         'Location' => 'location',
            //     ],
            //     'required' => false
            // ])
            ->add('SearchGlobal');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
