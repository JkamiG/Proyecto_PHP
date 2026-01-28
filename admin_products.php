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
$precio = '';
$stock = '';
$categoria_id = '';
$edit_mode = false;
$id = 0;

// Processar accions (Create, Update, Delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria_id = $_POST['categoria_id'];

    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        if (!empty($nombre) && is_numeric($precio) && is_numeric($stock)) {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssddi", $nombre, $descripcion, $precio, $stock, $categoria_id);
            $stmt->execute();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['id'];
        if (!empty($nombre) && !empty($id)) {
            $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ? WHERE id = ?");
            $stmt->bind_param("ssddii", $nombre, $descripcion, $precio, $stock, $categoria_id, $id);
            $stmt->execute();
        }
    }
    header("Location: admin_products.php");
    exit;
}

// Processar esborrar
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: admin_products.php");
    exit;
}

// Preparar formulari d'edició
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nombre = $row['nombre'];
        $descripcion = $row['descripcion'];
        $precio = $row['precio'];
        $stock = $row['stock'];
        $categoria_id = $row['categoria_id'];
    }
}

// Obtenir categories per al select
$cats_result = $conn->query("SELECT * FROM categorias");
$categorias = [];
while($cat = $cats_result->fetch_assoc()) {
    $categorias[] = $cat;
}

// Listat de productes amb accions
$sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id";
$result = $conn->query($sql);
?>

<div class="container">
    <h1>Gestión de Productos</h1>
    <a href="dashboard.php"><button style="margin-bottom: 20px;">&laquo; Volver al Panel</button></a>

    <!-- Formulari -->
    <div style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
        <h3><?php echo $edit_mode ? 'Editar Producto' : 'Nuevo Producto'; ?></h3>
        <form action="admin_products.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'create'; ?>">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion"><?php echo htmlspecialchars($descripcion); ?></textarea>

            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <label for="precio">Precio (€):</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($precio); ?>" required>
                </div>
                <div style="flex: 1;">
                    <label for="stock">Stock:</label>
                    <input type="number" name="stock" value="<?php echo htmlspecialchars($stock); ?>" required>
                </div>
            </div>

            <label for="categoria_id">Categoría:</label>
            <select name="categoria_id" required>
                <option value="">Selecciona...</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $categoria_id ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit"><?php echo $edit_mode ? 'Actualizar' : 'Guardar'; ?></button>
            <?php if ($edit_mode): ?>
                <a href="admin_products.php"><button type="button" style="background: #777;">Cancelar</button></a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Llistat -->
    <h3>Lista de Productos</h3>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['categoria_nombre']); ?></td>
                    <td><?php echo number_format($row['precio'], 2); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td>
                        <a href="admin_products.php?edit_id=<?php echo $row['id']; ?>" class="btn-edit">Editar</a>
                        <a href="admin_products.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('¿Borrar este producto?');">Borrar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
