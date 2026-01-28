<?php
include 'db.php';
include 'header.php';

// Si ja estem loguejats, redirigim al dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Processar formulari
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: error.php?msg=" . urlencode("Todos los campos son obligatorios."));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: error.php?msg=" . urlencode("Formato de email inválido."));
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: error.php?msg=" . urlencode("Las contraseñas no coinciden."));
        exit;
    }

    // Comprobar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: error.php?msg=" . urlencode("El email ya está registrado."));
        exit;
    }
    $stmt->close();

    // --Hash de la contraseña--
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // --Insertar usuario--
    $rol = 'cliente'; // Per defecte
    $insert_stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("ssss", $nombre, $email, $password_hashed, $rol);

    if ($insert_stmt->execute()) {
        header("Location: login.php?registrado=1");
        exit;
    } else {
        header("Location: error.php?msg=" . urlencode("Error al registrar: " . $conn->error));
        exit;
    }
}
?>

<div class="container">
    <h2>Registro de Usuario</h2>
    <form action="register.php" method="POST">
        <label for="nombre">Nombre Completo:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="login.php">Inicia Sesión aquí</a></p>
</div>

</body>
</html>
