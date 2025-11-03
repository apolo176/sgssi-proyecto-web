<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - MachacasDB</title>
    <link rel="stylesheet" href="/app/styles.css">
    <script src="/app/js/utils.js"></script>

    <script src="/app/js/login.js" defer></script>
</head>

<body>
    <header class="site-header">
        <h1>🔑 Iniciar sesión</h1>

        <div class="header-right" id="userArea">
            <!-- Aquí se insertará el círculo del usuario si está logueado -->
        </div>
    </header>
    <main class="container">
        <form id="login_form" method="post">
            <label>Email:</label>
            <input type="text" name="email" placeholder="Introduce tu email">

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Introduce tu contraseña">

            <?php $t = htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>
            <input type="hidden" name="csrf_token" value="<?php echo substr($t, 0, 32) . substr($t, 32); ?>" maxlength="128">

            <button type="submit">Iniciar sesión</button>
        </form>
        <p>¿No tienes cuenta? <a href="/register">Regístrate aquí</a></p>
    </main>

    <footer class="footer">Desarrollado por: Alex Isasi, Iker Ciordia, Liviu Deleanu, Jon Requies y Eder Torres</footer>
</body>

</html>