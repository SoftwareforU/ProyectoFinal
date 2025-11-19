<?php
session_start();
require 'conexiones.php';

$errores  = [];
$usuario  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['username'] ?? '');
    $clave    = $_POST['password']     ?? '';

    if ($usuario === '' || $clave === '') {
        $errores[] = 'Completa todos los campos.';
    } else {
        // Traigo IdUsuario, contrasena, role y aplico
        $stmt = $pdo->prepare(
          'SELECT IdUsuario, contrasena, role, aplico
             FROM usuario
            WHERE Usuarios = :u'
        );
        $stmt->execute(['u' => $usuario]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fila && password_verify($clave, $fila['contrasena'])) {
            // Guardar en sesión
            $_SESSION['id_usuario'] = $fila['IdUsuario'];
            $_SESSION['Usuarios']   = $usuario;
            $_SESSION['role']       = $fila['role'];
            $_SESSION['usuario']    = $usuario; // <-- AGREGA ESTA LÍNEA

            if ($fila['role'] === 'usuario' && $fila['aplico'] == 1) {
                // Obtengo el estado más reciente
                $stmt2 = $pdo->prepare(
                  'SELECT estado
                  FROM formularios
                  WHERE user_id = :uid
                  ORDER BY fecha_envio DESC
                LIMIT 1'
                );
                $stmt2->execute(['uid' => $fila['IdUsuario']]);
                $estado = $stmt2->fetchColumn();

                // Redirijo según estado
                if ($estado === 'pendiente') {
                    header('Location: espera.php');
                    exit;
                }
                if ($estado === 'aceptado') {
                    header('Location: frontend.php');
                    exit;
                }
                // rechazado
                header('Location: rechazado.php');
                exit;
            }

            // Si es usuario normal y no ha aplicado, va a apply.php
            if ($fila['role'] === 'usuario' && $fila['aplico'] == 0) {
                header('Location: apply.php');
                exit;
            }

            // Si es admin → backoffice; si no, frontend
            if ($fila['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: frontend.php');
            }
            exit;
        }

        $errores[] = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="holis.css">
</head>
<body>
  <div id="login">
    <h2>Iniciar sesión</h2>

    <?php if (!empty($errores)): ?>
      <ul style="text-align:left; list-style:none; padding:0; margin:0 0 10px;">
        <?php foreach ($errores as $e): ?>
          <li style="color:red;"><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form action="login.php" method="post">
      <p>
        <input
          type="text"
          name="username"
          placeholder="Usuario"
          value="<?= htmlspecialchars($usuario) ?>"
          required
        >
      </p>
      <p>
        <input
          type="password"
          name="password"
          placeholder="Contraseña"
          required
        >
      </p>
      <p><button type="submit">Entrar</button></p>
    </form>

    <p>
      ¿No tienes cuenta?
      <a href="registro.php" style="color:#184c99; text-decoration:none;">
        Regístrate
      </a>
    </p>
  </div>
</body>
</html>

