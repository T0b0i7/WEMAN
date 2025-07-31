<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Documents - WEMANTCHE Admin</title>
    <link rel="icon" type="image/jpg" href="../../assets/images/WEMANTCHE LOGO p 2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  
</head>
    <style>
        /* Variables et thème sombre */
        :root {
            --bg-dark: #1a1f2d;
            --bg-card: #242a38;
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #2dd4bf;
            --danger: #f43f5e;
            --warning: #f59e0b;
            --info: #3b82f6;
            --text-light: #94a3b8;
            --text-white: #ffffff;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        /* Style de base */
        body {
            background: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 300px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border-color);
            background: rgba(67, 97, 238, 0.1);
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
        }

        .sidebar-header h5 {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-link {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 15px;
          color: var(--text-white); /* Changé en blanc */
            transition: all 0.3s ease;
        }

        .nav-link i {
            font-size: 1.25rem;
            width: 30px;
            text-align: center;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-white);
            background: rgba(67, 97, 238, 0.1);
            border-left: 3px solid var(--primary);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .card-header {
            background: rgba(67, 97, 238, 0.1);
            border-bottom: 1px solid var(--border-color);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--bg-card), var(--primary));
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-icon.bg-primary { background: linear-gradient(135deg, var(--primary), #2d3fff); }
        .stat-icon.bg-success { background: linear-gradient(135deg, var(--success), #20a4f3); }
        .stat-icon.bg-warning { background: linear-gradient(135deg, var(--warning), #ff8a00); }
        .stat-icon.bg-info { background: linear-gradient(135deg, var(--info), #0ea5e9); }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--text-white);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-white);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tables */
        .table {
            color: var(--text-white);
        }

        .table th {
            background: rgba(67, 97, 238, 0.1);
          color: var(--text-white); /* Changé en blanc */
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            border-bottom: 1px solid var(--border-color);
        }

        /* Badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 500;
        }

        .badge.bg-success { background: var(--success) !important; }
        .badge.bg-warning { background: var(--warning) !important; }
        .badge.bg-info { background: var(--info) !important; }

        /* Charts */
        .chart-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: #2d3fff;
            transform: translateY(-2px);
        }

        /* Activity table */
        .activity-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .activity-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Styles pour l'en-tête et les statistiques */
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            color: var(--text-white);
        }

        .page-header h1 {
            margin: 0;
            font-weight: 600;
            color: var(--text-white);
        }

        /* Responsive */
        @media (max-width: 1199.98px) {
            .sidebar {
                width: 280px;
            }
            .main-content {
                margin-left: 280px;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
            }
            
            .nav-link {
                padding: 1rem 1.5rem;
            }

            .sidebar-header {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .row {
                margin: 0;
            }
        }
    </style>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <!-- Copier la sidebar du dashboard ici -->
            <div class="sidebar-header d-flex align-items-center gap-3">
                <img src="../../assets/images/WEMANTCHE LOGO p 2.png" alt="Logo" class="sidebar-logo">
                <h5 class="mb-0">WEMANTCHE</h5>
            </div>
            
    
            <ul class="sidebar-nav">
                <li class="nav-item active">
                    <a href="../dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../categories/index.php" class="nav-link">
                        <i class="fas fa-folder"></i>
                        <span>Catégories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../documents/index.php" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../redaction/demandes.php" class="nav-link">
                        <i class="fas fa-pen-fancy"></i>
                        <span>Demandes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../contacts/index.php" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../users/index.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        
        </nav>

        <!-- Contenu principal -->
        <main class="main-content">
            <div class="container-fluid py-4">
                <!-- En-tête -->
                <div class="page-header">
                    <h1 class="h3">Gestion des Documents</h1>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="upload.html" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Ajouter un Document
                    </a>
                </div>

                <!-- Statistiques -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="stat-label mb-0">Total Documents</h6>
                                        <h3 class="stat-value mb-0" id="totalDocs">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                <!-- Recherche -->
                <div class="input-group mb-4">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un document...">
                </div>

                <!-- Liste des documents -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Prix</th>
                                        <th>Note</th>
                                        <th>Langues</th>
                                        <th>Niveau</th>
                                        <th>Mots Clés</th>
                                        <th>Taille du Fichier</th>
                                        <th>Type de Fichier</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="documentsList">
                                    <!-- Les documents seront ajoutés ici dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="d-flex align-items-center">
                                <select class="form-select me-2" id="itemsPerPage" style="width: auto;">
                                    <option value="10">10 par page</option>
                                    <option value="25">25 par page</option>
                                    <option value="50">50 par page</option>
                                    <option value="100">100 par page</option>
                                </select>
                                <span class="text-muted" id="paginationInfo"></span>
                            </div>
                            <nav aria-label="Navigation des pages">
                                <ul class="pagination mb-0" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        class DocumentManager {
            constructor() {
                this.documents = [];
                this.currentPage = 1;
                this.itemsPerPage = 10;
                this.init();
            }

            init() {
                this.setupPagination();
                this.loadDocuments();
                this.setupSearch();
            }

            setupPagination() {
                document.getElementById('itemsPerPage').addEventListener('change', (e) => {
                    this.itemsPerPage = parseInt(e.target.value);
                    this.currentPage = 1;
                    this.loadDocuments();
                });
            }

            async loadDocuments() {
                try {
                    const response = await fetch(`get_documents.php?page=${this.currentPage}&limit=${this.itemsPerPage}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    
                    this.documents = data.documents;
                    this.renderDocuments();
                    this.renderPagination(data.pagination);
                    this.updateStats();
                } catch (error) {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Impossible de charger les documents', 'error');
                }
            }

            renderDocuments() {
                const tbody = document.getElementById('documentsList');
                tbody.innerHTML = '';

                this.documents.forEach(doc => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="${doc.id}">
                            </div>
                        </td>
                        <td>${doc.id}</td>
                        <td>${doc.titre}</td>
                        <td>${doc.categorie}</td>
                        <td>${doc.prix.toLocaleString('fr-FR')} FCFA</td>
                        <td>${doc.notes || '-'}</td>
                        <td>${doc.langues}</td>
                        <td>${doc.niveau}</td>
                        <td>${doc.mots_cles || '-'}</td>
                        <td>${doc.taille_fichier}</td>
                        <td>${doc.type_fichier}</td>
                        <td><span class="badge ${doc.statut === 'publier' ? 'bg-success' : 'bg-warning'}">${doc.statut}</span></td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info" onclick="viewDocument(${doc.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDeleteDocument(${doc.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }

            renderPagination(pagination) {
                const paginationContainer = document.getElementById('pagination');
                const paginationInfo = document.getElementById('paginationInfo');
                
                // Afficher les informations de pagination
                paginationInfo.textContent = `Affichage ${(pagination.currentPage - 1) * pagination.itemsPerPage + 1} à ${Math.min(pagination.currentPage * pagination.itemsPerPage, pagination.totalItems)} sur ${pagination.totalItems} documents`;

                // Générer les boutons de pagination
                let html = '';
                
                // Bouton précédent
                html += `
                    <li class="page-item ${pagination.currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.currentPage - 1}">Précédent</a>
                    </li>
                `;

                // Pages numériques
                for (let i = 1; i <= pagination.totalPages; i++) {
                    if (
                        i === 1 ||
                        i === pagination.totalPages ||
                        (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)
                    ) {
                        html += `
                            <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `;
                    } else if (
                        i === pagination.currentPage - 3 ||
                        i === pagination.currentPage + 3
                    ) {
                        html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Bouton suivant
                html += `
                    <li class="page-item ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.currentPage + 1}">Suivant</a>
                    </li>
                `;

                paginationContainer.innerHTML = html;

                // Ajouter les écouteurs d'événements pour les boutons de pagination
                paginationContainer.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const page = parseInt(e.target.dataset.page);
                        if (page && page !== pagination.currentPage) {
                            this.currentPage = page;
                            this.loadDocuments();
                        }
                    });
                });
            }

            updateStats() {
                const totalDocs = this.documents.length;

                document.getElementById('totalDocs').textContent = totalDocs;
            }

            setupSearch() {
                const searchInput = document.getElementById('searchInput');
                searchInput.addEventListener('input', (event) => {
                    const query = event.target.value.toLowerCase();
                    const filteredDocuments = this.documents.filter(doc => 
                        doc.titre.toLowerCase().includes(query) ||
                        doc.categorie.toLowerCase().includes(query) ||
                        doc.mots_cles.toLowerCase().includes(query)
                    );
                    this.renderDocuments(filteredDocuments);
                });
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            window.documentManager = new DocumentManager();
        });

        function confirmDeleteDocument(id) {
            // Afficher la boîte de dialogue de confirmation
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous ne pourrez pas annuler cette action !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDocument(id);
                }
            });
        }

        async function deleteDocument(id) {
            try {
                const response = await fetch(`delete_document.php?id=${id}`, {
                    method: 'DELETE'
                });
                if (!response.ok) {
                    throw new Error(`Erreur du serveur: ${response.status} - ${response.statusText}`);
                }
                const result = await response.json();
                if (result.success) {
                    Swal.fire(
                        'Supprimé !',
                        'Le document a été supprimé.',
                        'success'
                    );
                    // Recharger les documents
                    window.documentManager.loadDocuments();
                } else {
                    Swal.fire(
                        'Erreur !',
                        'Une erreur est survenue lors de la suppression du document.',
                        'error'
                    );
                }
            } catch (error) {
                console.error('Erreur lors de la suppression du document:', error);
                Swal.fire(
                    'Erreur !',
                    'Une erreur est survenue lors de la suppression du document.',
                    'error'
                );
            }
        }

        function viewDocument(id) {
            // Logique pour voir le document
            console.log('Voir le document avec ID:', id);
            // Rediriger vers une page de visualisation ou afficher un modal
            window.location.href = `view_document.php?id=${id}`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const documentsList = document.getElementById('documentsList');

            // Fonction pour filtrer les documents
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase();
                const rows = documentsList.querySelectorAll('tr');

                rows.forEach(row => {
                    const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Titre
                    const category = row.querySelector('td:nth-child(4)').textContent.toLowerCase(); // Catégorie
                    const keywords = row.querySelector('td:nth-child(9)').textContent.toLowerCase(); // Mots-clés

                    // Vérifier si la recherche correspond au titre, catégorie ou mots-clés
                    if (title.includes(query) || category.includes(query) || keywords.includes(query)) {
                        row.style.display = ''; // Afficher la ligne
                    } else {
                        row.style.display = 'none'; // Masquer la ligne
                    }
                });
            });
        });
    </script>
</body>
</html>