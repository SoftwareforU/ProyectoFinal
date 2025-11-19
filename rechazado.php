<?php
session_start();
// Si no hay usuario logueado, lo envío al login
if (empty($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Acceso denegado</title>
  <link rel="stylesheet" href="holis.css">
</head>
<body>
  <div id="login">
    <h2>Solicitud Rechazada</h2>
    <p>Lo sentimos, tu postulación fue rechazada y no puedes acceder al área de miembros.</p>
    <p>Si crees que hubo un error, comunícate con la cooperativa.</p>
    <p><a href="landingpage.html">Cerrar sesión</a></p>
  </div>
</body>
</html>
