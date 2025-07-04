<?php
require_once __DIR__ . '/bootstrap.php';

// --- Input Validation & Defaults ---
$pdo = $container->getPdo();
$chart_type = $_GET['type'] ?? 'revenue';
$period_days = isset($_GET['period']) ? (int)$_GET['period'] : 30;

// Whitelist valid chart types to prevent unexpected behavior
$valid_chart_types = ['revenue', 'users', 'bookings', 'packages_sold'];
if (!in_array($chart_type, $valid_chart_types)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid chart type specified.']);
    exit;
}

// Whitelist valid periods
$valid_periods = [30, 60, 90];
if (!in_array($period_days, $valid_periods)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid period specified.']);
    exit;
}

// --- Data Fetching Logic ---
$labels = [];
$data = [];
$label = '';
$y_axis_label = '';

header('Content-Type: application/json');

// Base query parts
$select_clause = '';
$from_clause = '';
$group_by_clause = 'GROUP BY date ORDER BY date ASC';
$date_column = '';

switch ($chart_type) {
    case 'revenue':
        $label = 'Sales Volume';
        $y_axis_label = 'Amount (€)';
        $select_clause = 'SELECT DATE(purchased_at) as date, SUM(amount_cents) / 100 as value';
        $from_clause = 'FROM purchases';
        $date_column = 'purchased_at';
        break;

    case 'users':
        $label = 'New User Signups';
        $y_axis_label = 'Number of Users';
        $select_clause = 'SELECT DATE(created_at) as date, COUNT(id) as value';
        $from_clause = 'FROM users';
        $date_column = 'created_at';
        break;

    case 'bookings':
        $label = 'Lessons Booked';
        $y_axis_label = 'Number of Bookings';
        $select_clause = 'SELECT DATE(created_at) as date, COUNT(id) as value';
        $from_clause = 'FROM bookings';
        $date_column = 'created_at';
        break;

    case 'packages_sold':
        $label = 'Packages Sold';
        $y_axis_label = 'Number of Packages';
        $select_clause = 'SELECT DATE(purchased_at) as date, COUNT(id) as value';
        $from_clause = 'FROM purchases';
        $date_column = 'purchased_at';
        break;
}

// --- Query Execution ---
$stmt = $pdo->prepare("
    $select_clause
    $from_clause
    WHERE $date_column >= CURDATE() - INTERVAL :period DAY
    $group_by_clause
");
$stmt->execute(['period' => $period_days]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Data Processing for Chart ---
$indexed_results = [];
foreach ($results as $row) {
    $indexed_results[$row['date']] = (float)$row['value'];
}

// Generate labels and data for the entire period to ensure consistency
for ($i = $period_days - 1; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('M d', strtotime($date)); // Format for display (e.g., "Jul 04")
    $data[] = $indexed_results[$date] ?? 0;
}

// --- JSON Response ---
echo json_encode([
    'labels' => $labels,
    'datasets' => [
        [
            'label' => $label,
            'data' => $data,
            'borderColor' => 'rgba(59, 130, 246, 1)', // A modern blue
            'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
            'tension' => 0.2,
            'fill' => true,
            'pointBackgroundColor' => 'rgba(59, 130, 246, 1)',
            'pointBorderColor' => '#fff',
            'pointHoverRadius' => 7,
            'pointHoverBackgroundColor' => 'rgba(59, 130, 246, 1)',
        ]
    ],
    'options' => [
        'maintainAspectRatio' => false,
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'title' => [
                    'display' => true,
                    'text' => $y_axis_label
                ]
            ]
        ],
        'plugins' => [
            'tooltip' => [
                'callbacks' => [
                    'label' => 'function(context) {
                        let label = context.dataset.label || "";
                        if (label) {
                            label += ": ";
                        }
                        if (context.parsed.y !== null) {
                            if ("' . $chart_type . '" === "revenue") {
                                label += new Intl.NumberFormat("nl-NL", { style: "currency", currency: "EUR" }).format(context.parsed.y);
                            } else {
                                label += context.parsed.y;
                            }
                        }
                        return label;
                    }'
                ]
            ]
        ]
    ]
]);