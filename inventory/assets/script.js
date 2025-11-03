
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?php echo $monthlyLabels; ?>,
    datasets: [{
      label: 'Sales (₱)',
      data: <?php echo json_encode(array_values($monthlySales)); ?>,
      borderColor: '#1D4ED8',
      backgroundColor: 'rgba(29, 78, 216, 0.1)',
      fill: true,
      tension: 0.3,
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
