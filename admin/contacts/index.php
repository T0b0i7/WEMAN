<?php
require_once('../../config/connexion.php');

// Paramètres de pagination
$messages_par_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $messages_par_page;

// Recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = '';
if (!empty($search)) {
    $where_clause = " WHERE 
        nom LIKE :search 
        OR prenom LIKE :search 
        OR email LIKE :search 
        OR sujet LIKE :search 
        OR message LIKE :search";
}

// Compte total des messages pour la pagination
$count_query = "SELECT COUNT(*) as total FROM contacts" . $where_clause;
$stmt = $pdo->prepare($count_query);
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}
$stmt->execute();
$total_messages = $stmt->fetch()['total'];
$total_pages = ceil($total_messages / $messages_par_page);

// Compte des messages actifs et en attente
$actifs_query = "SELECT COUNT(*) as total_actifs FROM contacts WHERE statut = 'active'";
$stmt = $pdo->prepare($actifs_query);
$stmt->execute();
$total_messages_actifs = $stmt->fetch()['total_actifs'];

$attente_query = "SELECT COUNT(*) as total_attente FROM contacts WHERE statut = 'en attente'";
$stmt = $pdo->prepare($attente_query);
$stmt->execute();
$total_messages_attente = $stmt->fetch()['total_attente'];

// Récupérer les messages avec pagination et recherche
$query = "SELECT * FROM contacts" . $where_clause . " ORDER BY date_creation DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($query);
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $messages_par_page, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages de Contact - WEMANTCHE Admin</title>
    <link rel="icon" type="image/jpg" href="../../assets/images/WEMANTCHE LOGO p 2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #1a1f2d;
            --bg-card: #242a38;
            --bg-modal: #2a303c;
            --primary: #4361ee;
            --primary-hover: #3451db;
            --secondary: #3f37c9;
            --success: #2dd4bf;
            --danger: #f43f5e;
            --warning: #f59e0b;
            --info: #3b82f6;
            --text-light: #94a3b8;
            --text-white: #ffffff;
            --border-color: rgba(255, 255, 255, 0.1);
            --transition: all 0.3s ease;
        }

        /* Base Styles */
        body {
            background: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar - Style mis à jour */
.sidebar {
    width: 300px;
    background: var(--bg-card);
    border-right: 1px solid var(--border-color);
    position: fixed;
    height: 100vh;
    left: 0;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease-in-out;
}

.sidebar-header {
    padding: 1.8rem;
    border-bottom: 1px solid var(--border-color);
    background: rgba(67, 97, 238, 0.1);
}

.sidebar-logo {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.sidebar-logo:hover {
    transform: scale(1.05);
}

.sidebar-header h5 {
    color: var(--text-white);
    font-weight: 600;
    font-size: 1.4rem;
    margin: 0;
    letter-spacing: 0.5px;
}

.sidebar-nav {
    padding: 1.5rem 0;
}

.nav-item {
    margin: 0.8rem 0;
}

.nav-link {
    padding: 1rem 1.8rem;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 15px;
  color: var(--text-white); /* Changé en blanc */
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.nav-link i {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    transition: all 0.3s ease;
}

.nav-link:hover,
.nav-link.active {
    color: var(--text-white);
    background: rgba(67, 97, 238, 0.1);
    border-left-color: var(--primary);
    transform: translateX(5px);
}

.nav-link:hover i,
.nav-link.active i {
    color: var(--primary);
    transform: scale(1.1);
}

/* Main Content */
        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 2rem;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Cards et Stats */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        /* Styles pour les cartes de statistiques */
        .stat-card {
            border: none;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #4361ee, #3f37c9);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #2dd4bf, #0ea5e9);
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #f59e0b, #f97316);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
        }

        .card-body {
            padding: 1.5rem;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Animation au survol */
        .stat-card:hover .stat-icon {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.3);
        }

        /* Table Styles */
        .table {
            color: var(--text-white);
            margin-bottom: 0;
        }

        .table th {
            color: var(--text-white) !important;
            background: rgba(67, 97, 238, 0.1);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .table td {
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tr:hover {
            background: rgba(67, 97, 238, 0.05);
        }

        /* Badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--secondary));
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #e11d48);
        }

        /* Search Bar */
        .form-control {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: var(--transition);
        }

        .form-control:focus {
            background: var(--bg-dark);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            color: var(--text-white);
        }

        /* Modal */
        .modal-content {
            background: var(--bg-modal);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Pagination */
        .pagination {
            margin: 0;
            gap: 0.25rem;
        }

        .page-link {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
          color: var(--text-white); /* Changé en blanc */
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: var(--transition);
        }

        .page-link:hover,
        .page-item.active .page-link {
            background: var(--primary);
            color: var(--text-white);
            border-color: var(--primary);
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

/* Ajouter dans la section style existante, après le style .form-control */
.form-control::placeholder {
    color: var(--text-white);
    opacity: 0.7;
}

.form-control::-webkit-input-placeholder {
    color: var(--text-white);
    opacity: 0.7;
}

.form-control::-moz-placeholder {
    color: var(--text-white);
    opacity: 0.7;
}

.form-control:-ms-input-placeholder {
    color: var(--text-white);
    opacity: 0.7;
}
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <nav class="sidebar">
            <!-- Copier la sidebar du dashboard ici -->
            <div class="sidebar-header">
                <div class="d-flex align-items-center">
                    <img src="../../assets/images/WEMANTCHE LOGO p 2.png" alt="Logo" class="sidebar-logo me-3">
                    <h5 class="mb-0">WEMANTCHE</h5>
                </div>
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

        <main class="main-content">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Gestion des Messages</h1>
                </div>

            <div class="container-fluid py-4">
               

                <!-- Statistiques -->
                <div class="row g-4 mb-4">
                    <!-- Total Messages -->
                    <div class="col-md-4">
                        <div class="card stat-card bg-gradient-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1 text-white-50">Total Messages</h6>
                                        <h3 class="mb-0 text-white" id="totalCount">
                                            <?php echo number_format($total_messages); ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Actifs -->
                    <div class="col-md-4">
                        <div class="card stat-card bg-gradient-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon">
                                        <i class="fas fa-envelope-open"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1 text-white-50">Messages Actifs</h6>
                                        <h3 class="mb-0 text-white">
                                            <?php echo number_format($total_messages_actifs ?? 0); ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages en Attente -->
                    <div class="col-md-4">
                        <div class="card stat-card bg-gradient-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-1 text-white-50">En Attente</h6>
                                        <h3 class="mb-0 text-white">
                                            <?php echo number_format($total_messages_attente ?? 0); ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- Barre de recherche -->
                 <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Rechercher..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if(!empty($search)): ?>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Sujet</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($messages as $message): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($message['date_creation'])); ?></td>
                                        <td><?php echo htmlspecialchars($message['prenom'] . ' ' . $message['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                                        <td><?php echo htmlspecialchars($message['sujet']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo isset($message['statut']) && $message['statut'] === 'nouvelle' ? 'success' : 'danger'; ?>">
                                                <?php echo isset($message['statut']) ? ucfirst($message['statut']) : 'Inconnu'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button 
                                                class="btn btn-sm btn-info me-2"
                                                onclick="viewMessage(<?php echo htmlspecialchars(json_encode($message)); ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button 
                                                class="btn btn-sm btn-danger"
                                                onclick="deleteMessage(<?php echo $message['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal pour afficher le message complet -->
                                    <div class="modal fade" id="messageModal<?php echo $message['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Message de <?php echo htmlspecialchars($message['prenom'] . ' ' . $message['nom']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Sujet:</strong> <?php echo htmlspecialchars($message['sujet']); ?></p>
                                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                                                    <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($message['date_creation'])); ?></p>
                                                    <hr>
                                                    <p><strong>Message:</strong></p>
                                                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <a href="mailto:<?php echo $message['email']; ?>" class="btn btn-primary">
                                                        <i class="fas fa-reply"></i> Répondre
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Affichage de <?php echo $offset + 1; ?> à 
                                <?php echo min($offset + $messages_par_page, $total_messages); ?> 
                                sur <?php echo $total_messages; ?> messages
                            </div>
                            <nav>
                                <ul class="pagination mb-0">
                                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" 
                                               href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function viewMessage(message) {
            Swal.fire({
                title: `Message de ${message.prenom} ${message.nom}`,
                html: `
                    <div class="text-start">
                        <p><strong>Sujet:</strong> ${message.sujet}</p>
                        <p><strong>Email:</strong> ${message.email}</p>
                        <p><strong>Date:</strong> ${new Date(message.date_creation).toLocaleString('fr-FR')}</p>
                        <p><strong>Statut:</strong> 
                            <span class="badge bg-${message.statut === 'nouvelle' ? 'success' : 'danger'}">
                                ${message.statut}
                            </span>
                        </p>
                        <hr>
                        <p><strong>Message:</strong></p>
                        <p>${message.message.replace(/\n/g, '<br>')}</p>
                    </div>
                `,
                width: '600px',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Répondre',
                cancelButtonText: 'Fermer',
                customClass: {
                    content: 'text-start'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `mailto:${message.email}`;
                }
            });
        }

        async function deleteMessage(id) {
            try {
                const result = await Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Voulez-vous vraiment supprimer ce message ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                });

                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);

                    const response = await fetch('delete.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Supprimé !',
                            text: 'Le message a été supprimé avec succès.',
                            timer: 1500
                        });
                        window.location.reload();
                    } else {
                        throw new Error(data.message);
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Une erreur est survenue'
                });
            }
        }

        // Fonction pour vérifier et mettre à jour les statuts
        async function checkAndUpdateStatus() {
            try {
                const response = await fetch('update_status.php');
                const data = await response.json();
                
                if (data.success && data.updated > 0) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Erreur mise à jour statuts:', error);
            }
        }

        // Vérifier les statuts toutes les heures
        setInterval(checkAndUpdateStatus, 3600000);
    </script>
</body>
</html>
