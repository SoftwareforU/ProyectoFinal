<?php
session_start();
require 'conexiones.php';

// 1) Protejo acceso: sólo usuario normal que ya aplicó
if (empty($_SESSION['id_usuario']) || $_SESSION['role'] !== 'usuario') {
    header('Location: login.php');
    exit;
}

// 2) Consulto estado
$stmt = $pdo->prepare(
  'SELECT estado
     FROM formularios
    WHERE user_id = :uid
    ORDER BY fecha_envio DESC
    LIMIT 1'
);
$stmt->execute(['uid' => $_SESSION['id_usuario']]);
$estado = $stmt->fetchColumn();

// 3) Redirijo si cambió
if ($estado === 'aceptado') {
    header('Location: frontend.php');
    exit;
}
if ($estado === 'rechazado') {
    header('Location: rechazado.php');
    exit;
}

// 4) Si sigue pendiente, muestro la página
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>En espera de revisión</title>
  <meta http-equiv="refresh" content="30"> <!-- recarga cada 30 s -->
  <link rel="stylesheet" href="holis.css">
</head>
<body>
  <div id="login">
    <h2>Tu solicitud está en revisión</h2>
    <p>Gracias por postularte. En breve recibirás la respuesta.</p>
    <p>Esta página se actualizará automáticamente.</p>
    <p><a href="landingpage.html">Cerrar sesión</a></p>
  </div>
</body>
</html>
