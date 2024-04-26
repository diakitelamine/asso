<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes');
    }


    public function configureActions(Actions $actions): Actions
    {

        $show = Action::new('Afficher')->linkToCrudAction('show');

        return $actions
        ->add(Crud::PAGE_INDEX, $show)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
        
    }

    public function show(AdminContext $context)
    {
       
        $order = $context->getEntity()->getInstance();
        return $this->render('admin/order.html.twig', [
            'order' => $order
        ]);

    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt')->setLabel('Date')->setHelp('Date de la commande'),
            NumberField::new('state')->setLabel('Etat')->setTemplatePath('admin/state.html.twig'),
            TextField::new('user')->setLabel('Utilisateur')->setHelp('Utilisateur de la commande'),
            TextField::new('carrierName')->setLabel('Transporteur')->setHelp('Transporteur de la commande'),
            NumberField::new('totalTva')->setLabel('Total TVA')->setHelp('Total de la commande'),
            NumberField::new('totalwt')->setLabel('Total TTC')->setHelp('Total de la commande'),
            

            
        ];
    }   


}
