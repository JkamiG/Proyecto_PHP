<?php
include 'db.php';
include 'header.php';

// Verificar si l'usuari està loguejat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_rol = $_SESSION['user_rol'];

// Lògica de Cerca i Ordenació / Lógica de Búsqueda y Ordenación
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$order = isset($_GET['order']) ? $_GET['order'] : 'id'; // per defecte per ID
$dir = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';

// Validar camps d'ordenació per seguretat (evitar SQL Injection directa en ORDER BY)
$allowed_orders = ['nombre', 'precio', 'stock'];
if (!in_array($order, $allowed_orders)) {
    $order = 'nombre';
}
$allowed_dirs = ['ASC', 'DESC'];
if (!in_array($dir, $allowed_dirs)) {
    $dir = 'ASC';
}

// Construir la consulta
$sql = "SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.nombre LIKE ? 
        ORDER BY $order $dir";

$stmt = $conn->prepare($sql);
$search_param = "%" . $search . "%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1>Panel Principal - <?php echo ucfirst($user_rol); ?></h1>

    <?php if ($user_rol === 'admin'): ?>
        <div style="background: #e9ecef; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
            <h3>Herramientas de Administrador</h3>
            <a href="categories.php"><button>Gestionar Categorías</button></a>
            <a href="admin_products.php"><button>Gestionar Productos</button></a>
        </div>
    <?php endif; ?>

    <h3>Catálogo de Productos</h3>

    <!-- Formulari de Búsqueda i Filtre -->
    <form action="dashboard.php" method="GET" style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" placeholder="Buscar por nombre..." value="<?php echo htmlspecialchars($search); ?>" style="margin-bottom: 0; flex: 1;">
            
            <select name="order" style="margin-bottom: 0; width: auto;">
                <option value="nombre" <?php echo $order == 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                <option value="precio" <?php echo $order == 'precio' ? 'selected' : ''; ?>>Precio</option>
                <option value="stock" <?php echo $order == 'stock' ? 'selected' : ''; ?>>Stock</option>
            </select>

            <select name="dir" style="margin-bottom: 0; width: auto;">
                <option value="ASC" <?php echo $dir == 'ASC' ? 'selected' : ''; ?>>Ascendente</option>
                <option value="DESC" <?php echo $dir == 'DESC' ? 'selected' : ''; ?>>Descendente</option>
            </select>

            <button type="submit">Filtrar</button>
        </div>
    </form>

    <!-- Taula de Resultats -->
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <?php if ($user_rol === 'cliente'): ?>
                        <th>Acción</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['categoria_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                        <td><?php echo number_format($row['precio'], 2); ?> €</td>
                        <td><?php echo $row['stock']; ?></td>
                        
                        <?php if ($user_rol === 'cliente'): ?>
                            <td>
                                <!-- Simuació de compra -->
                                <button onclick="alert('Funcionalidad de compra no implementada en esta versión simplificada.')">Comprar</button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron productos.</p>
    <?php endif; ?>

</div>

</body>
</html>
