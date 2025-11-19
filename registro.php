<?php
session_start();
require 'conexiones.php'; // instancia $pdo a la BD "coope"

$errors           = [];
$username         = '';
$nombre           = '';
$apellido         = '';
$email            = '';
$ci               = '';
$password         = '';
$confirm_password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Recoge y sanea
    $username         = trim($_POST['username']         ?? '');
    $nombre           = trim($_POST['nombre']           ?? '');
    $apellido         = trim($_POST['apellido']         ?? '');
    $email            = trim($_POST['email']            ?? '');
    $ci               = trim($_POST['ci']               ?? '');
    $password         = $_POST['password']              ?? '';
    $confirm_password = $_POST['confirm_password']      ?? '';

    // 2) Validaciones
    if (
        $username === '' || $nombre === '' || $apellido === '' ||
        $email === ''    || $ci === ''     || $password === '' ||
        $confirm_password === ''
    ) {
        $errors[] = 'Completa todos los campos.';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Las contraseñas no coinciden.';
    } else {
        // 3) Comprueba unicidad de username y CI
        $stmt = $pdo->prepare('SELECT 1 FROM Usuario WHERE Usuarios = :u');
        $stmt->execute(['u' => $username]);
        if ($stmt->fetch()) {
            $errors[] = 'El nombre de usuario ya está en uso.';
        }

        $stmt = $pdo->prepare('SELECT 1 FROM Miembro WHERE CI = :ci');
        $stmt->execute(['ci' => $ci]);
        if ($stmt->fetch()) {
            $errors[] = 'La Cédula ya está registrada.';
        }
    }

    // 4) Inserta si no hay errores
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // a) Inserta en Miembro
        $stmt = $pdo->prepare(
          'INSERT INTO Miembro
             (CI, Nombre, Apellido, Edad, Ingresos, Correo, FechIngre, Rol)
           VALUES
             (:ci, :n, :a, 0, 0.00, :email, CURDATE(), "miembro")'
        );
        $stmt->execute([
          'ci'    => $ci,
          'n'     => $nombre,
          'a'     => $apellido,
          'email' => $email
        ]);

        // b) Inserta en Usuario (ahora también guarda la CI)
        $stmt = $pdo->prepare(
          'INSERT INTO Usuario (Usuarios, contrasena, CI)
           VALUES (:u, :p, :ci)'
        );
        $stmt->execute([
          'u'  => $username,
          'p'  => $hash,
          'ci' => $ci
        ]);

        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <link rel="stylesheet" href="holis.css">
</head>
<body>
  <div id="login">
    <h2>Crear cuenta</h2>

    <?php if ($errors): ?>
      <ul style="text-align:left; list-style:none; padding:0; margin:0 0 10px;">
        <?php foreach ($errors as $e): ?>
          <li style="color:red;"><?= htmlspecialchars($e) ?></li>
        <?php endforeach ?>
      </ul>
    <?php endif ?>

    <form action="registro.php" method="post">
      <p><input type="text"    name="username"         placeholder="Usuario"
                value="<?= htmlspecialchars($username) ?>" required></p>
      <p><input type="text"    name="nombre"           placeholder="Nombre"
                value="<?= htmlspecialchars($nombre) ?>"   required></p>
      <p><input type="text"    name="apellido"         placeholder="Apellido"
                value="<?= htmlspecialchars($apellido) ?>" required></p>
      <p><input type="email"   name="email"            placeholder="Email"
                value="<?= htmlspecialchars($email) ?>"    required></p>
      <p><input type="text"    name="ci"               placeholder="Cédula (CI)"
                value="<?= htmlspecialchars($ci) ?>"       maxlength="8" required></p>
      <p><input type="password" name="password"        placeholder="Contraseña" required></p>
      <p><input type="password" name="confirm_password" placeholder="Repetir contraseña" required></p>
      <p><button type="submit">Registrarme</button></p>
    </form>

    <p><a href="login.php" style="color:#184c99; text-decoration:none;">
      ¿Ya tienes cuenta? Inicia sesión
    </a></p>
  </div>
</body>
</html>
