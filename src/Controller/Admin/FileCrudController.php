<?php

namespace App\Controller\Admin;

use App\Entity\File;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FileCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Files')
            ->setEntityLabelInPlural('Modules')
            ->setEntityLabelInSingular(
                fn (?File $file, string $pageName = "0") => $file ? ("File: " . $file->getName() ): 'File'

            )
            ->showEntityActionsAsDropdown()
            ;
    }

    public static function getEntityFqcn(): string
    {
        return File::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::new('detail', 'View')->linkToCrudAction('detail'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', "Internal ID")->onlyOnDetail();
        yield AssociationField::new('folder', "Location");
        yield TextField::new('name', "Name");
        yield TextField::new('extension', "Extension");
        yield ChoiceField::new('type', "Location")->setChoices([
            'a' => 'b'
        ]);
    }
}
