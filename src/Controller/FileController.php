<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class FileController
 *
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
     * @Route("/file/add/{parentUuid}", name="site_add_file")
     *
     * @param String $parentUuid
     *
     * @return Response
     */
    public function add($parentUuid = null)
    {


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

    }

    /**
     * Delete file controller
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/file/delete/{uuid}", name="site_file_rename")
     *
     * @param Request     $request
     * @param String|null $uuid
     *
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, $uuid = null)
    {

    }
}