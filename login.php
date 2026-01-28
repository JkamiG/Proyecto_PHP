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
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: error.php?msg=" . urlencode("Email y contraseña obligatorios."));
        exit;
    }

    // Buscar usuari per email
    $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verificar contrasenya hash
        if (password_verify($password, $user['password'])) {
            // Login correcte: Iniciem sessió
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_rol'] = $user['rol'];

            header("Location: dashboard.php");
            exit;
        } else {
            // Contrasenya incorrecta
             header("Location: error.php?msg=" . urlencode("Credenciales incorrectas (Password)."));
             exit;
        }
    } else {
        // Usuari no trobat
        header("Location: error.php?msg=" . urlencode("Credenciales incorrectas (Email no encontrado)."));
        exit;
    }
}
?>

<div class="container">
    <h2>Iniciar Sesión</h2>
    
    <?php if (isset($_GET['registrado'])): ?>
        <div class="success-msg">Registro exitoso. Ahora puedes iniciar sesión.</div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
</div>

</body>
</html>
