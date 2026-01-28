<?php
session_start();
// Destruir totes les variables de sessió
session_unset();

// Destruir la sessió
session_destroy();

// Redirigir a index
header("Location: index.html");
exit;
?>
