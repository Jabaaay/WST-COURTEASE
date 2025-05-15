$(function() {
    // Validate JSON input
    function isValidJSON(str) {
        try {
            JSON.parse(str);
            return true;
        } catch (e) {
            return false;
        }
    }

    // Handle form submission
    $('form').on('submit', function(e) {
        const chartData = $('#chart_data').val();
        
        if (!isValidJSON(chartData)) {
            e.preventDefault();
            alert('Please enter valid JSON data for the chart');
            return false;
        }

        // Store chart configuration in localStorage
        const chartConfig = {
            type: $('#chart_type').val(),
            title: $('#chart_title').val(),
            data: JSON.parse(chartData)
        };
        
        localStorage.setItem('dashboardChartConfig', JSON.stringify(chartConfig));
    });

    // Preview chart data
    $('#chart_data').on('change', function() {
        const chartData = $(this).val();
        if (isValidJSON(chartData)) {
            try {
                const data = JSON.parse(chartData);
                if (!data.labels || !data.datasets) {
                    alert('Invalid chart data format. Please include labels and datasets.');
                }
            } catch (e) {
                alert('Invalid JSON format');
            }
        }
    });
}); 