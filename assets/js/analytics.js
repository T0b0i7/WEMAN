document.addEventListener('DOMContentLoaded', function() {
    // Configuration des graphiques
    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    Chart.defaults.color = '#6c757d';

    // Graphique des ventes
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil'],
            datasets: [{
                label: 'Ventes 2024',
                data: [65, 59, 80, 81, 56, 55, 40],
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
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
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

    // Graphique de distribution
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    const distributionChart = new Chart(distributionCtx, {
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
                }
            }
        }
    });

    // Export PDF
    document.getElementById('exportPDF').addEventListener('click', function() {
        // Logique d'export PDF
        console.log('Exporting to PDF...');
    });

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        // Logique d'export Excel
        console.log('Exporting to Excel...');
    });

    // Date Range Buttons
    const dateButtons = document.querySelectorAll('.btn-group .btn-outline-primary');
    dateButtons.forEach(button => {
        button.addEventListener('click', function() {
            dateButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            updateCharts(this.textContent);
        });
    });

    // Fonction de mise à jour des graphiques
    function updateCharts(range) {
        // Logique de mise à jour selon la plage de dates
        console.log(`Updating charts for range: ${range}`);
    }
}); 