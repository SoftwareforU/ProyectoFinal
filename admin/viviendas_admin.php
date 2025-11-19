<?php
session_start();
require '../conexiones.php'; // $pdo

// Verificar admin
if (empty($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

// Token CSRF simple
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$token = $_SESSION['csrf_token'];

// Eliminar vivienda (GET con token)
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    if (!isset($_GET['token']) || $_GET['token'] !== $token) die('Token inválido.');
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM vivienda WHERE IdVivienda = :id');
    $stmt->execute(['id' => $id]);
    header('Location: viviendas_admin.php?msg=deleted'); exit;
}

// Obtener viviendas con info del miembro asignado
$sql = '
  SELECT v.IdVivienda, v.UbicInterna, v.NumPuerta, v.Estado, v.CI, m.Nombre
    FROM vivienda v
    LEFT JOIN miembro m ON v.CI = m.CI
   ORDER BY v.IdVivienda DESC
';
$stmt = $pdo->query($sql);
$viviendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<link rel="stylesheet" href="admin2.css">
<head><meta charset="utf-8"><title>Admin - Viviendas</title></head>

<body>
  <div id="Titulo">
  <h10>Administración de Viviendas</h10>
  </div>
  <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <p style="color:green;">Vivienda eliminada correctamente.</p>
  <?php endif; ?>

  <?php if (empty($viviendas)): ?>
    <p>No hay viviendas registradas.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Id</th>
          <th>Ubicación</th>
          <th>Nº Puerta</th>
          <th>Estado</th>
          <th>CI asignada</th>
          <th style="text-align:center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($viviendas as $v): ?>
          <tr>
            <td><?= htmlspecialchars($v['IdVivienda']) ?></td>
            <td><?= htmlspecialchars($v['UbicInterna']) ?></td>
            <td><?= htmlspecialchars($v['NumPuerta']) ?></td>
            <td><?= htmlspecialchars($v['Estado']) ?></td>
            <td><?= htmlspecialchars($v['CI']) ?></td>
            <td style="text-align:center;">
              <a href="../vivienda_form.php?id=<?= $v['IdVivienda'] ?>" class="btn-caja" style="padding:6px 18px;font-size:1rem;display:inline-block;">Editar</a>
              <a href="viviendas_admin.php?action=delete&id=<?= $v['IdVivienda'] ?>&token=<?= $token ?>"
                 onclick="return confirm('Confirmar eliminación?')" class="btn-caja" style="padding:6px 18px;font-size:1rem;display:inline-block;">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

   <div id="botones">
    <a href="../vivienda_form.php" class="btn-caja">Crear vivienda</a>
    <a href="index.php" class="btn-caja">← Volver al panel</a>
  </div>
</body>
</html>
