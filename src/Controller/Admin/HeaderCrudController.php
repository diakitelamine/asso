<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }
    
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Header')
            ->setEntityLabelInPlural('Headers');
    }


    public function configureFields(string $pageName): iterable
    {

        
        $required = true;
        if ($pageName == 'edit') {
            $required = false;  
        }

        return [
            TextField::new('title')->setLabel('Titre')->setHelp('Titre du Header'),
            TextareaField::new('content')->setLabel('Contenu'),
            TextField::new('buttonTitle')->setLabel('Titre du bouton'),
            TextField::new('buttonLink')->setLabel('Url du bouton'),
            ImageField::new('illustration')->setLabel('Image de fond du Header')->setHelp('Image de fond du Header en jpg ou jpeg')->setUploadDir('/public/uploads')
                                           ->setBasePath('uploads')
                                           ->setUploadedFileNamePattern('[day]-[month]-[year]-[contenthash].[extension]')->setRequired($required),



            
        ];
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
