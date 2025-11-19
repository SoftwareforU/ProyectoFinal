<?php
session_start();
require '../conexiones.php';

// 1) Verificar que el usuario es administrador
if (empty($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// 2) Procesar cambio de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ci'], $_POST['nuevo_estado'])) {
    $ci = $_POST['ci'];
    $nuevo_estado = $_POST['nuevo_estado'];
    
    $stmt = $pdo->prepare('UPDATE miembrofondo SET EstadoPago = :estado WHERE CI = :ci');
    $stmt->execute([
        'estado' => $nuevo_estado,
        'ci'     => $ci
    ]);
    
    header('Location: ver_comprobantes.php');
    exit;
}

// 3) Consultar todos los comprobantes
$sql = "SELECT u.Usuarios, m.CI, m.FchPago, m.MontoC, m.Metodo, m.EstadoPago, m.Archivo
        FROM miembrofondo m
        JOIN usuario u ON m.IdUsuario = u.IdUsuario
        ORDER BY m.FchPago DESC";
$stmt = $pdo->query($sql);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comprobantes de pago</title>
  <link rel="stylesheet" href="../holis.css">
</head>
<body>
  <div id="login">
    <h2>Comprobantes de pago registrados por los miembros</h2>

    <?php if (empty($registros)): ?>
      <p>No hay comprobantes registrados aún.</p>
    <?php else: ?>
      <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>Usuario</th>
            <th>CI</th>
            <th>Fecha de pago</th>
            <th>Monto</th>
            <th>Método</th>
            <th>Estado</th>
            <th>Comprobante</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($registros as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['Usuarios']) ?></td>
              <td><?= htmlspecialchars($r['CI']) ?></td>
              <td><?= htmlspecialchars($r['FchPago']) ?></td>
              <td><?= htmlspecialchars($r['MontoC']) ?></td>
              <td><?= htmlspecialchars($r['Metodo']) ?></td>
              <td><?= htmlspecialchars($r['EstadoPago']) ?></td>
              <td>
                <?php if (!empty($r['Archivo'])): ?>
                    <a href="../<?= htmlspecialchars($r['Archivo']) ?>" target="_blank">Ver comprobante</a>
                    <?php if (!file_exists('../' . $r['Archivo'])): ?>
                        <br><em>(Archivo no encontrado)</em>
                    <?php endif; ?>
                <?php else: ?>
                    <em>No disponible</em>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
    
    <br>
    <p><a href="index.php">← Volver al panel</a></p>
  </div>
</body>
</html>