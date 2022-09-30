<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * 
 * Indique que l'entité a un cycle de vie (événements Doctrine)
 * Appel des méthodes en fonctiondes cycles de vie des entités (avant un persist)
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $name;

    /**
     * @var UploadedFile
     *@Assert\Image(maxSize = "3M")
     */
    private $file;

    /**
     * @var ?string
     * Chemin de l'ancien fichier pour pouvoir le supprimer
     */
    private $oldPath;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
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

    /**
     * Get the value of file
     */ 
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return  self
     */ 
    public function setFile($file) {
        $this->file = $file;
        $this->oldPath = $this->path; //Sauvegarde le chemin de l'ancien fichier pour le supprimer lors de l'upload du nouveau
        $this->path = ""; //Modifie cette valeur pour activer la modification par Doctrine

        return $this;
    }

    /**
     * Retourne le lien vers le dossier de l'upload
     */
    public function getPublicRootDir(): string {
        //lien absolu de notre classe Image __DIR__
        //a quel endroit stocker les iamges : sur cette adresse absolue.
        return __DIR__ . '/../../public/uploads/';
    }

    /**
     * Génération d'un nom de fichier pour éviter les doublons
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * Permet d'appeler automatiquement la méthode avant le persist et de faire un upload
     */
    public function generatePath(): void {
        //Si un fichier a été envoyé
        if ($this->file instanceof UploadedFile) {
            // Génére un chemin de fichier
            $this->path = uniqid('img_').'.'.$this->file->guessExtension();
        }
    }

    /**
     * Déplace le fichier du dossier temp vers notre dossier perso
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload(): void {
        //test si le fichier existe
        if (is_file($this->getPublicRootDir().$this->oldPath)) {
            unlink($this->getPublicRootDir().$this->oldPath);
        }

        if($this->file instanceof UploadedFile) {
            //va chercher le fichier dans le dossier temporaire et le place dans le notre
            $this->file->move($this->getPublicRootDir(), $this->path);
        }
    }

    public function getWebPath(): string {
        return '/uploads/'.$this->path;
    }

    /**
     * @ORM\PreRemove()
     */
    //avant de supprimer l'entité on va supprimer le fichier
    public function remove()
    {
        // Test si le fichier existe
        if (is_file($this->getPublicRootDir().$this->path)) {
            unlink($this->getPublicRootDir().$this->path);
        }
    }
}
