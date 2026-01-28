<?php
// Iniciar sesión si no está iniciada ya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <!-- Enlace al estilo CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <nav>
        <div class="logo">
            <!-- Enlace al inicio -->
            <a href="index.html" style="font-size: 1.5em; font-weight: bold; margin:0;">Mi Tienda</a>
        </div>
        <div class="menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Usuario logueado -->
                <span>Hola, <?php echo htmlspecialchars($_SESSION['user_nombre']); ?> (<?php echo $_SESSION['user_rol']; ?>)</span>
                
                <a href="dashboard.php">Panel</a>
                
                <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                    <a href="admin_products.php">Productos</a>
                    <a href="categories.php">Categorías</a>
                <?php endif; ?>

                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <!-- Usuario no logueado -->
                <a href="login.php">Iniciar Sesión</a>
                <a href="register.php">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
