document.addEventListener('DOMContentLoaded', function() {
    // Configuration des graphiques
    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    Chart.defaults.color = '#6c757d';

    // Graphique des ventes mensuelles
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Ventes 2024',
                data: [65, 59, 80, 81, 56, 55, 40, 45, 60, 75, 85, 90],
                fill: true,
                backgroundColor: 'rgba(0, 102, 204, 0.1)',
                borderColor: 'rgb(0, 102, 204)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} ventes`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' ventes';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Graphique de distribution des documents
    const documentsCtx = document.getElementById('documentsChart').getContext('2d');
    const documentsChart = new Chart(documentsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Exposés', 'Mémoires', 'CV', 'Autres'],
            datasets: [{
                data: [40, 30, 20, 10],
                backgroundColor: [
                    'rgb(0, 102, 204)',
                    'rgb(40, 167, 69)',
                    'rgb(255, 193, 7)',
                    'rgb(108, 117, 125)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${percentage}% (${value} docs)`;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // Mise à jour des statistiques en temps réel
    function updateStats() {
        // Simuler des mises à jour aléatoires
        const stats = document.querySelectorAll('.stat-card h4');
        stats.forEach(stat => {
            const currentValue = parseInt(stat.textContent);
            const change = Math.floor(Math.random() * 5) - 2; // -2 à +2
            const newValue = Math.max(0, currentValue + change);
            
            // Animation de la mise à jour
            if (currentValue !== newValue) {
                stat.style.transition = 'color 0.3s ease';
                stat.style.color = change > 0 ? '#28a745' : '#dc3545';
                
                setTimeout(() => {
                    stat.textContent = newValue;
                    stat.style.color = '';
                }, 300);
            }
        });
    }

    // Mettre à jour les stats toutes les 30 secondes
    setInterval(updateStats, 30000);

    // Gestion des activités récentes
    function addNewActivity(activity) {
        const feed = document.querySelector('.activity-feed');
        const newItem = document.createElement('div');
        newItem.className = 'activity-item';
        newItem.innerHTML = `
            <div class="activity-content">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-${activity.type}">
                        <i class="fas fa-${activity.icon} text-white"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-0">${activity.message}</p>
                        <small class="text-muted">À l'instant</small>
                    </div>
                </div>
            </div>
        `;
        
        // Ajouter avec animation
        newItem.style.opacity = '0';
        feed.insertBefore(newItem, feed.firstChild);
        setTimeout(() => newItem.style.opacity = '1', 100);

        // Limiter à 5 activités
        const items = feed.querySelectorAll('.activity-item');
        if (items.length > 5) {
            items[items.length - 1].remove();
        }
    }

    // Simuler de nouvelles activités
    const activities = [
        { type: 'success', icon: 'check', message: 'Nouveau document ajouté' },
        { type: 'info', icon: 'user', message: 'Nouvel utilisateur inscrit' },
        { type: 'warning', icon: 'exclamation', message: 'Paiement en attente' },
        { type: 'danger', icon: 'times', message: 'Document supprimé' }
    ];

    setInterval(() => {
        const randomActivity = activities[Math.floor(Math.random() * activities.length)];
        addNewActivity(randomActivity);
    }, 45000);
}); 