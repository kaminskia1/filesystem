<?php

namespace App\Controller\Admin;

use App\Entity\File;
use App\Entity\Folder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FolderCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Folder')
            ->setEntityLabelInPlural('Folders')
            ->setEntityLabelInSingular(
                fn (?Folder $folder, string $pageName = "0") => $folder ? ("Folder: " . $folder->getName() ): 'Folder'

            )
            ->showEntityActionsAsDropdown()
        ;
    }

    public static function getEntityFqcn(): string
    {
        return Folder::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::new('detail', 'View')->linkToCrudAction('detail'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', "Internal ID")->onlyOnDetail();
        yield TextField::new('uuid', "UUID")->onlyOnDetail();
        yield TextField::new('name', "Name");
        yield AssociationField::new('parentFolder', "Location");
        yield NumberField::new('permission', "Permission Level");
    }
}
