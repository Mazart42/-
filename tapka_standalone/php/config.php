<?php
// tapka: настройки подключения к MySQL для OpenServer/XAMPP/phpMyAdmin.
// Импортируйте php/database.sql, затем при необходимости измените DB_USER/DB_PASS.

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'tapka';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function db(): mysqli {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        json_out(['error' => 'Нет подключения к базе данных'], 500);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

function request_json(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $_POST;
}

function json_out(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}


function tapka_cities(): array {
    return [
        'Новосибирск',
        'Кемерово',
        'Топки',
        'Улан-Удэ',
        'Ордынка',
        'Барабинск',
        'Тогучин',
        'Душанбе (Таджикистан)'
    ];
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_admin(): void {
    $user = current_user();
    if (!$user || ($user['role'] ?? '') !== 'admin') {
        header('Location: login.php');
        exit;
    }
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function upload_car_image(string $field = 'image_file'): ?string {
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$field];
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) return null;
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) return null;

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg'
    ];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) return null;

    $dir = dirname(__DIR__) . '/uploads/cars';
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $name = 'car_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $target = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) return null;
    return 'uploads/cars/' . $name;
}
