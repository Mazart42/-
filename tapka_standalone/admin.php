<?php
require_once __DIR__ . '/php/config.php';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}

require_admin();
$db = db();
$message = '';

function pick(string $name, array $allowed, string $fallback): string {
    $value = $_POST[$name] ?? $fallback;
    return in_array($value, $allowed, true) ? $value : $fallback;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_car') {
        $id = (int)($_POST['id'] ?? 0);
        $brand = trim((string)($_POST['brand'] ?? ''));
        $model = trim((string)($_POST['model'] ?? ''));
        $carClass = pick('car_class', ['economy','comfort','business','suv','electric','minivan','premium'], 'comfort');
        $year = max(1990, min(2035, (int)($_POST['year'] ?? date('Y'))));
        $transmission = pick('transmission', ['auto','manual','robot'], 'auto');
        $fuel = pick('fuel', ['petrol','diesel','hybrid','electric'], 'petrol');
        $seats = max(1, min(12, (int)($_POST['seats'] ?? 5)));
        $priceDay = (float)($_POST['price_day'] ?? 0);
        $deposit = (float)($_POST['deposit'] ?? 0);
        $mileageLimit = max(0, (int)($_POST['mileage_limit'] ?? 300));
        $city = pick('city', tapka_cities(), 'Новосибирск');
        $status = pick('status', ['available','maintenance','hidden'], 'available');
        $description = trim((string)($_POST['description'] ?? ''));
        $image = trim((string)($_POST['image'] ?? ''));
        $uploaded = upload_car_image();
        if ($uploaded) $image = $uploaded;
        if ($image === '') $image = 'assets/cars/solaris.svg';

        if ($brand && $model && $priceDay > 0) {
            if ($id > 0) {
                $stmt = $db->prepare('UPDATE cars SET brand=?, model=?, car_class=?, year=?, transmission=?, fuel=?, seats=?, price_day=?, deposit=?, mileage_limit=?, city=?, status=?, description=?, image=? WHERE id=?');
                $stmt->bind_param('sssissiddissssi', $brand, $model, $carClass, $year, $transmission, $fuel, $seats, $priceDay, $deposit, $mileageLimit, $city, $status, $description, $image, $id);
                $message = $stmt->execute() ? 'Автомобиль обновлён' : 'Ошибка обновления автомобиля';
            } else {
                $stmt = $db->prepare('INSERT INTO cars (brand, model, car_class, year, transmission, fuel, seats, price_day, deposit, mileage_limit, city, status, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sssissiddissss', $brand, $model, $carClass, $year, $transmission, $fuel, $seats, $priceDay, $deposit, $mileageLimit, $city, $status, $description, $image);
                $message = $stmt->execute() ? 'Автомобиль добавлен' : 'Ошибка добавления автомобиля';
            }
        } else {
            $message = 'Заполните марку, модель и цену за сутки';
        }
    }

    if ($action === 'delete_car') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM cars WHERE id = ?');
        $stmt->bind_param('i', $id);
        $message = $stmt->execute() ? 'Автомобиль удалён' : 'Ошибка удаления автомобиля';
    }

    if ($action === 'booking_status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = pick('status', ['new','confirmed','in_progress','done','cancelled'], 'new');
        $stmt = $db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        $message = $stmt->execute() ? 'Статус брони обновлён' : 'Ошибка обновления брони';
    }
}

$cars = $db->query('SELECT * FROM cars ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
$bookings = $db->query("SELECT b.*, CONCAT(c.brand, ' ', c.model) AS car_name FROM bookings b LEFT JOIN cars c ON c.id = b.car_id ORDER BY b.id DESC LIMIT 120")->fetch_all(MYSQLI_ASSOC);
$callbacks = $db->query('SELECT * FROM callback_requests ORDER BY id DESC LIMIT 100')->fetch_all(MYSQLI_ASSOC);
$users = $db->query('SELECT id, name, email, phone, role, created_at FROM users ORDER BY id DESC LIMIT 100')->fetch_all(MYSQLI_ASSOC);
$stats = [
    'cars' => count($cars),
    'bookings' => (int)$db->query('SELECT COUNT(*) c FROM bookings')->fetch_assoc()['c'],
    'new' => (int)$db->query("SELECT COUNT(*) c FROM bookings WHERE status='new'")->fetch_assoc()['c'],
    'revenue' => (float)$db->query("SELECT COALESCE(SUM(total),0) s FROM bookings WHERE status IN ('confirmed','in_progress','done')")->fetch_assoc()['s']
];
$user = current_user();
$classes = ['economy'=>'Эконом','comfort'=>'Комфорт','business'=>'Бизнес','suv'=>'SUV','electric'=>'Электро','minivan'=>'Минивэн','premium'=>'Премиум'];
$statuses = ['available'=>'Доступен','maintenance'=>'Сервис','hidden'=>'Скрыт'];
$bookingStatuses = ['new'=>'Новая','confirmed'=>'Подтверждена','in_progress'=>'В аренде','done'=>'Закрыта','cancelled'=>'Отменена'];
$cities = tapka_cities();
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Админ-панель tapka</title>
<style>
:root{--ink:#111827;--muted:#667085;--bg:#f4f7fb;--card:#fff;--line:#dce4ef;--blue:#2563eb;--cyan:#06b6d4;--green:#16a34a;--red:#ef4444;--radius:22px}*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:radial-gradient(circle at top left,#dff7ff 0,#f4f7fb 34%,#f6f3ed 100%);color:var(--ink);font-family:Inter,Arial,sans-serif}.top{position:sticky;top:0;z-index:5;background:rgba(255,255,255,.82);backdrop-filter:blur(16px);border-bottom:1px solid var(--line)}.top-inner{width:min(1360px,94vw);margin:auto;display:flex;justify-content:space-between;gap:16px;align-items:center;padding:16px 0}.brand{font-weight:950;font-size:28px;letter-spacing:-.06em}.brand span{color:var(--blue)}.small{font-size:12px;color:var(--muted)}.links{display:flex;gap:10px;flex-wrap:wrap}.btn,a.btn{border:0;border-radius:999px;background:var(--ink);color:#fff;text-decoration:none;padding:10px 14px;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px}.btn.blue{background:var(--blue)}.btn.red{background:var(--red)}.btn.ghost{background:#eef3fb;color:var(--ink)}.wrap{width:min(1360px,94vw);margin:24px auto 60px}.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.stat,.card{background:rgba(255,255,255,.88);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 22px 60px rgba(15,23,42,.08)}.stat{padding:18px}.stat strong{display:block;font-size:34px;letter-spacing:-.05em}.card{padding:22px;margin-bottom:20px}.msg{background:#ecfeff;border:1px solid #a5f3fc;padding:13px 16px;border-radius:16px;margin-bottom:18px}.form{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.field{width:100%;border:1px solid var(--line);background:#fff;border-radius:14px;padding:12px 13px;color:var(--ink);font:inherit}.field:focus{outline:3px solid rgba(37,99,235,.12);border-color:var(--blue)}textarea.field{min-height:88px;resize:vertical}.full{grid-column:1/-1}.two{grid-column:span 2}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px}.car-edit{display:grid;grid-template-columns:160px 1fr;gap:18px;border-top:1px solid var(--line);padding:18px 0}.thumb{width:150px;height:105px;object-fit:cover;border-radius:18px;background:#e8eef7;border:1px solid var(--line)}.car-fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.delete-row{margin:-8px 0 14px 178px}.table-wrap{overflow:auto;border:1px solid var(--line);border-radius:18px;background:#fff}table{width:100%;border-collapse:collapse;min-width:900px}th,td{padding:12px;border-bottom:1px solid var(--line);vertical-align:top;text-align:left}th{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);background:#f8fafc}tr:last-child td{border-bottom:0}.pill{display:inline-flex;border-radius:999px;padding:5px 10px;background:#eef3fb;font-weight:800;font-size:12px}.inline{display:flex;gap:8px;align-items:center}.money{font-weight:900;white-space:nowrap}.tabs{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0 18px}.tabs a{border-radius:999px;padding:9px 12px;background:#fff;border:1px solid var(--line);color:var(--ink);text-decoration:none;font-weight:800}@media(max-width:900px){.stats{grid-template-columns:repeat(2,1fr)}.form,.car-fields{grid-template-columns:1fr}.two{grid-column:auto}.car-edit{grid-template-columns:1fr}.delete-row{margin:0 0 14px}.top-inner{align-items:flex-start;flex-direction:column}}@media(max-width:520px){.stats{grid-template-columns:1fr}.links{width:100%}.btn{width:100%}}
</style>
</head>
<body>
<header class="top"><div class="top-inner"><div><div class="brand">tap<span>ka</span> admin</div><div class="small">Вы вошли как <?= e($user['email'] ?? '') ?></div></div><nav class="links"><a class="btn ghost" href="index.html">На сайт</a><a class="btn red" href="admin.php?logout=1">Выйти</a></nav></div></header>
<main class="wrap">
<?php if ($message): ?><div class="msg"><?= e($message) ?></div><?php endif; ?>
<section class="stats">
  <div class="stat"><div class="small">Автомобилей</div><strong><?= (int)$stats['cars'] ?></strong></div>
  <div class="stat"><div class="small">Всего броней</div><strong><?= (int)$stats['bookings'] ?></strong></div>
  <div class="stat"><div class="small">Новые заявки</div><strong><?= (int)$stats['new'] ?></strong></div>
  <div class="stat"><div class="small">Подтверждено на сумму</div><strong><?= number_format($stats['revenue'], 0, ',', ' ') ?> ₽</strong></div>
</section>
<div class="tabs"><a href="#cars">Автопарк</a><a href="#bookings">Брони</a><a href="#clients">Клиенты</a><a href="#callbacks">Заявки</a></div>

<section class="card" id="cars">
<h1>Добавить автомобиль</h1>
<form class="form" method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="save_car">
<input class="field" name="brand" placeholder="Марка" required>
<input class="field" name="model" placeholder="Модель" required>
<select class="field" name="car_class"><?php foreach($classes as $key=>$label): ?><option value="<?= $key ?>"><?= $label ?></option><?php endforeach; ?></select>
<input class="field" type="number" name="year" placeholder="Год" value="<?= date('Y') ?>">
<select class="field" name="transmission"><option value="auto">Автомат</option><option value="manual">Механика</option><option value="robot">Робот</option></select>
<select class="field" name="fuel"><option value="petrol">Бензин</option><option value="diesel">Дизель</option><option value="hybrid">Гибрид</option><option value="electric">Электро</option></select>
<input class="field" type="number" name="seats" placeholder="Мест" value="5">
<input class="field" type="number" step="0.01" name="price_day" placeholder="Цена/сутки" required>
<input class="field" type="number" step="0.01" name="deposit" placeholder="Залог">
<input class="field" type="number" name="mileage_limit" placeholder="Лимит км/сутки" value="300">
<select class="field" name="city"><?php foreach($cities as $cityName): ?><option value="<?= e($cityName) ?>" <?= $cityName==='Новосибирск'?'selected':'' ?>><?= e($cityName) ?></option><?php endforeach; ?></select>
<select class="field" name="status"><?php foreach($statuses as $key=>$label): ?><option value="<?= $key ?>"><?= $label ?></option><?php endforeach; ?></select>
<input class="field two" name="image" placeholder="Путь к фото, например assets/cars/solaris.svg">
<input class="field two" type="file" name="image_file" accept="image/*">
<textarea class="field full" name="description" placeholder="Описание автомобиля"></textarea>
<button class="btn blue full">Сохранить автомобиль</button>
</form>
</section>

<section class="card">
<h2>Автопарк</h2>
<?php foreach ($cars as $car): ?>
<form method="post" enctype="multipart/form-data" class="car-edit">
<input type="hidden" name="action" value="save_car">
<input type="hidden" name="id" value="<?= (int)$car['id'] ?>">
<div><img class="thumb" src="<?= e($car['image']) ?>" alt=""><div class="small">ID <?= (int)$car['id'] ?></div></div>
<div class="car-fields">
  <input class="field" name="brand" value="<?= e($car['brand']) ?>" placeholder="Марка">
  <input class="field" name="model" value="<?= e($car['model']) ?>" placeholder="Модель">
  <select class="field" name="car_class"><?php foreach($classes as $key=>$label): ?><option value="<?= $key ?>" <?= $car['car_class']===$key?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select>
  <input class="field" type="number" name="year" value="<?= (int)$car['year'] ?>" placeholder="Год">
  <select class="field" name="transmission"><option value="auto" <?= $car['transmission']==='auto'?'selected':'' ?>>Автомат</option><option value="manual" <?= $car['transmission']==='manual'?'selected':'' ?>>Механика</option><option value="robot" <?= $car['transmission']==='robot'?'selected':'' ?>>Робот</option></select>
  <select class="field" name="fuel"><option value="petrol" <?= $car['fuel']==='petrol'?'selected':'' ?>>Бензин</option><option value="diesel" <?= $car['fuel']==='diesel'?'selected':'' ?>>Дизель</option><option value="hybrid" <?= $car['fuel']==='hybrid'?'selected':'' ?>>Гибрид</option><option value="electric" <?= $car['fuel']==='electric'?'selected':'' ?>>Электро</option></select>
  <input class="field" type="number" name="seats" value="<?= (int)$car['seats'] ?>" placeholder="Мест">
  <input class="field" type="number" step="0.01" name="price_day" value="<?= e((string)$car['price_day']) ?>" placeholder="Цена/сутки">
  <input class="field" type="number" step="0.01" name="deposit" value="<?= e((string)$car['deposit']) ?>" placeholder="Залог">
  <input class="field" type="number" name="mileage_limit" value="<?= (int)$car['mileage_limit'] ?>" placeholder="Км/сутки">
  <select class="field" name="city"><?php foreach($cities as $cityName): ?><option value="<?= e($cityName) ?>" <?= $car['city']===$cityName?'selected':'' ?>><?= e($cityName) ?></option><?php endforeach; ?></select>
  <select class="field" name="status"><?php foreach($statuses as $key=>$label): ?><option value="<?= $key ?>" <?= $car['status']===$key?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select>
  <input class="field full" name="image" value="<?= e($car['image']) ?>" placeholder="Путь к фото">
  <input class="field full" type="file" name="image_file" accept="image/*">
  <textarea class="field full" name="description" placeholder="Описание"><?= e($car['description']) ?></textarea>
  <button class="btn blue">Сохранить</button>
</div>
</form>
<form method="post" class="delete-row" onsubmit="return confirm('Удалить автомобиль и связанные брони?')">
<input type="hidden" name="action" value="delete_car"><input type="hidden" name="id" value="<?= (int)$car['id'] ?>"><button class="btn red">Удалить #<?= (int)$car['id'] ?></button>
</form>
<?php endforeach; ?>
</section>

<section class="card" id="bookings">
<h2>Брони</h2>
<div class="table-wrap"><table><tr><th>ID</th><th>Клиент</th><th>Авто</th><th>Даты</th><th>Выдача</th><th>Сумма</th><th>Статус</th><th>Комментарий</th></tr>
<?php foreach ($bookings as $b): ?>
<tr>
<td><?= (int)$b['id'] ?></td>
<td><strong><?= e($b['customer_name']) ?></strong><br><span class="small"><?= e($b['customer_phone']) ?><br><?= e($b['customer_email']) ?></span></td>
<td><?= e($b['car_name']) ?></td>
<td><?= e($b['date_from']) ?> → <?= e($b['date_to']) ?><br><span class="small"><?= (int)$b['days'] ?> сут.</span></td>
<td><?= e($b['pickup_city']) ?><br><span class="small"><?= e($b['pickup_address']) ?></span></td>
<td class="money"><?= number_format((float)$b['total'], 0, ',', ' ') ?> ₽</td>
<td><form method="post" class="inline"><input type="hidden" name="action" value="booking_status"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><select class="field" name="status"><?php foreach($bookingStatuses as $key=>$label): ?><option value="<?= $key ?>" <?= $b['status']===$key?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select><button class="btn">OK</button></form></td>
<td><span class="small"><?= e($b['comment']) ?><br><?= e($b['created_at']) ?></span></td>
</tr>
<?php endforeach; ?>
</table></div>
</section>

<section class="card" id="clients">
<h2>Пользователи</h2>
<div class="table-wrap"><table><tr><th>ID</th><th>Имя</th><th>Email</th><th>Телефон</th><th>Роль</th><th>Дата</th></tr>
<?php foreach ($users as $u): ?><tr><td><?= (int)$u['id'] ?></td><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['phone']) ?></td><td><span class="pill"><?= e($u['role']) ?></span></td><td><?= e($u['created_at']) ?></td></tr><?php endforeach; ?>
</table></div>
</section>

<section class="card" id="callbacks">
<h2>Заявки на звонок</h2>
<div class="table-wrap"><table><tr><th>ID</th><th>Имя</th><th>Телефон</th><th>Email</th><th>Сообщение</th><th>Дата</th></tr>
<?php foreach ($callbacks as $c): ?><tr><td><?= (int)$c['id'] ?></td><td><?= e($c['name']) ?></td><td><?= e($c['phone']) ?></td><td><?= e($c['email']) ?></td><td><?= e($c['message']) ?></td><td><?= e($c['created_at']) ?></td></tr><?php endforeach; ?>
</table></div>
</section>
</main>
</body>
</html>
