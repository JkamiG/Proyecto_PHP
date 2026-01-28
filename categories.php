<?php
include 'db.php';
include 'header.php';

// Verificar permisos (Solo admin)
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header("Location: index.html");
    exit;
}

// Inicialitzar variables
$nombre = '';
$descripcion = '';
$edit_mode = false;
$id = 0;

// Processar accions (Create, Update, Delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        if (!empty($nombre)) {
            $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
            $stmt->bind_param("ss", $nombre, $descripcion);
            $stmt->execute();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['id'];
        if (!empty($nombre) && !empty($id)) {
            $stmt = $conn->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nombre, $descripcion, $id);
            $stmt->execute();
        }
    }
    // Redirigir per evitar resubmission
    header("Location: categories.php");
    exit;
}

// Processar esborrar (Delete) desde GET
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Podríem afegir verificació de productes existents abans d'esborrar si volguéssim ser més estrictes
    $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: categories.php");
    exit;
}

// Preparar formulari d'edició
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nombre = $row['nombre'];
        $descripcion = $row['descripcion'];
    }
}

// Obtenir llistat de categories
$result = $conn->query("SELECT * FROM categorias");
?>

<div class="container">
    <h1>Gestión de Categorías</h1>
    <a href="dashboard.php"><button style="margin-bottom: 20px;">&laquo; Volver al Panel</button></a>

    <!-- Formulari Create/Update -->
    <div style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
        <h3><?php echo $edit_mode ? 'Editar Categoría' : 'Nueva Categoría'; ?></h3>
        <form action="categories.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion"><?php echo htmlspecialchars($descripcion); ?></textarea>

            <button type="submit"><?php echo $edit_mode ? 'Actualizar' : 'Guardar'; ?></button>
            <?php if ($edit_mode): ?>
                <a href="categories.php"><button type="button" style="background: #777;">Cancelar</button></a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Llistat -->
    <h3>Lista de Categorías</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                    <td>
                        <a href="categories.php?edit_id=<?php echo $row['id']; ?>" class="btn-edit">Editar</a>
                        <a href="categories.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('¿Seguro que quieres borrar esta categoría?');">Borrar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
