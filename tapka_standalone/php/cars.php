<?php
require_once __DIR__ . '/config.php';

$db = db();
$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $class = trim((string)($_GET['class'] ?? ''));
    $city = trim((string)($_GET['city'] ?? ''));
    $q = trim((string)($_GET['q'] ?? ''));

    $where = ["status = 'available'"];
    $types = '';
    $params = [];

    if ($class !== '' && $class !== 'all') {
        $where[] = 'car_class = ?';
        $types .= 's';
        $params[] = $class;
    }
    if ($city !== '' && $city !== 'all') {
        $where[] = 'city = ?';
        $types .= 's';
        $params[] = $city;
    }
    if ($q !== '') {
        $where[] = '(brand LIKE ? OR model LIKE ? OR description LIKE ?)';
        $types .= 'sss';
        $like = '%' . $q . '%';
        $params[] = $like; $params[] = $like; $params[] = $like;
    }

    $sql = 'SELECT * FROM cars WHERE ' . implode(' AND ', $where) . ' ORDER BY price_day ASC, id DESC';
    $stmt = $db->prepare($sql);
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    json_out(['cars' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
}

if ($action === 'cities') {
    json_out(['cities' => tapka_cities()]);
}

json_out(['error' => 'Неизвестное действие'], 404);
