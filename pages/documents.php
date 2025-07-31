<?php
// filepath: c:\xampp\htdocs\WEMAN\pages\documents.php

// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once '../config/connexion.php';
require_once 'protect-source.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit();
}

// Récupérer les catégories depuis la base de données
$sqlCategories = "SELECT id, nom FROM categories_documents WHERE active = 1";
$stmtCategories = $pdo->prepare($sqlCategories);
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les documents en fonction de la catégorie sélectionnée et de la recherche
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$sqlDocuments = "SELECT d.*, c.nom AS categorie_nom 
                 FROM documents d 
                 JOIN categories_documents c ON d.categorie_id = c.id";

$conditions = [];
$params = [];

if (!empty($selectedCategory)) {
    $conditions[] = "c.id = :categoryId";
    $params[':categoryId'] = $selectedCategory;
}

if (!empty($searchQuery)) {
    $conditions[] = "(d.titre LIKE :search OR d.contenu LIKE :search)";
    $params[':search'] = '%' . $searchQuery . '%';
}

if (!empty($conditions)) {
    $sqlDocuments .= " WHERE " . implode(' AND ', $conditions);
}

// Définir le nombre de documents par page
$documentsParPage = 15;

// Récupérer la page actuelle depuis l'URL (par défaut : 1)
$pageActuelle = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Calculer l'offset pour la requête SQL
$offset = ($pageActuelle - 1) * $documentsParPage;

// Ajouter la limite et l'offset à la requête SQL
$sqlDocuments .= " LIMIT :limit OFFSET :offset";

$stmtDocuments = $pdo->prepare($sqlDocuments);

// Lier les paramètres dynamiques (catégorie et recherche)
foreach ($params as $key => $value) {
    $stmtDocuments->bindValue($key, $value);
}

// Ajouter les paramètres de limite et d'offset
$stmtDocuments->bindValue(':limit', $documentsParPage, PDO::PARAM_INT);
$stmtDocuments->bindValue(':offset', $offset, PDO::PARAM_INT);

// Exécuter la requête
$stmtDocuments->execute();
$documents = $stmtDocuments->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre total de documents pour calculer le nombre de pages
$sqlCount = "SELECT COUNT(*) FROM documents d JOIN categories_documents c ON d.categorie_id = c.id";
if (!empty($conditions)) {
    $sqlCount .= " WHERE " . implode(' AND ', $conditions);
}
$stmtCount = $pdo->prepare($sqlCount);
foreach ($params as $key => $value) {
    $stmtCount->bindValue($key, $value);
}
$stmtCount->execute();
$totalDocuments = $stmtCount->fetchColumn();

// Calculer le nombre total de pages
$totalPages = ceil($totalDocuments / $documentsParPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <title>Documents - WEMANTCHE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;600&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js"></script>
</head>
<style>
/* Navigation Styles */
.navbar {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    padding: 15px 0;
}

.navbar-brand {
    font-family: 'Montserrat', sans-serif;
    transition: transform 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
}

.nav-link {
    position: relative;
    padding: 8px 15px !important;
    margin: 0 5px;
    font-weight: 500;
    color: #333 !important;
    transition: all 0.3s ease;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: #0d6efd;
    transition: all 0.3s ease;
}

.nav-link:hover::after,
.nav-link.active::after {
    width: 100%;
}

.nav-link i {
    transition: transform 0.3s ease;
}

.nav-link:hover i {
    transform: translateY(-2px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.navbar .nav-item {
    animation: fadeInUp 0.6s ease backwards;
    animation-delay: calc(0.1s * var(--animation-order));
}

/* Dropdown Styles */
.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
    padding: 0.5rem;
}

.dropdown-item {
    padding: 0.6rem 1rem;
    border-radius: 0.3rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: rgba(13, 110, 253, 0.1);
    transform: translateX(5px);
}

/* Animation du dropdown */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate.slideIn {
    animation: slideIn 0.3s ease;
}
</style>
<body>
<!-- Header Navigation -->
<header class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <span style="color: #04013F; font-weight: 700; font-size: 1.5rem;">WEMAN</span><span style="color: #0d6efd; font-weight: 700; font-size: 1.5rem;">TCHE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item" style="--animation-order: 1">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 2">
                    <a class="nav-link active" href="documents.php">
                        <i class="fas fa-file-alt me-1"></i>Documents
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 3">
                    <a class="nav-link" href="services.php">
                        <i class="fas fa-cogs me-1"></i>Services
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 4">
                    <a class="nav-link" href="redaction.php">
                        <i class="fas fa-pen-fancy me-1"></i>Rédaction
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 5">
                    <a class="nav-link" href="apropos.php">
                        <i class="fas fa-info-circle me-1"></i>À propos
                    </a>
                </li>
            </ul>
            <div class="d-none d-lg-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary rounded-pill dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>Mon Compte
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2 text-primary"></i>Mon Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary rounded-pill me-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                    <a href="register.php" class="btn btn-primary rounded-pill">
                        <i class="fas fa-user-plus me-2"></i>Inscription
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Sidebar for mobile -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
    <a class="navbar-brand" href="../index.php">
            <span class="ms-2">
                <span style="color: #04013F;">WEMAN</span><span style="color: #87CEEB;">TCHE</span>
            </span>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
    <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
            <li class="nav-item">
                <!-- Lien direct simplifié -->
                <a class="nav-link" href="documents.php">Documents</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="redaction.php">Rédaction sur mesure</a></li>
           
            <li class="nav-item"><a class="nav-link" href="apropos.php">À propos</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Mon Compte</a></li>
        </ul>
    </div>
</div>

<!-- Header Section -->
<section class="documents-header bg-light py-5 mt-5">
    <div class="container pt-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="fw-bold mb-4">Documents</h1>
                <p class="lead">Trouvez tous les documents dont vous avez besoin</p>
            </div>
            <div class="col-lg-6">
                <!-- Filtres -->
                <div class="search-filters p-4 bg-white rounded shadow-sm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <select class="form-select" id="categoryFilter" onchange="filterByCategory(this.value)">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['id']) ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="documents.php">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '') ?>">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Documents Grid -->
<section class="documents-grid py-5">
    <div class="container">
        <div class="row">
            <?php if (empty($documents)): ?>
                <div class="alert alert-warning text-center">
                    Aucun document trouvé pour votre recherche.
                </div>
            <?php else: ?>
                <?php foreach ($documents as $document): ?>
                    <div class="col-md-3 mb-4"> <!-- Remplacez col-md-4 par col-md-3 -->
                        <div class="card h-100">
                            <div class="card-header">
                                <span class="badge bg-info"><?php echo ucfirst($document['categorie_nom']); ?></span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><strong> Titre: </strong><?php echo $document['titre']; ?></h5>
                                <p class="card-text"><strong> Contenu: </strong><?php echo $document['contenu']; ?></p>
                                <p class="card-text"><strong> Taille: </strong> <?php echo $document['taille_fichier']; ?> Ko</p>
                                <p class="card-text"><strong> Type: </strong> <?php echo $document['type_fichier']; ?></p>
                                <p class="card-text"><strong> Langues:</strong> <?php echo $document['langues']; ?></p>
                                <div class="document-stats d-flex justify-content-between mb-3">
                                    <span><i class="fas fa-download me-1"></i> <?php echo $document['downloads_count']; ?> téléchargements</span>
                                    <span><i class="fas fa-star text-warning me-1"></i> <?php echo $document['notes']; ?>/5</span>
                                </div>
                                <div class="document-price mb-3">
                                    <span class="h5 mb-0"><?php echo number_format($document['prix'], 2, ',', ' '); ?> FCFA</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary flex-grow-1 preview-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#previewModal"
                                        data-file="<?= htmlspecialchars($document['file_path']) ?>"
                                        data-type="<?= htmlspecialchars($document['type_fichier']) ?>">
                                        <i class="fas fa-eye me-1"></i>Aperçu
                                    </button>
                                    <a href="#" class="btn btn-sm btn-primary flex-grow-1 download-btn" data-id="<?= $document['id'] ?>">
                                        <i class="fas fa-shopping-cart me-1"></i>Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- Pagination -->
        <nav aria-label="Pagination">
            <ul class="pagination justify-content-center">
                <!-- Lien vers la page précédente -->
                <li class="page-item <?= $pageActuelle <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $pageActuelle - 1 ?>" aria-label="Précédent">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Liens vers les pages -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $pageActuelle ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Lien vers la page suivante -->
                <li class="page-item <?= $pageActuelle >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $pageActuelle + 1 ?>" aria-label="Suivant">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</section>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel">
    <div class=
    "modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu du document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pdf-viewer">
                    <div id="pdf-controls" class="mb-3">
                        <!-- Contrôles PDF -->
                    </div>
                    <canvas id="pdf-canvas" class="w-100"></canvas>
                </div>
                <div id="office-viewer" style="display: none; height: 80vh;">
                    <iframe id="office-iframe" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
                <div class="blur-overlay">Aperçu limité - Veuillez télécharger pour voir plus</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Password Confirmation Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Confirmation du mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">Entrez votre mot de passe pour confirmer</label>
                        <input type="password" class="form-control" id="passwordInput" required>
                    </div>
                    <input type="hidden" id="documentIdInput">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmPasswordBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4">
            <!-- À propos -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 fw-bold">À propos de WEMANTCHE</h5>
                <p class="text-light mb-3">
                    WEMANTCHE est votre plateforme de confiance pour tous vos besoins en documents académiques et professionnels. Notre mission est de fournir des services de qualité pour votre réussite.
                </p>
                <div class="social-links mt-4">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>

            <!-- Liens Rapides -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 fw-bold">Liens Rapides</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="documents.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Documents
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="services.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Services
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="redaction.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Rédaction sur mesure
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="apropos.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>À propos
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 fw-bold">Contactez-nous</h5>
                <ul class="list-unstyled">
                    <li class="mb-3 text-light">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Porto-Novo, Bénin
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:wemanwade@gmail.com" class="text-light text-decoration-none">wemanwade@gmail.com</a>
                    </li>
                    <li class="mb-3 text-light">
                        <i class="fas fa-phone me-2"></i>
                        +229 01 49 48 98 71
                    </li>
                    <li class="mb-3 text-light">
                        <i class="fas fa-clock me-2"></i>
                        Lun - Sam: 8h00 - 18h00
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row mt-4">
            <div class="col-12">
                <hr class="bg-light opacity-25">
                <p class="text-center text-light mb-0">
                    © <?php echo date('Y'); ?> WEMANTCHE. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
    // Configuration PDF.js
    const defaultScale = 1.5;
    let currentPDF = null;

    // Gestionnaire d'aperçu
    document.querySelectorAll('.preview-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filePath = 'preview.php?file=' + encodeURIComponent(this.dataset.file);
            loadPDF(filePath);
        });
    });

    async function loadPDF(url) {
        try {
            const loadingTask = pdfjsLib.getDocument(url);
            currentPDF = await loadingTask.promise;
            renderFirstPage();
        } catch (err) {
            console.error('Erreur de chargement du PDF:', err);
            alert('Impossible de charger le document');
        }
    }

    function renderFirstPage() {
        if (!currentPDF) return;

        currentPDF.getPage(1).then(page => {
            const canvas = document.getElementById('pdf-canvas');
            const ctx = canvas.getContext('2d');
            const viewport = page.getViewport({ scale: defaultScale });

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = { canvasContext: ctx, viewport };
            page.render(renderContext).promise.then(() => {
                document.getElementById('page-num').textContent = 1;
                document.getElementById('page-count').textContent = currentPDF.numPages;
            });
        });
    }

    // Réinitialiser à la fermeture du modal
    document.getElementById('previewModal').addEventListener('hidden.bs.modal', () => {
        currentPDF = null;
        document.getElementById('pdf-canvas').getContext('2d').clearRect(0, 0, 
            document.getElementById('pdf-canvas').width, 
            document.getElementById('pdf-canvas').height
        );
    });

    function filterByCategory(categoryId) {
        const url = new URL(window.location.href);
        url.searchParams.set('category', categoryId); // Met à jour le paramètre "category"
        window.location.href = url.toString(); // Redirige vers l'URL mise à jour
    }
</script>
<script>
document.querySelectorAll('.preview-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filePath = this.dataset.file;
        const fileType = this.dataset.type;
        const pdfViewer = document.getElementById('pdf-viewer');
        const officeViewer = document.getElementById('office-viewer');
        const officeIframe = document.getElementById('office-iframe');

        // Afficher d'abord le message de sécurité
        Swal.fire({
            title: 'Information de sécurité',
            html: `
                <div class="text-center">
                    <i class="fas fa-lock text-warning mb-3" style="font-size: 2em;"></i>
                    <p>Pour des raisons de sécurité, le document a été tourné à l'envers.</p>
                    <p class="small text-muted">Pour voir le document dans son orientation normale, veuillez le télécharger.</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Compris',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Après confirmation, afficher l'aperçu du document
                if (fileType === 'application/pdf') {
                    pdfViewer.style.display = 'block';
                    officeViewer.style.display = 'none';
                    // Rotation du canvas pour le PDF
                    const canvas = document.getElementById('pdf-canvas');
                    canvas.style.transform = 'rotate(180deg)';
                    loadPDF('preview.php?file=' + encodeURIComponent(filePath));
                } else {
                    pdfViewer.style.display = 'none';
                    officeViewer.style.display = 'block';
                    const baseUrl = window.location.origin;
                    const directUrl = `${baseUrl}/preview.php?file=${encodeURIComponent(filePath)}&direct=1`;
                    const viewerUrl = `https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(directUrl)}`;
                    officeIframe.src = viewerUrl;
                    // Rotation de l'iframe pour les documents Office
                    officeIframe.style.transform = 'rotate(180deg)';
                }
            }
        });
    });
});

// Ajouter un style pour la rotation
const style = document.createElement('style');
style.textContent = `
    // ...existing styles...
    
    #pdf-canvas, #office-iframe {
        transition: transform 0.5s ease;
    }

    .preview-warning {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 1rem;
        border-radius: 8px;
        z-index: 1000;
    }
`;
document.head.appendChild(style);

// Réinitialiser la rotation à la fermeture du modal
document.getElementById('previewModal').addEventListener('hidden.bs.modal', () => {
    document.getElementById('office-iframe').src = '';
    document.getElementById('office-iframe').style.transform = '';
    const canvas = document.getElementById('pdf-canvas');
    canvas.style.transform = '';
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    currentPDF = null;
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.download-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const documentId = this.dataset.id;
            document.getElementById('documentIdInput').value = documentId;
            const passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
            passwordModal.show();
        });
    });

    document.getElementById('confirmPasswordBtn').addEventListener('click', function () {
        const password = document.getElementById('passwordInput').value;
        const documentId = document.getElementById('documentIdInput').value;

        if (!password) {
            Swal.fire('Erreur', 'Veuillez entrer votre mot de passe.', 'error');
            return;
        }

        // Envoi de la requête AJAX pour vérifier le mot de passe
        fetch('verify_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ password, documentId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Succès', 'Mot de passe confirmé. Téléchargement en cours...', 'success');
                window.location.href = `download.php?id=${documentId}`;
            } else {
                Swal.fire('Erreur', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire('Erreur', 'Une erreur est survenue. Veuillez réessayer.', 'error');
        });
    });
</script>
<script>
function checkNewDocuments() {
    fetch('check_new_documents.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    showCloseButton: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'info',
                    title: `${data.count} nouveau(x) document(s) disponible(s)`,
                    text: data.titles.join(', '),
                    customClass: {
                        popup: 'simple-toast'
                    }
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Style pour la notification
const style = document.createElement('style');
style.textContent = `
    .simple-toast {
        background: #fff !important;
        color: #333 !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1) !important;
    }
    .simple-toast .swal2-close:focus {
        box-shadow: none !important;
    }
`;
document.head.appendChild(style);

// Vérifier les nouveaux documents au chargement de la page
document.addEventListener('DOMContentLoaded', checkNewDocuments);

// Vérifier périodiquement les nouveaux documents (toutes les 5 minutes)
setInterval(checkNewDocuments, 300000);
</script>
<script>
function checkNewDocuments() {
    const notificationShown = localStorage.getItem('notificationShown');
    
    if (notificationShown) {
        return;
    }

    fetch('check_new_documents.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'info',
                    title: `${data.count} nouveau(x) document(s) disponible(s)`,
                    text: data.titles.join(', '),
                    customClass: {
                        popup: 'simple-toast'
                    }
                }).then(() => {
                    localStorage.setItem('notificationShown', 'true');
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Style simplifié
const style = document.createElement('style');
style.textContent = `
    .simple-toast {
        background: #fff;
        color: #333;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1) !important;
    }
`;
document.head.appendChild(style);

// Vérifier les nouveaux documents au chargement de la page
document.addEventListener('DOMContentLoaded', checkNewDocuments);
</script>

</body>
</html>