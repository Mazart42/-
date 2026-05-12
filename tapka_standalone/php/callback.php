<?php
require_once __DIR__ . '/config.php';

$data = request_json();
$name = trim((string)($data['name'] ?? ''));
$phone = trim((string)($data['phone'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$message = trim((string)($data['message'] ?? ''));

if (!$name || !$phone) {
    json_out(['error' => 'Укажите имя и телефон'], 400);
}
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_out(['error' => 'Укажите корректный email'], 400);
}

$db = db();
$stmt = $db->prepare('INSERT INTO callback_requests (name, phone, email, message) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $name, $phone, $email, $message);
$stmt->execute();
json_out(['success' => true, 'message' => 'Заявка отправлена. Мы перезвоним в рабочее время.']);
