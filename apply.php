<?php
session_start();
require 'conexiones.php';

$errores            = [];
$nombreCompleto     = $_POST['nombre_completo']    ?? '';
$direccion          = $_POST['direccion']          ?? '';
$telefono           = $_POST['telefono']           ?? '';
$integrantesFamilia = $_POST['integrantes_familia'] ?? '';
$tieneMascotas      = $_POST['tiene_mascotas']     ?? '0';

// Protejo acceso
if (empty($_SESSION['id_usuario']) || $_SESSION['role'] === 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validaciones...
    if (empty($errores)) {
        // INSERT con 6 columnas y 6 valores
        $sql = '
          INSERT INTO formularios
            (user_id,
             nombre_completo,
             direccion,
             telefono,
             integrantes_familia,
             tiene_mascotas)
          VALUES
            (:uid,
             :nc,
             :dir,
             :tel,
             :int,
             :mas)
        ';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'uid' => $_SESSION['id_usuario'],
          'nc'  => $nombreCompleto,
          'dir' => $direccion,
          'tel' => $telefono,
          'int' => (int)$integrantesFamilia,
          'mas' => (int)$tieneMascotas
        ]);

        // Marco que ya aplicó
        $stmt = $pdo->prepare(
          'UPDATE Usuario
              SET aplico = 1
            WHERE IdUsuario = :uid'
        );
        $stmt->execute(['uid' => $_SESSION['id_usuario']]);

        header('Location: espera.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario de Postulación</title>
  <link rel="stylesheet" href="holis.css">
</head>
<body>
  <div id="login">
    <h2>Formulario de Postulación</h2>

    <?php if (!empty($errores)): ?>
      <ul style="text-align:left; list-style:none; padding:0; margin:0 0 10px;">
        <?php foreach ($errores as $e): ?>
          <li style="color:red;"><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form action="apply.php" method="post">
      <p>
        <input
          type="text"
          name="nombre_completo"
          placeholder="Nombre completo"
          value="<?= htmlspecialchars($nombreCompleto) ?>"
          required
          style="width:100%;"
        >
      </p>
      <p>
        <input
          type="text"
          name="direccion"
          placeholder="Dirección"
          value="<?= htmlspecialchars($direccion) ?>"
          required
          style="width:100%;"
        >
      </p>
      <p>
        <input
          type="tel"
          name="telefono"
          placeholder="Número de teléfono"
          value="<?= htmlspecialchars($telefono) ?>"
          required
          style="width:100%;"
        >
      </p>
      <p>
        <input
          type="number"
          name="integrantes_familia"
          placeholder="Integrantes de la familia"
          value="<?= htmlspecialchars($integrantesFamilia) ?>"
          required
          min="1"
          style="width:100%;"
        >
      </p>
      <p>
        <label>
          <input
            type="radio"
            name="tiene_mascotas"
            value="1"
            <?= $tieneMascotas === '1' ? 'checked' : '' ?>
          >
          Sí tengo mascotas
        </label><br>
        <label>
          <input
            type="radio"
            name="tiene_mascotas"
            value="0"
            <?= $tieneMascotas === '0' ? 'checked' : '' ?>
          >
          No tengo mascotas
        </label>
      </p>
      <p><button type="submit">Enviar Postulación</button></p>
    </form>
  </div>
</body>
</html>
