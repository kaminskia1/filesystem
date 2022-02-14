<?php

namespace App\Twig;

use App\Entity\Folder;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $packages;

    private $kernel;

    public function __construct(Packages $packages, KernelInterface $kernel)
    {
        $this->packages = $packages;
        $this->kernel = $kernel;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('file_exists', [$this, 'fileExists']),
            new TwigFunction('is_valid_folder', [$this, 'isValidFolder']),
        ];
    }

    /**
     * @param             $path
     * @param string|null $packageName
     *
     * @return bool
     */
    public function fileExists($path, string $packageName = null)
    {
        // Grab installation directory and versioned asset location. Glue together with public directory
        return file_exists($this->kernel->getProjectDir() . "/public" . $this->packages->getUrl($path));
    }

    /**
     * @param Folder|mixed $folder
     *
     * @return bool
     */
    public function isValidFolder($folder): bool
    {
        if ($folder instanceof Folder && $folder->getName() !== "..")
        {
            return true;
        }
        return false;
    }
}
