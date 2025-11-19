<?php
// admin_comunicaciones.php
declare(strict_types=1);
session_start();
require __DIR__ . '/../conexiones.php'; // ruta correcta a conexiones.php

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}


$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$mensaje = '';

try {
    if ($accion === 'responder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $respuesta = trim((string)($_POST['respuesta'] ?? ''));
        $estado = in_array($_POST['estado'] ?? '', ['Abierta','Respondida','Cerrada']) ? $_POST['estado'] : 'Respondida';
        if ($id > 0 && $respuesta !== '') {
            $stmt = $pdo->prepare('UPDATE comunicaciones 
                                      SET Respuesta = :resp, Estado = :estado, UsuarioAdmin = :admin, FechaRespuesta = NOW() 
                                    WHERE IdComunicacion = :id');
            $stmt->execute([
                ':resp'=>$respuesta,
                ':estado'=>$estado,
                ':admin'=>$_SESSION['nombre'] ?? 'admin',
                ':id'=>$id
            ]);
            $mensaje = 'Respuesta guardada.';
        }
    } elseif ($accion === 'borrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM comunicaciones WHERE IdComunicacion = :id');
            $stmt->execute([':id'=>$id]);
            $mensaje = 'Comunicación eliminada.';
        }
    }
} catch (PDOException $e) {
    $mensaje = 'Error en operación.';
}

// Listado con JOIN a usuario
$sqlList = "SELECT c.*, u.Usuarios AS UsuarioNombre
              FROM comunicaciones c
         LEFT JOIN usuario u ON u.IdUsuario = c.IdUsuario
          ORDER BY c.FechaCreacion DESC";
$stmtList = $pdo->query($sqlList);
$comunicaciones = $stmtList->fetchAll();

// Ver detalle
$idVer = isset($_GET['ver']) ? (int)$_GET['ver'] : 0;
$idResponder = isset($_GET['responder']) ? (int)$_GET['responder'] : 0;
$detalle = null;
if ($idVer > 0 || $idResponder > 0) {
    $idDetalle = $idVer > 0 ? $idVer : $idResponder;
    $stmt = $pdo->prepare("SELECT c.*, u.Usuarios AS UsuarioNombre
                             FROM comunicaciones c
                        LEFT JOIN usuario u ON u.IdUsuario = c.IdUsuario
                            WHERE c.IdComunicacion = :id LIMIT 1");
    $stmt->execute([':id'=>$idDetalle]);
    $detalle = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="admin2.css">
<title>Admin comunicaciones</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;max-width:1000px;margin:16px auto;padding:10px}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:8px;border:1px solid #eee;text-align:left}
textarea{width:100%;min-height:100px}
button{padding:6px 10px}
</style>
</head>
<body>
<div class="admin-center-box">
<h2>Panel de comunicaciones</h2>
<?php if ($mensaje): ?><div style="color:green"><?=htmlspecialchars($mensaje)?></div><?php endif; ?>

<table>
<thead>
  <tr>
    <th>ID</th>
    <th>Fecha</th>
    <th>Usuario</th>
    <th>Tipo</th>
    <th>Título</th>
    <th>Estado</th>
    <th>Acciones</th>
  </tr>
</thead>
<tbody>
<?php foreach($comunicaciones as $c): ?>
<tr>
  <td><?= $c['IdComunicacion'] ?></td>
  <td><?= htmlspecialchars($c['FechaCreacion']) ?></td>
  <td><?= htmlspecialchars($c['UsuarioNombre'] ?? 'N/D') ?></td>
  <td><?= htmlspecialchars($c['Tipo']) ?></td>
  <td><?= htmlspecialchars($c['Titulo']) ?></td>
  <td><?= htmlspecialchars($c['Estado']) ?></td>
  <td>
    <a href="?ver=<?= $c['IdComunicacion'] ?>" class="btn-caja" style="padding:6px 18px;font-size:1rem;">Ver</a>
    <?php if($c['Estado'] === 'pendiente'): ?>
      <a href="?responder=<?= $c['IdComunicacion'] ?>" class="btn-caja" style="padding:6px 18px;font-size:1rem;">Responder</a>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php if ($detalle): ?>
<hr>
<h3>Comunicación #<?= $detalle['IdComunicacion'] ?> — <?= htmlspecialchars($detalle['Titulo']) ?></h3>
<p>
  Usuario: <?=htmlspecialchars($detalle['UsuarioNombre'] ?? 'N/D')?> •
  Tipo: <?=htmlspecialchars($detalle['Tipo'])?> •
  Fecha: <?=htmlspecialchars($detalle['FechaCreacion'])?>
</p>

<h4>Mensaje</h4>
<div style="white-space:pre-wrap;border:1px solid #ccc;padding:8px;background:#fff;"><?=htmlspecialchars($detalle['Mensaje'])?></div>

<h4>Respuesta</h4>
<?php if ($detalle['Respuesta']): ?>
  <div style="white-space:pre-wrap;border:1px solid #ccc;padding:8px;background:#fff;"><?=htmlspecialchars($detalle['Respuesta'])?></div>
  <p>Respondida por <?=htmlspecialchars($detalle['UsuarioAdmin'] ?? '')?> el <?=htmlspecialchars($detalle['FechaRespuesta'] ?? '')?></p>
<?php elseif ($idResponder > 0): ?>
  <form method="post">
    <input type="hidden" name="accion" value="responder">
    <input type="hidden" name="id" value="<?= $detalle['IdComunicacion'] ?>">
    <label>Estado:
      <select name="estado">
        <option value="Respondida">Respondida</option>
        <option value="Cerrada">Cerrada</option>
        <option value="Abierta">Abierta</option>
      </select>
    </label>
    <label>Respuesta:
      <textarea name="respuesta" required></textarea>
    </label>
    <button type="submit">Guardar respuesta</button>
  </form>
<?php else: ?>
  <p>Sin respuesta aún.</p>
  <a href="?responder=<?= $detalle['IdComunicacion'] ?>" class="btn-caja" style="padding:6px 18px;font-size:1rem;">Responder</a>
<?php endif; ?>
<?php endif; ?>

<div class="volver-panel">
  <a href="index.php" class="btn-caja" style="padding:6px 18px;font-size:1rem;">← Volver al panel</a>
</div>
</div>
</body>
</html>
