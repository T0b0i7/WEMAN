<?php
class UploadException extends Exception {
    private $customErrors = [
        UPLOAD_ERR_INI_SIZE => "Le fichier dépasse la taille maximale autorisée par PHP.ini",
        UPLOAD_ERR_FORM_SIZE => "Le fichier dépasse la taille maximale autorisée par le formulaire",
        UPLOAD_ERR_PARTIAL => "Le fichier n'a été que partiellement téléversé",
        UPLOAD_ERR_NO_FILE => "Aucun fichier n'a été téléversé",
        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant",
        UPLOAD_ERR_CANT_WRITE => "Échec de l'écriture du fichier sur le disque",
        UPLOAD_ERR_EXTENSION => "Une extension PHP a arrêté le téléversement"
    ];

    public function __construct($code) {
        $message = isset($this->customErrors[$code]) 
            ? $this->customErrors[$code] 
            : "Erreur inconnue lors du téléversement";
        parent::__construct($message, $code);
    }
}
