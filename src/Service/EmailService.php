<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailService {
    // PROPRIETE : Contient quelque chose (valeur primaire, int, float, string, ou un objet)
    // Caratéristique 
    private $coeur;
    
    // CONSTRUCTEUR
    public function __construct(MailerInterface $mailer) {
        // On met l'objet MailerInterface dans le $mailer
        // à l'intérieur du coeur de l'objet courant (cad objet en cours de construction
        // Qui est l'objet Email Service) dénommé $this.
        $this->coeur = $mailer;
    }
    // METHODE : le but est de faire quelque chose et dy retourner un résultat  (sauf void)
    // Comportement
    public function envoyer(array $table,string $vue,string $from,string $destination, string $objet): void {

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($destination)
            ->subject($objet)
            ->htmlTemplate($vue)
            ->context($table);

            $this->coeur->send($email);

    }
}