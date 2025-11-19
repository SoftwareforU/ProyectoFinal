<?php
session_start();
require '../conexiones.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query(
  'SELECT f.id,
          u.Usuarios AS usuario,
          f.nombre_completo,
          f.direccion,
          f.telefono,
          f.integrantes_familia,
          f.tiene_mascotas,
          f.fecha_envio
     FROM formularios f
     JOIN Usuario u ON f.user_id = u.IdUsuario
    WHERE f.estado = "pendiente"
    ORDER BY f.fecha_envio DESC'
);
$formularios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Backoffice – Formularios</title>
  <link rel="stylesheet" href="../holis.css">
</head>
<body>
  <div id="login">
    <h2>Formularios Pendientes</h2>
    <p><a href="index.php">← Volver al panel</a></p>
    <?php if (empty($formularios)): ?>
      <p>No hay formularios pendientes.</p>
    <?php else: ?>
      <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
          <th>ID</th><th>Usuario</th><th>Nombre Completo</th>
          <th>Dirección</th><th>Teléfono</th><th>Familiares</th>
          <th>Mascotas</th><th>Fecha</th><th>Acciones</th>
        </tr>
        <?php foreach ($formularios as $f): ?>
        <tr>
          <td><?= $f['id'] ?></td>
          <td><?= htmlspecialchars($f['usuario']) ?></td>
          <td><?= htmlspecialchars($f['nombre_completo']) ?></td>
          <td><?= htmlspecialchars($f['direccion']) ?></td>
          <td><?= htmlspecialchars($f['telefono']) ?></td>
          <td><?= $f['integrantes_familia'] ?></td>
          <td><?= $f['tiene_mascotas'] ? 'Sí' : 'No' ?></td>
          <td><?= $f['fecha_envio'] ?></td>
          <td>
            <a href="handle_form.php?id=<?= $f['id'] ?>&action=accept">
              Aceptar
            </a> |
            <a href="handle_form.php?id=<?= $f['id'] ?>&action=reject">
              Rechazar
            </a>
          </td>
        </tr>
        <?php endforeach ?>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>