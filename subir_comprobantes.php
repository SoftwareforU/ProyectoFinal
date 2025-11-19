<?php
session_start();
require 'conexiones.php';

// 1) Solo sesión iniciada
if (empty($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

// 2) Verifica estado de postulación
$stmt = $pdo->prepare(
  'SELECT estado
     FROM formularios
    WHERE user_id = :uid
    ORDER BY fecha_envio DESC
    LIMIT 1'
);
$stmt->execute(['uid' => $_SESSION['id_usuario']]);
$estado = $stmt->fetchColumn();

if ($estado === 'pendiente') {
    header('Location: espera.php'); exit;
}
if ($estado === 'rechazado') {
    header('Location: rechazado.php'); exit;
}

// 3) Variables iniciales
$errores    = [];
$fchPago    = $_POST['fch_pago']    ?? '';
$monto      = $_POST['monto']       ?? '';
$metodo     = $_POST['metodo']      ?? '';
$id_fondo   = $_POST['id_fondo']    ?? '';
$archivo    = $_FILES['comprobante'] ?? null;
$estadoPago = 'pendiente';

// Procesar acciones separadas: enviar (subir archivo) / registrar (guardar en BD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ACCIÓN: Enviar comprobante (solo subir archivo)
    if (isset($_POST['enviar_comprobante'])) {
        if (!$archivo || empty($archivo['name'])) {
            $errores[] = 'Adjunta un comprobante (PDF/JPG/PNG) antes de enviar.';
        } else {
            $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf','jpg','jpeg','png'], true)) {
                $errores[] = 'Formato no válido. Solo PDF, JPG o PNG.';
            } else {
                $dir = 'uploads/comprobantes/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $nombre = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($archivo['name']));
                $ruta   = $dir . $nombre;
                if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
                    $errores[] = 'Error al subir el comprobante.';
                } else {
                    // Guardar ruta en sesión para usarla luego al registrar
                    $_SESSION['uploaded_comprobante'] = $ruta;
                    $exito_enviar = 'Comprobante subido correctamente. Ahora puedes registrar el pago.';
                }
            }
        }
    }

    // ACCIÓN: Registrar comprobante (guardar/actualizar registro en BD)
    if (isset($_POST['registrar_comprobante'])) {
        // Validaciones básicas
        if ($fchPago === '') $errores[] = 'Selecciona la fecha de pago.';
        if (!is_numeric($monto) || $monto <= 0) $errores[] = 'Ingresa un monto válido.';
        if (trim($metodo) === '') $errores[] = 'Indica el método de pago.';
        if ($id_fondo === '') $errores[] = 'Selecciona un fondo.';

        // Determinar ruta del comprobante: preferir la subida previa en sesión
        $ruta = $_SESSION['uploaded_comprobante'] ?? '';
        // Si no hay ruta en sesión, permitir que el usuario suba y registre en la misma acción
        if (empty($ruta) && $archivo && !empty($archivo['name'])) {
            $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf','jpg','jpeg','png'], true)) {
                $errores[] = 'Formato no válido del comprobante. Solo PDF, JPG o PNG.';
            } else {
                $dir = 'uploads/comprobantes/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $nombre = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($archivo['name']));
                $ruta   = $dir . $nombre;
                if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
                    $errores[] = 'Error al subir el comprobante.';
                    $ruta = '';
                }
            }
        }

        if (empty($ruta)) $errores[] = 'No existe un comprobante subido. Primero presiona "Enviar comprobante".';

        // Si no hay errores, insertar o actualizar
        if (empty($errores)) {
            // Verificar si ya existe registro para este usuario + fondo
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM miembrofondo WHERE IdUsuario = :uid AND IdFondo = :idfondo');
            $checkStmt->execute(['uid' => $_SESSION['id_usuario'], 'idfondo' => $id_fondo]);
            $existe = $checkStmt->fetchColumn() > 0;

            if ($existe) {
                $sql = '
                  UPDATE miembrofondo
                    SET FchPago = :fch, MontoC = :mto, Metodo = :met, EstadoPago = :est, Archivo = :arc
                  WHERE IdUsuario = :uid AND IdFondo = :idfondo
                ';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                  'uid'     => $_SESSION['id_usuario'],
                  'idfondo' => $id_fondo,
                  'fch'     => $fchPago,
                  'mto'     => $monto,
                  'met'     => $metodo,
                  'est'     => $estadoPago,
                  'arc'     => $ruta
                ]);
            } else {
                $sql = '
                  INSERT INTO miembrofondo
                    (IdUsuario, IdFondo, FchPago, MontoC, Metodo, EstadoPago, Archivo)
                  VALUES
                    (:uid, :idfondo, :fch, :mto, :met, :est, :arc)
                ';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                  'uid'     => $_SESSION['id_usuario'],
                  'idfondo' => $id_fondo,
                  'fch'     => $fchPago,
                  'mto'     => $monto,
                  'met'     => $metodo,
                  'est'     => $estadoPago,
                  'arc'     => $ruta
                ]);
            }

            // limpiar comprobante temporal de sesión
            unset($_SESSION['uploaded_comprobante']);
            header('Location: subir_comprobantes.php?ok=1');
            exit;
        }
    }
}

// Obtener fondos disponibles
$fondos = $pdo->query('SELECT IdFondo, Tipo FROM fondo')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Subir comprobante</title>
  <link rel="stylesheet" href="holis.css">
</head>
<body>
   <div id="Barra">
        <h1> COVIUNIDOS </h1>     

        <button id="open-menu">☰ </button>

        <div id="side-menu">
            <button id="menu-toggle">✖ </button>
            <div id="text-menu">
                <ul>
                    <li><a href="frontend.php">Inicio</a></li>
                    <li><a href="registro_horas.php">Registrar horas</a></li>
                    <li><a href="subir_comprobantes.php">Subir Comprobantes</a></li>
                    <li><a href="comunicacion.php">Comunicación</a></li>
                    <li><a href="perfil.php">Perfil</a></li>
                </ul>
            </div>
            <div id="LogOutBox">
                <a href="landingpage.html" style="text-decoration: none; color: inherit;">
                    <p class="LogOut"><b>Cerrar Sesión</b></p>
                </a>
            </div>
        </div>

        <script src="script.js"></script>
  </div> <!-- cierre de #Barra -->

<!-- Centra el box de comprobantes debajo de la barra -->
<div style="display:flex; justify-content:center; align-items:center; min-height:80vh;">
  <div id="login">
    <h2>Subir comprobante</h2>

    <a href="frontend.php">← Volver al inicio</a><br>

    <?php if (!empty($errores)): ?>
      <ul style="color:red;">
        <?php foreach ($errores as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <p>
        <label>Fondo:</label><br>
        <select name="id_fondo" required style="width:100%;">
          <option value="">Selecciona un fondo</option>
          <option value="1">Fondo para la cooperativa</option>
          <?php foreach ($fondos as $f): ?>
            <option value="<?= $f['IdFondo'] ?>"><?= htmlspecialchars($f['Tipo']) ?></option>
          <?php endforeach; ?>
        </select>
      </p>

      <p>
        <label>Fecha de pago:</label><br>
        <input type="date" name="fch_pago" value="<?= htmlspecialchars($fchPago) ?>" required style="width:100%;">
      </p>

      <p>
        <label>Monto:</label><br>
        <input type="number" name="monto" step="0.01" value="<?= htmlspecialchars($monto) ?>" required style="width:100%;">
      </p>

      <p>
        <label>Método:</label><br>
        <input type="text" name="metodo" value="<?= htmlspecialchars($metodo) ?>" required style="width:100%;">
      </p>

      <p>
        <label>Comprobante (PDF/JPG/PNG):</label><br>
        <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" required>
      </p>

      <p>
        <button type="submit" name="enviar_comprobante" style="padding:10px 16px;">Enviar comprobante</button>
      </p>
    </form>
  </div>
</div>
</body>
</html>
