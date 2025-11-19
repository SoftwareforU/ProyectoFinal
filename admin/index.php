<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Backoffice – Panel</title>
  <link rel="stylesheet" href="../holis.css">
</head>
<body>
  <div id="login">
    <h2>Panel de Administración</h2>
    <nav>
      <ul style="list-style:none; padding:0;">
        <li><a href="usuarios.php">Registros de Usuarios</a></li>
        <li><a href="formularios.php">Formularios de Postulación</a></li>
        <li><a href="ver_horas.php">Ver Horas Registradas</a></li>
        <li><a href="ver_comprobantes.php">Ver Comprobantes Registrados</a></li>
        <li><a href="viviendas_admin.php">Gestión de Viviendas</a></li>
        <li><a href="admin_comunicaciones.php">Gestión de Comunicaciones</a></li>
        <li><a href="../landingpage.html">Cerrar sesión</a></li>
      </ul>
    </nav>
  </div>
</body>
</html>
