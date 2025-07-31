document.addEventListener('DOMContentLoaded', () => {
    // Vérifie d'abord si une date de dernière mise à jour existe dans le localStorage
    let lastUpdateDate = localStorage.getItem('lastSystemUpdate');
    
    if (!lastUpdateDate) {
        // Si c'est la première fois, on enregistre la date actuelle
        lastUpdateDate = new Date().toISOString();
        localStorage.setItem('lastSystemUpdate', lastUpdateDate);
    }

    const today = new Date();
    const lastUpdate = new Date(lastUpdateDate);
    const monthsDiff = (today.getFullYear() - lastUpdate.getFullYear()) * 12 + 
                      (today.getMonth() - lastUpdate.getMonth());

    if (monthsDiff >= 4) {
        Swal.fire({
            title: '<strong>Mise à jour système requise</strong>',
            icon: 'warning',
            html: `
                <div class="update-alert">
                    <p class="text-warning">Votre système n'a pas été mis à jour depuis ${monthsDiff} mois.</p>
                    <div class="alert alert-info">
                        <h6 class="mb-3">Une mise à jour est recommandée pour :</h6>
                        <ul class="text-left">
                            <li>🔒 Renforcer la sécurité du système</li>
                            <li>🚀 Améliorer les performances</li>
                            <li>✨ Accéder aux nouvelles fonctionnalités</li>
                            <li>🐛 Corriger les bugs potentiels</li>
                        </ul>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted small">
                            <i class="fas fa-clock"></i> Dernière mise à jour : 
                            ${lastUpdate.toLocaleDateString('fr-FR', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            })}
                        </p>
                    </div>
                </div>
            `,
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: '<i class="fas fa-sync-alt"></i> Mettre à jour maintenant',
            confirmButtonColor: '#4361ee',
            cancelButtonText: 'Plus tard',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                container: 'system-update-notification',
                popup: 'system-update-popup',
                header: 'update-header',
                title: 'update-title',
                closeButton: 'update-close',
                content: 'update-content',
                actions: 'update-actions',
                confirmButton: 'update-confirm btn-lg',
                cancelButton: 'update-cancel btn-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirection vers la page de mise à jour
                window.location.href = '../admin/system-update.php';
            } else {
                // Si l'utilisateur clique sur "Plus tard", on rappelle dans 24h
                const nextCheck = new Date();
                nextCheck.setHours(nextCheck.getHours() + 24);
                localStorage.setItem('nextUpdateCheck', nextCheck.toISOString());
            }
        });
    }
});

// Ajout de styles personnalisés pour la notification
const styles = `
    .system-update-notification .update-alert {
        padding: 1.5rem;
        text-align: left;
    }
    .system-update-notification ul {
        list-style-type: none;
        padding-left: 0;
    }
    .system-update-notification ul li {
        margin-bottom: 0.75rem;
        font-size: 1.1em;
    }
    .system-update-notification .alert {
        background-color: rgba(67, 97, 238, 0.1);
        border: 1px solid rgba(67, 97, 238, 0.2);
        border-radius: 8px;
        padding: 1.5rem;
    }
    .update-confirm, .update-cancel {
        padding: 0.75rem 1.5rem;
        font-size: 1.1em;
    }
    .update-title {
        color: #4361ee;
        font-size: 1.5em;
    }
`;

const styleSheet = document.createElement("style");
styleSheet.innerText = styles;
document.head.appendChild(styleSheet);