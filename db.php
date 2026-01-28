<?php
// Configuración de la base de datos
$host = 'localhost';
$db_user = 'root'; // Usuario por defecto de XAMPP
$db_pass = '';     // Contraseña por defecto de XAMPP (vacía)
$db_name = 'tienda_online';

//  Intentar conectar a la base de datos
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

//  Verificar la conexión
if ($conn->connect_error) {
    // Si falla, mostramos un error y detenemos la ejecución
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres a utf8 para evitar problemas con acentos y ñ
$conn->set_charset("utf8");
?>
