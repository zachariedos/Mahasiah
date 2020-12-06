<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Products;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles');
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('Title', 'Nom du produit')
                ->setSortable(true),
            TextField::new('description', 'Description'),
            MoneyField::new('Price', 'Prix')
                ->setCurrency('EUR')
                ->setSortable(true),
            NumberField::new('Quantity', 'Quantité')
                ->setSortable(true),
            TextField::new('Size', 'Taille')
                ->setSortable(true),
            CollectionField::new('Categories', 'Catégories')
                ->setSortable(false)
                ->onlyOnIndex(),
        ];
    }
}
