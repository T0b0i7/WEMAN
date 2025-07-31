<?php
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/documents/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50 MB
define('ALLOWED_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'image/jpeg',
    'image/png'
]);

define('UPLOAD_ERROR_MESSAGES', [
    'INVALID_TYPE' => 'Type de fichier non autorisé. Types acceptés: PDF, Word, Excel, PowerPoint, JPG, PNG',
    'FILE_TOO_LARGE' => 'Le fichier est trop volumineux. Taille maximale: 50MB',
    'DIRECTORY_ERROR' => 'Erreur lors de la création du dossier de destination',
    'MOVE_ERROR' => 'Erreur lors du déplacement du fichier',
    'DB_ERROR' => 'Erreur lors de l\'enregistrement dans la base de données',
    'SESSION_ERROR' => 'Session utilisateur invalide'
]);

function getHumanFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}
