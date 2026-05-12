<?php
require_once __DIR__ . '/config.php';

$data = request_json();
$carId = (int)($data['car_id'] ?? 0);
$name = trim((string)($data['customer_name'] ?? ''));
$phone = trim((string)($data['customer_phone'] ?? ''));
$email = trim((string)($data['customer_email'] ?? ''));
$city = trim((string)($data['pickup_city'] ?? ''));
$address = trim((string)($data['pickup_address'] ?? ''));
$dateFrom = trim((string)($data['date_from'] ?? ''));
$dateTo = trim((string)($data['date_to'] ?? ''));
$comment = trim((string)($data['comment'] ?? ''));

if (!$carId || !$name || !$phone || !$city || !$dateFrom || !$dateTo) {
    json_out(['error' => 'Заполните автомобиль, имя, телефон, город и даты аренды'], 400);
}
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_out(['error' => 'Укажите корректный email или оставьте поле пустым'], 400);
}
if (!in_array($city, tapka_cities(), true)) {
    json_out(['error' => 'Выберите город из списка tapka'], 400);
}

$from = DateTime::createFromFormat('Y-m-d', $dateFrom);
$to = DateTime::createFromFormat('Y-m-d', $dateTo);
if (!$from || !$to || $to <= $from) {
    json_out(['error' => 'Дата возврата должна быть позже даты выдачи'], 400);
}
$days = max(1, (int)$from->diff($to)->days);

$db = db();
$stmt = $db->prepare('SELECT id, price_day, status FROM cars WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $carId);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();
if (!$car || $car['status'] !== 'available') {
    json_out(['error' => 'Автомобиль недоступен для бронирования'], 404);
}

$total = $days * (float)$car['price_day'];
$user = current_user();
$userId = $user ? (int)$user['id'] : null;

$stmt = $db->prepare('INSERT INTO bookings (user_id, car_id, customer_name, customer_phone, customer_email, pickup_city, pickup_address, date_from, date_to, days, total, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('iisssssssids', $userId, $carId, $name, $phone, $email, $city, $address, $dateFrom, $dateTo, $days, $total, $comment);
if (!$stmt->execute()) {
    json_out(['error' => 'Не удалось создать бронь'], 500);
}

json_out([
    'success' => true,
    'booking_id' => $db->insert_id,
    'days' => $days,
    'total' => $total,
    'message' => 'Бронь создана. Менеджер tapka свяжется с вами для подтверждения.'
]);
