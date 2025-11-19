<?php
session_start();
require '../conexiones.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query(
  'SELECT IdUsuario, Usuarios, role
     FROM Usuario
    ORDER BY IdUsuario'
);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Backoffice – Usuarios</title>
  <link rel="stylesheet" href="../holis.css">
</head>
<body>
  <div id="login">
    <h2>Registros de Usuarios</h2>
    <p><a href="index.php">← Volver al panel</a></p>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
      <tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Acciones</th></tr>
      <?php foreach ($usuarios as $u): ?>
      <tr>
        <td><?= $u['IdUsuario'] ?></td>
        <td><?= htmlspecialchars($u['Usuarios']) ?></td>
        <td><?= $u['role'] ?></td>
        <td>
          <a href="editar_usuario.php?id=<?= $u['IdUsuario'] ?>">Editar</a>
          |
          <a href="delete_user.php?id=<?= $u['IdUsuario'] ?>"
             onclick="return confirm('¿Eliminar usuario?')">Borrar</a>
        </td>
      </tr>
      <?php endforeach ?>
    </table>
  </div>
</body>
</html>