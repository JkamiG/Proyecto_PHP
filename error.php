<?php include 'header.php'; ?>

<div class="container">
    <h2>Ha ocurrido un error</h2>
    
    <?php
    // Recogemos el mensaje de error de la URL
    $error_msg = isset($_GET['msg']) ? $_GET['msg'] : 'Error desconocido.';
    
    // Limpiamos por seguridad
    $error_msg = htmlspecialchars($error_msg);
    ?>

    <div class="error-msg">
        <p><?php echo $error_msg; ?></p>
    </div>

    <a href="javascript:history.back()"><button>Volver atrás</button></a>
    <a href="index.html" style="margin-left: 10px;">Ir al Inicio</a>
</div>

</body>
</html>
