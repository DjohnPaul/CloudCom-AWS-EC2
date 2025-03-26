// Call when document is loaded
document.addEventListener('DOMContentLoaded', initDashboard);

// Initialize dashboard
function initDashboard() {
    renderSalesChart('weekly');
    populateTopItems();
    
    const selectElement = document.querySelector('.chart-actions select');
    if (selectElement) {
        selectElement.addEventListener('change', (e) => {
            renderSalesChart(e.target.value.toLowerCase());
        });
    } else {
        console.warn('Dropdown not found: .chart-actions select');
    }
}

// Function to format currency
function formatCurrency(amount) {
    return '₱ ' + amount.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function renderSalesChart(timeframe) {
    // Clear existing chart
    const chartCanvas = document.querySelector('.chart-canvas');
    if (!chartCanvas) {
        console.error('Chart container not found');
        return;
    }
    chartCanvas.innerHTML = '';

    // Fetch data from API
    fetch(`SalesData.php?timeframe=${timeframe}`)
        .then(response => response.json())
        .then(data => {
            if (!data.labels || !data.sales || data.labels.length === 0) {
                console.error('Invalid or empty data:', data);
                return;
            }

            // Convert API data into structured format
            const salesData = data.labels.map((label, index) => ({
                week: label,
                month: label,
                day: label,
                sales: data.sales[index]
            }));

            // Find max value for scaling, avoiding division by zero
            const maxSales = Math.max(1, ...salesData.map(item => item.sales));

            // Create bars
            salesData.forEach((item) => {
                const barHeight = (item.sales / maxSales) * 220; // Scale based on max sales

                const bar = document.createElement('div');
                bar.className = 'chart-bar';
                bar.style.height = barHeight + 'px';

                const label = document.createElement('div');
                label.className = 'chart-bar-label';
                label.textContent = formatCurrency(item.sales);

                bar.appendChild(label);
                chartCanvas.appendChild(bar);
            });

            // Update x-axis labels
            let xAxis = document.querySelector('.chart-x-axis');
            if (!xAxis) {
                xAxis = document.createElement('div');
                xAxis.className = 'chart-x-axis';
                if (chartCanvas.parentNode) {
                    chartCanvas.parentNode.appendChild(xAxis);
                } else {
                    console.warn('chartCanvas has no parent node');
                }
            }
            xAxis.innerHTML = '';

            salesData.forEach((item) => {
                const label = document.createElement('div');
                label.className = 'chart-x-label';
                label.textContent =
                    timeframe === 'daily'
                        ? item.day
                        : timeframe === 'monthly'
                        ? item.month
                        : item.week;
                xAxis.appendChild(label);
            });
        })
        .catch(error => console.error('Error fetching sales data:', error));
}

// Function to populate top items table
function populateTopItems() {
    fetch("TopItems.php")
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Data:", data); // Log the response to check

            const tableBody = document.querySelector('.top-items-table tbody');
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='3'>No data available</td></tr>";
                return;
            }

            data.forEach(item => {
                const row = document.createElement('tr');

                // Create name cell
                const nameCell = document.createElement('td');
                const nameDiv = document.createElement('div'); 
                nameDiv.className = 'popular-item';

                // Add food image
                const img = document.createElement('img');
                img.src = `images/${item.category}/${item.imageName}`;
                img.alt = item.foodName;
                img.style.width = '50px';
                img.style.height = '50px';
                img.style.borderRadius = '5px';

                const nameText = document.createElement('div');
                nameText.textContent = item.foodName;

                nameDiv.appendChild(img);
                nameDiv.appendChild(nameText);
                nameCell.appendChild(nameDiv);

                // Create sales cell
                const salesCell = document.createElement('td');
                salesCell.textContent = `₱${parseFloat(item.totalSales).toFixed(2)}`;

                // Create orders count cell
                const ordersCell = document.createElement('td');
                ordersCell.textContent = item.totalSold;

                row.appendChild(nameCell);
                row.appendChild(salesCell);
                row.appendChild(ordersCell);

                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching data:", error));
}