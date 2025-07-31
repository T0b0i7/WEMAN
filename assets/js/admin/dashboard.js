import { authManager } from './auth.js';
import { adminUI } from './ui.js';

class DashboardManager {
    constructor() {
        this.charts = {};
        this.init();
    }

    init() {
        this.initSalesChart();
        this.initDocumentsChart();
        this.loadRecentActivities();
        this.setupEventListeners();
        this.startRealTimeUpdates();
    }

    initSalesChart() {
        const options = {
            series: [{
                name: 'Ventes',
                data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            colors: ['#0066cc'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3
                }
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep']
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " FCFA"
                    }
                }
            }
        };

        this.charts.sales = new ApexCharts(document.querySelector("#salesChart"), options);
        this.charts.sales.render();
    }

    initDocumentsChart() {
        const chartOptions = {
            series: [{
                name: 'Total',
                data: []
            }, {
                name: 'Disponibles',
                data: []
            }, {
                name: 'En attente',
                data: []
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            colors: ['#7367F0', '#28C76F', '#FF9F43'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: 0
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return val + " documents";
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            }
        };

        const chart = new ApexCharts(document.querySelector("#documentsChart"), chartOptions);
        chart.render();

        // Fonction pour mettre à jour les données
        async function updateChartData(periode) {
            try {
                const response = await fetch(`api/stats.php?action=documents_stats&periode=${periode}`);
                const data = await response.json();

                const dates = data.map(item => new Date(item.date).toLocaleDateString('fr-FR'));
                const totals = data.map(item => parseInt(item.total));
                const disponibles = data.map(item => parseInt(item.disponibles));
                const enAttente = data.map(item => parseInt(item.en_attente));

                chart.updateOptions({
                    xaxis: {
                        categories: dates
                    }
                });

                chart.updateSeries([
                    { name: 'Total', data: totals },
                    { name: 'Disponibles', data: disponibles },
                    { name: 'En attente', data: enAttente }
                ]);
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
            }
        }

        // Initialiser avec 7 jours
        updateChartData(7);

        // Gestionnaire d'événements pour le changement de période
        document.querySelector('.chart-filters select').addEventListener('change', (e) => {
            const periode = e.target.value === '7 derniers jours' ? 7 : 
                           e.target.value === '30 derniers jours' ? 30 : 365;
            updateChartData(periode);
        });
    }

    async loadRecentActivities() {
        try {
            // Simuler chargement API
            const activities = [
                {
                    type: 'document',
                    message: 'Nouveau document ajouté',
                    time: '5 min'
                },
                // Autres activités...
            ];

            this.updateActivityFeed(activities);
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    updateActivityFeed(activities) {
        const feed = document.querySelector('.activity-feed');
        if (!feed) return;

        feed.innerHTML = activities.map(activity => `
            <div class="activity-item">
                <div class="activity-content">
                    <div class="activity-icon bg-${this.getActivityColor(activity.type)}">
                        <i class="fas ${this.getActivityIcon(activity.type)}"></i>
                    </div>
                    <div class="activity-info">
                        <p class="mb-0">${activity.message}</p>
                        <small class="text-muted">${activity.time}</small>
                    </div>
                </div>
            </div>
        `).join('');
    }

    setupEventListeners() {
        // Toggle Sidebar
        document.querySelector('.sidebar-toggle')?.addEventListener('click', () => {
            document.querySelector('.admin-wrapper').classList.toggle('sidebar-collapsed');
        });

        // Notifications
        document.querySelector('.notifications-dropdown')?.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    startRealTimeUpdates() {
        // Simuler des mises à jour en temps réel
        setInterval(() => {
            this.updateRandomStats();
        }, 5000);
    }

    updateRandomStats() {
        // Mettre à jour les statistiques aléatoirement
        const stats = document.querySelectorAll('.stat-card h3');
        stats.forEach(stat => {
            const currentValue = parseInt(stat.textContent);
            const newValue = currentValue + Math.floor(Math.random() * 10) - 5;
            stat.textContent = Math.max(0, newValue);
        });
    }

    // Utilitaires
    getActivityColor(type) {
        const colors = {
            document: 'primary',
            user: 'success',
            payment: 'warning',
            error: 'danger'
        };
        return colors[type] || 'secondary';
    }

    getActivityIcon(type) {
        const icons = {
            document: 'fa-file-alt',
            user: 'fa-user',
            payment: 'fa-money-bill',
            error: 'fa-exclamation-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    async loadDashboardData() {
        try {
            // ... autres chargements de données ...

            // Charger les dernières demandes
            await this.loadLatestDemandes();
            
        } catch (error) {
            console.error('Erreur lors du chargement des données:', error);
        }
    }

    async loadLatestDemandes() {
        try {
            // Simulation de données pour les demandes
            const demandes = [
                {
                    id: 1,
                    client: "Jean Dupont",
                    title: "Mémoire en Gestion de Projet",
                    date: "2024-02-20",
                    status: "new"
                },
                {
                    id: 2,
                    client: "Marie Martin",
                    title: "Thèse en Sciences Sociales",
                    date: "2024-02-19",
                    status: "in-progress"
                },
                // Ajouter d'autres demandes...
            ];

            // Mettre à jour le compteur total
            document.getElementById('totalDemandes').textContent = demandes.length;

            // Remplir le tableau des dernières demandes
            const container = document.getElementById('latestDemandes');
            if (container) {
                container.innerHTML = demandes.map(demande => `
                    <tr>
                        <td>#${demande.id}</td>
                        <td>${demande.client}</td>
                        <td>${demande.title}</td>
                        <td>${this.formatDate(demande.date)}</td>
                        <td>
                            <span class="badge bg-${this.getStatusColor(demande.status)}">
                                ${this.getStatusLabel(demande.status)}
                            </span>
                        </td>
                        <td>
                            <a href="redaction/demandes.php?id=${demande.id}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.error('Erreur lors du chargement des demandes:', error);
        }
    }

    getStatusColor(status) {
        const colors = {
            'new': 'info',
            'in-progress': 'warning',
            'completed': 'success',
            'rejected': 'danger'
        };
        return colors[status] || 'secondary';
    }

    getStatusLabel(status) {
        const labels = {
            'new': 'Nouvelle',
            'in-progress': 'En cours',
            'completed': 'Terminée',
            'rejected': 'Refusée'
        };
        return labels[status] || status;
    }
}

// Fonction pour formater les dates
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

// Charger les statistiques des documents
const loadDocumentsStats = async (periode = 7) => {
    try {
        const response = await fetch(`api/stats.php?action=documents_stats&periode=${periode}`);
        const data = await response.json();
        
        const options = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false }
            },
            series: [{
                name: 'Documents',
                data: data.map(item => item.total)
            }],
            xaxis: {
                categories: data.map(item => formatDate(item.date))
            },
            // ... autres options de style
        };

        const chart = new ApexCharts(document.querySelector("#documentsChart"), options);
        chart.render();
    } catch (error) {
        console.error('Erreur:', error);
    }
};

// Charger les types de documents
const loadDocumentTypes = async () => {
    try {
        const response = await fetch('api/stats.php?action=document_types');
        const data = await response.json();
        
        const options = {
            chart: {
                type: 'pie',
                height: 300
            },
            series: data.map(item => item.total),
            labels: data.map(item => item.type_fichier),
            // ... autres options de style
        };

        const chart = new ApexCharts(document.querySelector("#documentTypesChart"), options);
        chart.render();
    } catch (error) {
        console.error('Erreur:', error);
    }
};

// Ajouter ces nouvelles fonctions pour les graphiques
function initPerformanceChart() {
    const options = {
        series: [{
            name: 'Documents',
            data: [31, 40, 28, 51, 42, 109, 100]
        }, {
            name: 'Revenus',
            data: [11, 32, 45, 32, 34, 52, 41]
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']
        },
        tooltip: {
            theme: 'dark'
        }
    };

    new ApexCharts(document.querySelector("#performanceChart"), options).render();
}

function initDocumentsPieChart() {
    const options = {
        series: [44, 55, 13, 43],
        chart: {
            type: 'donut',
            height: 350
        },
        labels: ['Mémoires', 'Rapports', 'CV', 'Autres'],
        colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                }
            }
        }]
    };

    new ApexCharts(document.querySelector("#documentsPieChart"), options).render();
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.dashboardManager = new DashboardManager();
    loadDocumentsStats();
    loadDocumentTypes();
    initPerformanceChart();
    initDocumentsPieChart();
    
    // Gestionnaire pour le changement de période
    document.querySelector('.chart-filters select').addEventListener('change', (e) => {
        const periode = e.target.value === '7 derniers jours' ? 7 : 
                       e.target.value === '30 derniers jours' ? 30 : 365;
        loadDocumentsStats(periode);
    });
});

function initDocumentsChart() {
    const chartOptions = {
        series: [{
            name: 'Total',
            data: []
        }, {
            name: 'Disponibles',
            data: []
        }, {
            name: 'En attente',
            data: []
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        colors: ['#7367F0', '#28C76F', '#FF9F43'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: [],
            labels: {
                rotate: 0
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val) {
                    return val + " documents";
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        }
    };

    const chart = new ApexCharts(document.querySelector("#documentsChart"), chartOptions);
    chart.render();

    // Fonction pour mettre à jour les données
    async function updateChartData(periode) {
        try {
            const response = await fetch(`api/stats.php?action=documents_stats&periode=${periode}`);
            const data = await response.json();

            const dates = data.map(item => new Date(item.date).toLocaleDateString('fr-FR'));
            const totals = data.map(item => parseInt(item.total));
            const disponibles = data.map(item => parseInt(item.disponibles));
            const enAttente = data.map(item => parseInt(item.en_attente));

            chart.updateOptions({
                xaxis: {
                    categories: dates
                }
            });

            chart.updateSeries([
                { name: 'Total', data: totals },
                { name: 'Disponibles', data: disponibles },
                { name: 'En attente', data: enAttente }
            ]);
        } catch (error) {
            console.error('Erreur lors du chargement des données:', error);
        }
    }

    // Initialiser avec 7 jours
    updateChartData(7);

    // Gestionnaire d'événements pour le changement de période
    document.querySelector('.chart-filters select').addEventListener('change', (e) => {
        const periode = e.target.value === '7 derniers jours' ? 7 : 
                       e.target.value === '30 derniers jours' ? 30 : 365;
        updateChartData(periode);
    });
}

function initDocumentTypesChart() {
    const options = {
        series: [],
        chart: {
            type: 'donut',
            height: 350
        },
        labels: [],
        colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#00CFE8'],
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + " documents";
                }
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        name: {
                            show: true
                        },
                        value: {
                            show: true,
                            formatter: function(val) {
                                return val + " docs";
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + " docs";
                            }
                        }
                    }
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#documentTypesChart"), options);
    chart.render();

    // Charger les données
    async function loadDocumentTypes() {
        try {
            const response = await fetch('api/stats.php?action=document_types');
            const data = await response.json();

            chart.updateOptions({
                labels: data.map(item => item.categorie)
            });
            chart.updateSeries(data.map(item => parseInt(item.total)));
        } catch (error) {
            console.error('Erreur lors du chargement des types de documents:', error);
        }
    }

    loadDocumentTypes();
}