<?php
require_once __DIR__ . '/bootstrap.php';

$pdo = $container->getPdo();
$chart_type = $_GET['type'] ?? 'revenue';

$labels = [];
$data = [];
$label = '';

header('Content-Type: application/json');

switch ($chart_type) {
    case 'revenue':
        $label = 'Daily Sales Revenue (€)';
        $stmt = $pdo->prepare("
            SELECT DATE(purchased_at) as date, SUM(amount_cents) as value
            FROM purchases
            WHERE purchased_at >= CURDATE() - INTERVAL 30 DAY
            GROUP BY date
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $indexed_results = [];
        foreach ($results as $row) {
            $indexed_results[$row['date']] = $row['value'] / 100;
        }

        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('M d', strtotime($date));
            $data[] = $indexed_results[$date] ?? 0;
        }
        break;

    case 'bookings':
        $label = 'Daily Bookings';
        $stmt = $pdo->prepare("
            SELECT DATE(created_at) as date, COUNT(id) as value
            FROM bookings
            WHERE created_at >= CURDATE() - INTERVAL 30 DAY
            GROUP BY date
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $indexed_results = [];
        foreach ($results as $row) {
            $indexed_results[$row['date']] = (int)$row['value'];
        }

        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('M d', strtotime($date));
            $data[] = $indexed_results[$date] ?? 0;
        }
        break;

    case 'users':
        $label = 'New User Registrations';
        $stmt = $pdo->prepare("
            SELECT DATE(created_at) as date, COUNT(id) as value
            FROM users
            WHERE created_at >= CURDATE() - INTERVAL 30 DAY
            GROUP BY date
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $indexed_results = [];
        foreach ($results as $row) {
            $indexed_results[$row['date']] = (int)$row['value'];
        }

        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('M d', strtotime($date));
            $data[] = $indexed_results[$date] ?? 0;
        }
        break;

    case 'packages':
        $label = 'Package Popularity';
        $stmt = $pdo->prepare("
            SELECT pk.name as package_name, COUNT(p.id) as value
            FROM purchases p
            JOIN packages pk ON p.package_id = pk.id
            GROUP BY pk.name
            ORDER BY value DESC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $labels[] = $row['package_name'];
            $data[] = (int)$row['value'];
        }
        break;
}

echo json_encode([
    'labels' => $labels,
    'datasets' => [
        [
            'label' => $label,
            'data' => $data,
            'borderColor' => 'rgb(75, 192, 192)',
            'tension' => 0.1,
            'fill' => false,
        ]
    ]
]);
