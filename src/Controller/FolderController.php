<?php

namespace App\Controller;

use App\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class FolderController
 *
 * @package App\Controller
 */
class FolderController extends AbstractController
{

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * FilesystemController constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Add folder controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/folder/add", name="site_folder_add_root")
     * @Route("/folder/add/{parentUuid}", name="site_folder_add")
     *
     * @param Request     $request
     * @param String|null $parentUuid
     *
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, $parentUuid = null)
    {
        $folder = new Folder();
        if ($parentUuid !== null) {
            $parent = $this->getDoctrine()->getManager()->getRepository(Folder::class)->findUuid($parentUuid);
            if ($parent === null) {
                throw new Exception("Invalid parent UUID");
            }
            $folder->setParentFolder($parent);
        }

        $form = $this->createFormBuilder($folder)
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $folder = $form->getData();

            $this->entityManager->persist($folder);
            $this->entityManager->flush();

            return $this->redirectToRoute("site_view_folder", [
                'folder' => $folder->getParentFolder() !== null ? $folder->getParentFolder()->getUuid() : null
            ]);
        }

        return $this->renderForm('file_system/folder/add.html.twig', [
            'parent' => $parentUuid,
            'form' => $form
        ]);
    }

    /**
     * Rename folder controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/folder/rename/{uuid}", name="site_folder_rename")
     *
     * @param Request $request
     * @param String  $uuid
     *
     * @return Response
     * @throws Exception
     */
    public function rename(Request $request, $uuid)
    {
        /** @var Folder $folder */
        $folder = $this->getDoctrine()->getManager()->getRepository(Folder::class)->findUuid($uuid);
        if ($folder !== null) {
            $form = $this->createFormBuilder($folder)
                ->add('name', TextType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $folder = $form->getData();

                $this->entityManager->persist($folder);
                $this->entityManager->flush();

                return $this->redirectToRoute("site_view_folder", [
                    'folder' => $folder->getParentFolder() !== null ? $folder->getParentFolder()->getUuid() : null
                ]);
            }

            return $this->renderForm('file_system/folder/rename.html.twig', [
                'folderUuid' => $folder->getUuid(),
                'form' => $form
            ]);
        }


    }

    /**
     * Move folder controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/folder/move/{uuid}", name="site_folder_move")
     *
     * @param Request $request
     * @param String  $uuid
     *
     * @return Response
     * @throws Exception
     */
    public function move(Request $request, $uuid)
    {
        return new Response("Move Folder $uuid");

    }

    /**
     * Delete folder controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/folder/delete/{uuid}", name="site_folder_delete")
     *
     * @param Request $request
     * @param String  $uuid
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, $uuid)
    {

        /** @var Folder $folder */
        $folder = $this->getDoctrine()->getManager()->getRepository(Folder::class)->findUuid($uuid);
        if ($folder !== null) {

            $form = $this->createFormBuilder($folder)
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'Delete' => 1,
                        'Shift up' => 0
                    ],
                    'mapped' => false,
                    'expanded' => true,
                    'multiple' => false,
                ])
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                switch ($form['type']->getData())
                {
                    case 0:
                        foreach ($folder->getChildFolders() as $f)
                        {
                            // Set child's parent to parent of folder in question
                            $f->setParentFolder($f->getParentFolder()->getParentFolder());
                        }
                        foreach ($folder->getChildFiles() as $f)
                        {
                            $f->setFolder($f->getFolder()->getParentFolder());
                        }

                        $this->entityManager->flush();
                        $this->entityManager->remove($folder);
                        break;

                    case 1:
                        // Recursion \o/ (this worked on the first try too, oh yeah)
                        function recursiveDelete(EntityManagerInterface $em, Folder $folder)
                        {
                            // Iterate through every folder
                            if ($folder->hasChildFolders())
                            {
                                foreach($folder->getChildFolders() as $f)
                                {
                                    recursiveDelete($em, $f);
                                }
                            }

                            // Start deleting once bottom is reached
                            foreach($folder->getChildFiles() as $f)
                            {
                                $em->remove($f);
                            }
                            $em->remove($folder);
                            $em->flush();
                        }
                        recursiveDelete($this->entityManager, $folder);
                        break;
                }

                // Flush any remaining changes
                $this->entityManager->flush();

                return $this->redirectToRoute("site_view_folder", [
                    'folder' => $folder->getParentFolder() !== null ? $folder->getParentFolder()->getUuid() : null
                ]);
            }

            return $this->renderForm('file_system/folder/delete.html.twig', [
                'folderUuid' => $folder->getUuid(),
                'form' => $form
            ]);
        }

    }
}