<?php 
// Le dossier virtuel de la class de ce fichier
// src/Service/FileUploader.php
namespace App\Service;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
// Utilisation de UploadedFile afin d'Uploaded les images
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    // Création de deux variables
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        // Initialisation des variables targetDirectory et slugger (hydratation)
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        // Création d'une variable qui prend le chemin d'accès au fichier
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Création d'un Slug permettant d'identifier le fichier
        $safeFilename = $this->slugger->slug($originalFilename);
        // Création d'une variable qui génère un identifiant unique concaténé avec safeFilename et le guessExtention contenu dans la variable file
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            // Essaie de d'envoyer le fichier dans le targetDirectory défini à travers la variable $fileName
            $file->move($this->getTargetDirectory(), $fileName);
            // S'il y a une erreur ou exception possibilité de créer des conditions
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        // retourne le chemin du fichier 
        return $fileName;
    }

    public function getTargetDirectory()
    {
        // retourne le targetDirectory
        return $this->targetDirectory;
    }
}