<?php
ob_start();
require_once '../config/connexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_GET['file'])) {
    try {
        $rawFile = urldecode($_GET['file']);
        $safeFile = basename($rawFile);
        
        $basePath = realpath(__DIR__ . '/../uploads/documents');
        $filePath = $basePath . DIRECTORY_SEPARATOR . $safeFile;

        if(!$basePath || !file_exists($filePath)) {
            throw new Exception("Fichier introuvable : " . htmlspecialchars($safeFile));
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filePath);
        
        $allowedMimeTypes = [
            'application/pdf',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        if (!in_array($mime, $allowedMimeTypes)) {
            throw new Exception("Type de fichier invalide : $mime");
        }

        if (!is_readable($filePath)) {
            throw new Exception("Permissions insuffisantes");
        }

        // Gestion des PDF
        if ($mime === 'application/pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . rawurlencode($safeFile) . '"');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: public, must-revalidate, max-age=3600');
            ob_end_clean();
            readfile($filePath);
            exit;
        } 
        // Gestion des fichiers Office
        else {
            if (!isset($_GET['direct'])) {
                // Construction de l'URL pour Google Viewer
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                $directUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?file=' . urlencode($safeFile) . '&direct=1';
                $gviewUrl = 'https://docs.google.com/gview?embedded=true&url=' . urlencode($directUrl);
                header('Location: ' . $gviewUrl);
                exit;
            } else {
                // En-têtes pour les fichiers Office
                header('Content-Type: ' . $mime);
                header('Content-Disposition: inline; filename="' . rawurlencode($safeFile) . '"');
                header('Content-Length: ' . filesize($filePath));
                header('Cache-Control: public, must-revalidate, max-age=3600');
                header('Access-Control-Allow-Origin: *'); // Autorise CORS
                ob_end_clean();
                readfile($filePath);
                exit;
            }
        }

    } catch (Exception $e) {
        ob_end_clean();
        http_response_code(500);
        die("Erreur : " . $e->getMessage() . 
            "<br>Chemin : " . htmlspecialchars($filePath));
    }
}

ob_end_clean();
http_response_code(400);
die("Paramètre 'file' requis");