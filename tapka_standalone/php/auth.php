<?php
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';
$data = request_json();

if ($action === 'session') {
    json_out(['user' => current_user()]);
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    json_out(['success' => true]);
}

$email = trim((string)($data['email'] ?? ''));
$password = (string)($data['password'] ?? '');

if ($action === 'login') {
    if (!$email || !$password) json_out(['error' => 'Введите email и пароль'], 400);

    $db = db();
    $stmt = $db->prepare('SELECT id, name, email, phone, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_out(['error' => 'Неверный email или пароль'], 401);
    }

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role' => $user['role']
    ];
    json_out(['success' => true, 'user' => $_SESSION['user']]);
}

if ($action === 'register') {
    $name = trim((string)($data['name'] ?? ''));
    $phone = trim((string)($data['phone'] ?? ''));
    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 6) {
        json_out(['error' => 'Заполните имя, email и пароль от 6 символов'], 400);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';
    $db = db();
    $stmt = $db->prepare('INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $name, $email, $phone, $hash, $role);
    if (!$stmt->execute()) {
        json_out(['error' => 'Такой email уже зарегистрирован'], 409);
    }

    $_SESSION['user'] = [
        'id' => (int)$db->insert_id,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'role' => $role
    ];
    json_out(['success' => true, 'user' => $_SESSION['user']]);
}

json_out(['error' => 'Неизвестное действие'], 404);
