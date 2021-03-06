<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Folder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class FilesystemController
 *
 * @package App\Controller
 */

class FilesystemController extends AbstractController
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
     * Main view controller
     *
     * @param Request     $request
     * @param String|null $folder
     *
     * @return Response
     * @todo: Validate user has access to folder/file
     *
     * @Route("/", name="site_view")
     * @Route("/path/{folder}", name="site_view_folder")
     *
     */
    public function view(Request $request, $folder = null): Response
    {

        // Null means root ("/") is being requested. Generate root if no folder provided.
        if (is_null($folder)) {
            $folder = $this->_getRootFolder();
        } else {
            $folder = $this->getDoctrine()->getRepository(Folder::class)->findUuid($folder);
        }

        /**
         * @var Folder $folder
         */

        // Check that folder exists
        if (is_null($folder)) {
            return $this->render($request->isXmlHttpRequest() ? 'file_system/error_ajax.html.twig' : 'file_system/error.html.twig', [
                'user' => $this->getUser(),
                'type' => 404
            ]);
        }

        // Render template
        return $this->render($request->isXmlHttpRequest() ? 'file_system/view_ajax.html.twig' : 'file_system/view.html.twig', [
            'user' => $this->getUser(),
            'directory' => $folder->getPath(),
            'explorerTree' => $this->_getExplorerTree(clone $folder),
            'head' => $folder,

            // ternary > lambda [refactor this]
            // If folder is set, display said.
            'folder' => is_null($folder->getUuid()) ?
                $folder->getChildFolders()

                // If parent folder does not exist, display root folder
                : (is_null($folder->getParentFolder()) ?
                    [(clone $this->_getRootFolder())->setName(".."), ...$folder->getChildFolders()]

                    // Otherwise, display parent folder.
                    : [(clone $folder->getParentFolder())->setName(".."), ...$folder->getChildFolders()]
                ),
            'files' => $folder->getChildFiles()
        ]);
    }

    /**
     * Generate a Folder object for the root (/) location
     *
     * @return Folder
     */
    protected function _getRootFolder()
    {
        $folder = new Folder("");
        $folder->setUuid(null);

        // Grab all orphan folders
        foreach ($this->getDoctrine()->getRepository(Folder::class)->findBy(['parentFolder' => null]) as $f) {
            /* @var Folder $f */
            $folder->addContentsFolder($f);
        }

        // Grab all orphan files
        foreach ($this->getDoctrine()->getRepository(File::class)->findBy(['folder' => null]) as $f) {
            /* @var File $f */
            $folder->addContentsFile($f);
        }
        return $folder;

    }

    /**
     * Generate a tree structure based on the requested folder's location
     *
     * @param Folder $folder
     *
     * @return Folder
     * @todo: Verify user has access to folders/files mentioned in tree. Throw global error if un-allowed is found.
     *
     */
    protected function _getExplorerTree(Folder $folder)
    {

        // Nullfiy folder's second sublevel (children's children). Ensure that at least one iteration is completed
        $child = null;
        do {
            foreach ($folder->getChildFolders() as $sub) {
                if ($sub->getChildFolders()->count() > 0) {
                    $sub->hasChildren = true;
                }
                if ($sub->getUuid() !== $child) {
                    $sub->setChildFolders(new ArrayCollection());
                }

            }
            $child = $folder->getUuid();
            $folder = $folder->getParentFolder();
        } while ($folder != null);


        foreach ($this->_getRootFolder()->getChildFolders() as $sub) {
            if ($sub->getChildFolders()->count() > 0) {
                $sub->hasChildren = true;
            }
            if ($sub->getUuid() !== $child) {
                $sub->setChildFolders(new ArrayCollection());
            }
        }
        return $this->_getRootFolder();
    }

    /**
     * Explorer node controller
     *
     * @Route("/explorer/stem/{uuid}", name="ajax_explorer_stem")
     *
     * @param String $uuid
     *
     * @return Response
     */
    public function getExplorerNode($uuid = null)
    {
        // Null means root ("/") is being requested. Generate root if not provided.
        if (is_null($uuid)) {
            $folder = $this->_getRootFolder();
        } else {
            $folder = $this
                ->getDoctrine()
                ->getRepository(Folder::class)
                ->findUuid($uuid);
        }

        return $this->render("file_system/explorer_node.html.twig", [
            'folders' => $folder->getChildFolders()
        ]);
    }


}
