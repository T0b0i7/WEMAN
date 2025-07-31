document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres
    const filterForm = document.querySelector('.card .row.g-3');
    const filterInputs = filterForm.querySelectorAll('select, input');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            filterLogs();
        });
    });

    function filterLogs() {
        // Simuler le chargement
        const tbody = document.querySelector('tbody');
        tbody.style.opacity = '0.5';
        
        setTimeout(() => {
            tbody.style.opacity = '1';
            showNotification('Filtres appliqués avec succès', 'success');
        }, 500);
    }

    // Gestion des détails
    const detailButtons = document.querySelectorAll('[title="Détails"]');
    const logDetailsModal = new bootstrap.Modal(document.getElementById('logDetailsModal'));

    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Charger les détails du log
            const row = this.closest('tr');
            const timestamp = row.cells[0].textContent;
            const type = row.cells[1].textContent.trim();
            const module = row.cells[2].textContent;
            const message = row.cells[3].textContent;
            
            // Mettre à jour le modal
            const modal = document.getElementById('logDetailsModal');
            modal.querySelector('dd:nth-of-type(1)').textContent = timestamp;
            modal.querySelector('dd:nth-of-type(3)').textContent = module;
            modal.querySelector('pre:nth-of-type(1)').textContent = message;
            
            logDetailsModal.show();
        });
    });

    // Nettoyage des logs
    document.getElementById('clearLogs').addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir nettoyer les logs ? Cette action est irréversible.')) {
            showNotification('Logs nettoyés avec succès', 'success');
        }
    });

    // Téléchargement des logs
    document.getElementById('downloadLogs').addEventListener('click', function() {
        // Simuler le téléchargement
        const link = document.createElement('a');
        link.href = '#';
        link.download = `system_logs_${new Date().toISOString().split('T')[0]}.csv`;
        link.click();
        
        showNotification('Téléchargement des logs commencé', 'info');
    });

    // Système de notifications
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.innerHTML = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Gestion de la pagination
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (!this.parentElement.classList.contains('disabled')) {
                const page = this.textContent;
                loadPage(page);
            }
        });
    });

    function loadPage(page) {
        // Simuler le chargement d'une nouvelle page
        const tbody = document.querySelector('tbody');
        tbody.style.opacity = '0.5';
        
        setTimeout(() => {
            tbody.style.opacity = '1';
            showNotification(`Page ${page} chargée`, 'info');
        }, 500);
    }

    // Export des logs
    function exportLogs(format) {
        const filters = {
            type: document.querySelector('select:nth-of-type(1)').value,
            module: document.querySelector('select:nth-of-type(2)').value,
            dateStart: document.querySelector('input[type="date"]:nth-of-type(1)').value,
            dateEnd: document.querySelector('input[type="date"]:nth-of-type(2)').value
        };

        // Simuler l'export
        console.log(`Exporting logs in ${format} format with filters:`, filters);
        showNotification(`Export en ${format.toUpperCase()} démarré`, 'info');
    }
}); 