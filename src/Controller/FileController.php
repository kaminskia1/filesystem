<?php

namespace App\Controller;

use App\Entity\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class FileController
 *
 * @isGranted("ROLE_ADMIN")
 * @package App\Controller
 */
class FileController extends AbstractController
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
     * Add file controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/file/add", name="site_add_file_root")
     * @Route("/file/add/{parentUuid}", name="site_file_add")
     *
     * @param Request          $request
     * @param SluggerInterface $slugger
     * @param null             $parentUuid
     *
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, SluggerInterface $slugger, $parentUuid = null)
    {
        // Instance a new File
        $baseFile = new File();

        // If not root, attempt to retrieve file
        if ($parentUuid !== null) {
            $parent = $this->getDoctrine()->getManager()->getRepository(Folder::class)->findUuid($parentUuid);
            if ($parent === null) {
                throw new Exception("Invalid parent folder UUID");
            }
            $baseFile->setFolder($parent);
        }

        /* @TODO: Add filesize limiter env variable */
        $form = $this->createFormBuilder($baseFile)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Local' => 0,
                    'External' => 1
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            /* External Options */
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
            ])
            /* Internal Options */
            /* @TODO: Ensure that upload directory is non-executable */
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'multiple' => 'multiple'
                ]
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var File $baseFile */
            $baseFile = $form->getData();

            // If local upload
            if ($baseFile->getType() == 0) {
                /** @var UploadedFile[] $uploadFile */
                $uploadFile = $form->get('file')->getData();

                foreach ($uploadFile as $uf) {
                    // Create file instance
                    $file = new File();

                    // Copy attributes from base file; append uploaded file's name
                    $file->setFolder($baseFile->getFolder());
                    $file->setType($baseFile->getType());
                    $file->setName($uf->getClientOriginalName());

                    // Create slugged name
                    $newFilename = $slugger->slug($file->getName()) . '.' . uniqid() . '.' .
                        $uf->guessExtension();

                    $uf->move('uploads', $newFilename);
                    $file->setUrl("/uploads/" . $newFilename);

                    // Save new file
                    $this->entityManager->persist($file);
                }

            // If external url
            } else {
                if ($baseFile->getType() == 1 && !in_array($baseFile->getName(), [null, ''])) {
                    $this->entityManager->persist($baseFile);
                } else {
                    return $this->redirectToRoute("site_view_folder", [
                        'folder' => $baseFile->getFolder() !== null ? $baseFile->getFolder()->getUuid() : null
                    ]);
                }
            }

            // Save all changes
            $this->entityManager->flush();

            return $this->redirectToRoute("site_view_folder", [
                'folder' => $baseFile->getFolder() !== null ? $baseFile->getFolder()->getUuid() : null
            ]);
        }

        return $this->renderForm('file_system/file/add.html.twig', [
            'parent' => $parentUuid,
            'form' => $form
        ]);

    }

    /**
     * Rename file controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/file/rename/{uuid}", name="site_file_rename")
     *
     * @param Request     $request
     * @param String|null $uuid
     *
     * @return Response
     * @throws Exception
     */
    public function rename(Request $request, $uuid = null)
    {
        /** @var Folder $folder */
        $file = $this->getDoctrine()->getManager()->getRepository(File::class)->findUuid($uuid);
        if ($file !== null) {
            $form = $this->createFormBuilder($file)
                ->add('name', TextType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                /** @var File $file */
                $file = $form->getData();

                $this->entityManager->persist($file);
                $this->entityManager->flush();

                return $this->redirectToRoute("site_view_folder", [
                    'folder' => $file->getFolder() !== null ? $file->getFolder()->getUuid() : null
                ]);
            }

            return $this->renderForm('file_system/file/rename.html.twig', [
                'fileUuid' => $file->getUuid(),
                'form' => $form
            ]);
        }
        return new Response(404, 404);
    }

    /**
     * Delete file controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/file/delete/{uuid}", name="site_file_delete")
     *
     * @param Request     $request
     * @param String|null $uuid
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, $uuid = null)
    {

        /** @var Folder $folder */
        $file = $this->getDoctrine()->getManager()->getRepository(File::class)->findUuid($uuid);
        if ($file !== null) {

            $form = $this->createFormBuilder($file)
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                // If is local file
                if ($file->getType() == 0) {
                    $fs = new Filesystem();
                    $exp = explode("/", $file->getUrl());
                    $dir = 'uploads/' . array_pop($exp);
                    if ($fs->exists($dir)) {
                        $fs->remove($dir);
                    }
                }
                // Flush any remaining changes
                $this->entityManager->remove($file);
                $this->entityManager->flush();

                return $this->redirectToRoute("site_view_folder", [
                    'folder' => $file->getFolder() !== null ? $file->getFolder()->getUuid() : null
                ]);
            }

            return $this->renderForm('file_system/file/delete.html.twig', [
                'fileUuid' => $file->getUuid(),
                'form' => $form
            ]);
        }
        return new Response(404, 404);

    }

    /**
     * Move file controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/file/move/{uuid}", name="site_file_move")
     *
     * @param Request     $request
     * @param String|null $uuid
     *
     * @return Response
     * @throws Exception
     */
    public function move(Request $request, $uuid = null)
    {
        /** @var Folder $folder */
        $file = $this->getDoctrine()->getManager()->getRepository(File::class)->findUuid($uuid);
        if ($file !== null) {
            $form = $this->createFormBuilder($file)
                ->add('folder', EntityType::class, [
                    'class' => Folder::class,
                    'label' => 'Location',
                    'placeholder' => '/',
                    'required' => false,
                ])
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form->getData();

                $this->entityManager->persist($file);
                $this->entityManager->flush();

                return $this->redirectToRoute("site_view_folder", [
                    'folder' => $file->getFolder() !== null ? $file->getFolder()->getUuid() : null
                ]);
            }

            return $this->renderForm('file_system/file/move.html.twig', [
                'fileUuid' => $file->getUuid(),
                'form' => $form
            ]);
        }
        return new Response(404, 404);
    }
}