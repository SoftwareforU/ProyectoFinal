<?php
session_start();
require '../conexiones.php';

// 1) Verificar que el usuario es administrador
if (empty($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// 2) Consultar todas las contribuciones
$sql = '
  SELECT u.IdUsuario, c.FchTrabajo, c.HsTrabaj, c.ValorContri
    FROM contribucion c
    JOIN Usuario u ON c.IdUsuario = u.IdUsuario
   ORDER BY c.FchTrabajo DESC
';
$stmt = $pdo->query($sql);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Horas registradas</title>
  <link rel="stylesheet" href="../holis.css">
</head>
<body>
  <div id="login">
    <h2>Horas registradas por los miembros</h2>

    <?php if (empty($registros)): ?>
      <p>No hay horas registradas aún.</p>
    <?php else: ?>
      <table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse:collapse;">
        <thead>
          <tr style="background:#eee;">
            <th>Nombre</th>
            <th>Fecha de trabajo</th>
            <th>Horas</th>
            <th>Valor estimado</th>
          </tr>
        </thead>
        <tbody>
          <p><a href="index.php">← Volver al panel</a></p>
          <?php foreach ($registros as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['IdUsuario']) ?></td>
              <td><?= htmlspecialchars($r['FchTrabajo']) ?></td>
              <td><?= htmlspecialchars($r['HsTrabaj']) ?></td>
              <td>$<?= number_format($r['ValorContri'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
