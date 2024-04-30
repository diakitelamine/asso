<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits');
    }

    public function configureFields(string $pageName): iterable
    {

         $required = true;
        if ($pageName == 'edit') {
            $required = false;  
        }

        return [
            IdField::new('id')->hideOnForm()->setLabel('ID'),
            TextField::new('name')->setLabel('Titre')->setHelp('Titre du produit'),
            BooleanField::new('isHomePage')->setLabel('Produit à la une ?')->setHelp('Afficher le produit sur la page d\'accueil'),
            SlugField::new('slug')->setTargetFieldName('name')->setHelp('URL du produit'),
            TextEditorField::new('description')->setLabel('Description')->setHelp('Description du produit'),
            ImageField::new('illustration')->setLabel('Image')->setHelp('Image du produit')->setUploadDir('/public/uploads')
                                           ->setBasePath('uploads')
                                           ->setUploadedFileNamePattern('[day]-[month]-[year]-[contenthash].[extension]')->setRequired($required),
            NumberField::new('price')->setLabel('Prix H.T')->setHelp('Prix du produit H.T'),
            ChoiceField::new('tva')->setLabel('TVA')->setChoices([
                '2.1%' => '2.1',
                '5.5%' => '5.5',
                '10%' => '10',
                '20%' => '20'
            ]),
            AssociationField::new('category')->setLabel('Catégorie')->setHelp('Catégorie du produit'),
        ];
    }

  
}
