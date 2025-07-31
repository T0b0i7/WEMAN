<?php
// Si c'est une requête AJAX ou une requête qui attend du JSON, ne rien afficher
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
    return;
}

// Sinon, afficher le HTML de protection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Inclure le fichier JavaScript externe -->
    <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <script src="../assets/js/protect-source.js"></script>
</head>
<body>
    <h1></h1>
    <p></p>
</body>
</html>