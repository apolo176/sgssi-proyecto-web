<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - MachacasDB</title>
    <link rel="stylesheet" href="/app/styles.css">
    <script src="/app/js/utils.js"></script>
    <script src="/app/js/register.js" defer></script>
</head>
<body>
<header class="site-header">
    <h1 id="title">🧾 Registro de usuario</h1>
    <div class="header-right" id="userArea"></div>
</header>
<main class="container">
    <form id="register_form" method="post">
        <label>Nombre y apellidos:</label>
        <input name="nombreapellido" type="text" placeholder="Nombre y apellidos">
        <label>DNI:</label>
        <input name="DNI" type="text" placeholder="12345678-X">
        <label>Contraseña:</label>
        <input name="password" type="password" placeholder="Contraseña segura">
        <label>Teléfono:</label>
        <input name="telefono" type="text" placeholder="685123456">
        <label>Fecha de Nacimiento:</label>
        <input name="fechanac" type="text" placeholder="aaaa-mm-dd">
        <label>Email:</label>
        <input name="email" type="text" placeholder="tuemail@dominio.com">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="128">
        <button id="register_submit" type="submit">📝 Registrarse</button>
    </form>
    <p id="alreadyHasAccount">¿Ya tienes cuenta? <a href="/login">Inicia sesión aquí</a></p>
</main>
<footer class="footer">Desarrollado por: Alex Isasi, Iker Ciordia, Liviu Deleanu, Jon Requies y Eder Torres</footer>
</body>
</html>
