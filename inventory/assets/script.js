
// 📁 assets/js/script.js
document.addEventListener('DOMContentLoaded', () => {
    console.log("Dashboard loaded");

    // Example: Chart.js sample
    const chartCanvas = document.getElementById('productChart');
    if (chartCanvas) {
        new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: ['Product A', 'Product B', 'Product C'],
                datasets: [{
                    label: 'Stock Levels',
                    data: [12, 19, 3],
                    backgroundColor: ['#3498db', '#2ecc71', '#e74c3c']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Inventory Overview'
                    }
                }
            }
        });
    }
});