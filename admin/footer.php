</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script nonce="<?php echo $nonce ?? ''; ?>">
document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM fully loaded and parsed.");

    const chartCanvas = document.getElementById('businessChart');
    if (!chartCanvas) {
        console.error("Chart canvas element with ID 'businessChart' not found.");
        return;
    }
    console.log("Chart canvas element found.");
    const ctx = chartCanvas.getContext('2d');

    let chart;

    const chartDataTypeSelect = document.getElementById('chartDataType');
    const chartTimePeriodSelect = document.getElementById('chartTimePeriod');

    if (!chartDataTypeSelect || !chartTimePeriodSelect) {
        console.error("Chart filter dropdowns not found.");
        return;
    }
    console.log("Chart filter dropdowns found.");

    const chartLoader = document.getElementById('chartLoader');

    async function fetchChartData(type, period) {
        const url = `graph_data.php?type=${type}&period=${period}`;
        console.log(`Fetching chart data from: ${url}`);
        chartLoader.style.display = 'block'; // Show loader
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            console.log("Successfully fetched and parsed chart data:", data);
            return data;
        } catch (error) {
            console.error("Failed to fetch or parse chart data:", error);
            const canvasCtx = chartCanvas.getContext('2d');
            canvasCtx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
            canvasCtx.font = "16px 'Inter', sans-serif";
            canvasCtx.fillStyle = "rgb(107, 114, 128)";
            canvasCtx.textAlign = "center";
            canvasCtx.fillText("Could not load chart data. Check console for errors.", chartCanvas.width / 2, chartCanvas.height / 2);
            return null;
        } finally {
            chartLoader.style.display = 'none'; // Hide loader
        }
    }

    function renderChart(data) {
        if (!data || !data.datasets) {
            console.error("Render chart called with invalid data:", data);
            return;
        }
        console.log("Rendering chart with data:", data);

        if (data.options && data.options.plugins && data.options.plugins.tooltip && data.options.plugins.tooltip.callbacks && data.options.plugins.tooltip.callbacks.label) {
            try {
                const callbackBody = data.options.plugins.tooltip.callbacks.label.substring(
                    data.options.plugins.tooltip.callbacks.label.indexOf('{') + 1,
                    data.options.plugins.tooltip.callbacks.label.lastIndexOf('}')
                );
                data.options.plugins.tooltip.callbacks.label = new Function('context', callbackBody);
            } catch (e) {
                console.error("Failed to parse tooltip callback function:", e);
                data.options.plugins.tooltip.callbacks.label = function(context) {
                    return `${context.dataset.label}: ${context.parsed.y}`;
                };
            }
        }

        if (chart) {
            console.log("Destroying existing chart instance.");
            chart.destroy();
        }

        console.log("Creating new Chart instance.");
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: data.options || {}
        });
        console.log("Chart rendered successfully.");
    }

    async function updateChart() {
        console.log("updateChart function called.");
        const dataType = chartDataTypeSelect.value;
        const timePeriod = chartTimePeriodSelect.value;
        const data = await fetchChartData(dataType, timePeriod);
        renderChart(data);
    }

    console.log("Initializing chart.");
    updateChart();

    chartDataTypeSelect.addEventListener('change', updateChart);
    chartTimePeriodSelect.addEventListener('change', updateChart);
});
</script>
</body>
</html>
