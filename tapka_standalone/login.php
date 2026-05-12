<?php
require_once __DIR__ . '/php/config.php';
if (current_user() && (current_user()['role'] ?? '') === 'admin') {
    header('Location: admin.php');
    exit;
}
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Вход в tapka</title>
<style>
:root{--ink:#101828;--muted:#667085;--blue:#2563eb;--line:#d7e0ec}*{box-sizing:border-box}body{min-height:100vh;margin:0;display:grid;place-items:center;background:radial-gradient(circle at 20% 15%,#c7f9ff 0,#eef5ff 36%,#fff7ed 100%);font-family:Inter,Arial,sans-serif;color:var(--ink)}.card{width:min(440px,92vw);background:rgba(255,255,255,.88);backdrop-filter:blur(18px);border:1px solid var(--line);border-radius:28px;padding:30px;box-shadow:0 30px 90px rgba(15,23,42,.16)}.logo{font-size:38px;font-weight:950;letter-spacing:-.08em;margin-bottom:6px}.logo span{color:var(--blue)}p{color:var(--muted);margin:0 0 22px}.field{width:100%;border:1px solid var(--line);border-radius:16px;padding:14px 15px;margin:0 0 12px;font:inherit}.btn{width:100%;border:0;border-radius:999px;background:#101828;color:#fff;font-weight:900;padding:14px;cursor:pointer}.msg{margin-top:14px;color:#b42318;font-weight:700}.links{display:flex;justify-content:space-between;gap:12px;margin-top:18px}.links a{color:#2563eb;text-decoration:none;font-weight:800}
</style>
</head>
<body>
<main class="card">
  <div class="logo">tap<span>ka</span></div>
  <p>Вход администратора или зарегистрированного пользователя.</p>
  <form id="loginForm">
    <input class="field" name="email" type="email" placeholder="Email" value="admin@tapka.local" required>
    <input class="field" name="password" type="password" placeholder="Пароль" value="admin123" required>
    <button class="btn">Войти</button>
  </form>
  <div class="msg" id="msg"></div>
  <div class="links"><a href="index.html">← На сайт</a><a href="#" id="fillDemo">Данные админа</a></div>
</main>
<script>
const form = document.getElementById('loginForm');
const msg = document.getElementById('msg');
document.getElementById('fillDemo').onclick = (e) => { e.preventDefault(); form.email.value='admin@tapka.local'; form.password.value='admin123'; };
form.addEventListener('submit', async (e) => {
  e.preventDefault(); msg.textContent = '';
  const data = Object.fromEntries(new FormData(form).entries());
  const res = await fetch('php/auth.php?action=login', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)});
  const json = await res.json().catch(() => ({}));
  if (!res.ok || !json.success) { msg.textContent = json.error || 'Не удалось войти'; return; }
  location.href = json.user && json.user.role === 'admin' ? 'admin.php' : 'index.html';
});
</script>
</body>
</html>
