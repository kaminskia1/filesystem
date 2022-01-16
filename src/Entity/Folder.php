<?php

namespace App\Entity;

use App\Repository\FolderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=FolderRepository::class)
 */
class Folder
{
    /**
     * @var bool Flag for explorer generator
     * @todo deprecate
     */
    public $hasChildren = false;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $uuid;
    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="folder")
     */
    private $childFiles;
    /**
     * Self-referencing relationship.
     *
     * @ORM\OneToMany(targetEntity=Folder::class, mappedBy="parentFolder")
     */
    private $childFolders;

    /**
     * @ORM\ManyToOne(targetEntity=Folder::class, inversedBy="contentsFolders")
     */
    private $parentFolder;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function __construct($name = null)
    {
        $this->uuid = Uuid::v4();
        $this->name = $name;
        $this->childFiles = new ArrayCollection();
        $this->childFolders = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getPath();
    }

    public function getPath()
    {
        $arr = [];
        $this->_getPath($this, $arr);

        // Unset last element, as it is the root

        // Reverse the array and prepend a / to indicate the root
        $path = "";
        foreach ($arr as $ele) {
            if (mb_strlen($path) < 35) {
                $path = "/$ele" . $path;
            } else {
                $path = "/.." . $path;
                break;
            }
        }
        return $path;
    }

    private function _getPath(Folder $self, &$path)
    {
        array_push($path, $self->getName());
        if (!is_null($self->getParentFolder())) {
            $this->_getPath($self->getParentFolder(), $path);
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParentFolder(string $name = null): ?self
    {

        return $this->parentFolder;
    }

    public function setParentFolder(?self $parentFolder): self
    {
        $this->parentFolder = $parentFolder;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|File[]
     */
    public function getChildFiles(): Collection
    {
        return $this->childFiles;
    }

    public function addContentsFile(File $contentsFile): self
    {
        if (!$this->childFiles->contains($contentsFile)) {
            $this->childFiles[] = $contentsFile;
            $contentsFile->setFolder($this);
        }

        return $this;
    }

    public function removeContentsFile(File $contentsFile): self
    {
        if ($this->childFiles->removeElement($contentsFile)) {
            // set the owning side to null (unless already changed)
            if ($contentsFile->getFolder() === $this) {
                $contentsFile->setFolder(null);
            }
        }

        return $this;
    }

    public function hasChildFolders(): bool
    {
        return $this->childFolders->count() > 0;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildFolders(): Collection
    {
        return $this->childFolders;
    }

    /**
     * Does not remove other side's relationship. Only utilize this to modify local instances
     *
     * @param Collection|null $content
     *
     * @return $this
     */
    public function setChildFolders(?Collection $content): self
    {
        $this->childFolders = $content;
        return $this;
    }


    public function addContentsFolder(self $contentsFolder): self
    {
        if (!$this->childFolders->contains($contentsFolder)) {
            $this->childFolders[] = $contentsFolder;
            $contentsFolder->setParentFolder($this);
        }

        return $this;
    }

    public function removeContentsFolder(self $contentsFolder): self
    {
        if ($this->childFolders->removeElement($contentsFolder)) {
            // set the owning side to null (unless already changed)
            if ($contentsFolder->getParentFolder() === $this) {
                $contentsFolder->setParentFolder(null);
            }
        }

        return $this;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getExtension()
    {
        return 'folder';
    }


}
