<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- ... your existing head content ... -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>
<body>
<!-- ... your existing body content ... -->

<div style="width: 200px; height: 200px; margin: 0 auto;">
    <canvas id="pieChart"></canvas>
</div>
<script>
    // Register the plugin
    Chart.register(ChartDataLabels);

    const data = {
        labels: ['Gagnés', 'Perdus', 'Nuls'],
        datasets: [{
            data: [
                <?php echo $stats['matchsGagnes']; ?>,
                <?php echo $stats['matchsPerdus']; ?>,
                <?php echo $stats['matchsNuls']; ?>
            ],
            backgroundColor: ['#4CAF50', '#F44336', '#FFC107'],
            hoverOffset: 4
        }]
    };

    const config = {
        type: 'pie',
        data: data,
        options: {
            maintainAspectRatio: false,
            aspectRatio: 1,
            plugins: {
                // Configure data labels
                datalabels: {
                    formatter: (value, context) => {
                        const dataArray = context.chart.data.datasets[0].data;
                        const total = dataArray.reduce((acc, val) => acc + val, 0);
                        const percentage = ((value / total) * 100).toFixed(1) + '%';
                        return percentage;
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold'
                    }
                },
                // Customize tooltips to show counts and percentages
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const value = context.parsed;
                            const dataArray = context.chart.data.datasets[0].data;
                            const total = dataArray.reduce((acc, val) => acc + val, 0);
                            const percentage = ((value / total) * 100).toFixed(1) + '%';
                            label += value + ' (' + percentage + ')';
                            return label;
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    };

    const pieChart = new Chart(
        document.getElementById('pieChart'),
        config
    );
</script>

<!-- ... rest of your HTML ... -->
</body>
</html>
