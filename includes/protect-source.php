<?php
// Désactiver l'affichage des erreurs pour la production
error_reporting(0);
ini_set('display_errors', 0);

// Protection contre l'inspection du code source
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

// Empêcher l'accès direct aux fichiers PHP
if (count(get_included_files()) == 1) {
    http_response_code(403);
    die('Accès direct non autorisé');
}

// Protection contre le hotlinking
if (isset($_SERVER['HTTP_REFERER'])) {
    $allowed_domains = array('wemantche.com', 'localhost', '127.0.0.1');
    $domain = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    
    if (!in_array($domain, $allowed_domains)) {
        http_response_code(403);
        die('Accès non autorisé');
    }
}

// Protection contre les méthodes HTTP non autorisées
$allowed_methods = array('GET', 'POST');
if (!in_array($_SERVER['REQUEST_METHOD'], $allowed_methods)) {
    http_response_code(405);
    die('Méthode non autorisée');
}
?>
