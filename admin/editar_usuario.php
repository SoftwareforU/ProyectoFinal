<?php
session_start();
require '../conexiones.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT IdUsuario, Usuarios, role FROM Usuario WHERE IdUsuario = ?');
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoUsuario = trim($_POST['Usuarios']);
    $nuevoRol = trim($_POST['role']);
    $stmt = $pdo->prepare('UPDATE Usuario SET Usuarios = ?, role = ? WHERE IdUsuario = ?');
    $stmt->execute([$nuevoUsuario, $nuevoRol, $id]);
    header('Location: usuarios.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuario</title>
  <link rel="stylesheet" href="../holis.css">
</head>
<body>
  <div id="login">
    <h2>Editar Usuario</h2>
    <form method="post">
      <label>Usuario:
        <input type="text" name="Usuarios" value="<?= htmlspecialchars($usuario['Usuarios']) ?>" required>
      </label>
      <label>Rol:
        <select name="role" required>
          <option value="admin" <?= $usuario['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
          <option value="user" <?= $usuario['role'] === 'user' ? 'selected' : '' ?>>user</option>
        </select>
      </label>
      <button type="submit">Guardar cambios</button>
    </form>
    <p><a href="usuarios.php" class="btn-caja">← Volver</a></p>
  </div>
</body>
</html>