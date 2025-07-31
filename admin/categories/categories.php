<?php
require_once '../../config/connexion.php';

try {
    // Récupérer toutes les catégories actives
    $sql = "SELECT id, nom, description, statut, active, cree_a, mis_a_jour_a 
            FROM categories_documents 
            ORDER BY nom ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculer les statistiques
    $total_categories = count($categories);
    $total_documents = 0; // Remplacez par la logique pour calculer le total des documents
    $categories_actives = count(array_filter($categories, function($categorie) {
        return $categorie['active'];
    }));

    // Rechercher une catégorie
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    if (!empty($search)) {
        $categories = array_filter($categories, function($categorie) use ($search) {
            return stripos($categorie['nom'], $search) !== false;
        });
    }

} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - WEMANTCHE Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
   
</head>
<style>
        /* Stats Cards Styling */
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

        .stats-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: linear-gradient(145deg, var(--bg-card), var(--bg-dark));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card:nth-child(1) {
            background: linear-gradient(145deg, #4361ee20, #4361ee40);
            border-left: 4px solid var(--primary);
        }

        .stats-card:nth-child(2) {
            background: linear-gradient(145deg, #f59e0b20, #f59e0b40);
            border-left: 4px solid var(--warning);
        }

        .stats-card:nth-child(3) {
            background: linear-gradient(145deg, #2dd4bf20, #2dd4bf40);
            border-left: 4px solid var(--success);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stats-icon {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
            position: relative;
            z-index: 1;
        }

        .stats-value {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-white), #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stats-label {
            color: var(--text-light);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        /* Style des icônes */
        .stats-icon.bg-primary { 
            color: var(--primary);
            background: linear-gradient(145deg, #4361ee30, #4361ee10);
        }

        .stats-icon.bg-warning { 
            color: var(--warning);
            background: linear-gradient(145deg, #f59e0b30, #f59e0b10);
        }

        .stats-icon.bg-success { 
            color: var(--success);
            background: linear-gradient(145deg, #2dd4bf30, #2dd4bf10);
        }

        /* Styles Modal */
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: rgba(67, 97, 238, 0.1);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .modal-title {
            color: var(--text-white);
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Formulaires */
        .form-label {
            color: var(--text-white);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--bg-dark);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            color: var(--text-white);
        }

        .form-control::placeholder {
            color: var(--text-light);
        }

        .form-select option {
            background-color: var(--bg-dark);
            color: var(--text-white);
        }

        /* Boutons */
        .btn-secondary {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
        }

        .btn-secondary:hover {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-white);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(145deg, var(--primary), #2d3fff);
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #2d3fff, var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        /* Layout et Sidebar */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
        }

        .sidebar-header h5 {
            color: var(--text-white);
            font-weight: 600;
            font-size: 1.2rem;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 1rem 0;
            list-style: none;
            margin: 0;
        }

        .nav-item {
            margin: 0.5rem 0;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-light);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: var(--text-white);
            background: rgba(67, 97, 238, 0.1);
            border-left-color: var(--primary);
        }

        .sidebar-nav .nav-link:hover i,
        .sidebar-nav .nav-link.active i {
            color: var(--primary);
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            background: var(--bg-dark);
            min-height: 100vh;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Style de base */
        body {
            background: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Pour aligner le logo et le texte */
        .sidebar-header h5 {
            color: var(--text-white);
            margin: 0;
            font-size: 1.1rem;
        }

        /* Style pour les tableaux */
        .table {
            color: var(--text-white);
            margin-bottom: 0;
        }

        .table th {
            background: rgba(67, 97, 238, 0.1);
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Style pour les badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Style pour les boutons d'action */
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-sm i {
            font-size: 0.95rem;
        }

        .btn-sm:hover {
            transform: translateY(-2px);
        }

        /* Personnalisation de la barre de recherche */
        .input-group-text {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-light);
        }

        .form-control {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-white);
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: var(--bg-card);
            border-color: var(--primary);
            color: var(--text-white);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
    </style>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des Catégories</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Nouvelle Catégorie
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Rechercher une catégorie..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        Rechercher
                    </button>
                    <?php if(!empty($search)): ?>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="stats-wrapper">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div>
                        <div class="stats-value"><?php echo $total_categories; ?></div>
                        <div class="stats-label">Total Catégories</div>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-file"></i>
                    </div>
                    <div>
                        <div class="stats-value"><?php echo $total_documents; ?></div>
                        <div class="stats-label">Documents</div>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <div class="stats-value"><?php echo $categories_actives; ?></div>
                        <div class="stats-label">Catégories Actives</div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>État</th>
                    <th>Créé le</th>
                    <th>Dernière modification</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $categorie): ?>
                <tr>
                    <td><?php echo htmlspecialchars($categorie['nom']); ?></td>
                    <td><?php echo htmlspecialchars($categorie['description']); ?></td>
                    <td><?php echo htmlspecialchars($categorie['statut']); ?></td>
                    <td>
                        <span class="badge <?php echo $categorie['active'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $categorie['active'] ? 'Actif' : 'Inactif'; ?>
                        </span>
                    </td>
                    <td><?php echo $categorie['cree_a']; ?></td>
                    <td><?php echo $categorie['mis_a_jour_a']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Ajout Catégorie -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvelle Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="public">Public</option>
                                <option value="prive">Privé</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active">
                            <label class="form-check-label" for="active">Actif</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="categoryForm" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
