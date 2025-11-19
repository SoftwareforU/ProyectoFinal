<?php
session_start();
require 'conexiones.php';

// 1) Asegurar que haya sesión
if (empty($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

// 2) Consultar último estado de postulación
$stmt = $pdo->prepare(
  'SELECT estado
     FROM formularios
    WHERE user_id = :uid
    ORDER BY fecha_envio DESC
    LIMIT 1'
);
$stmt->execute(['uid' => $_SESSION['id_usuario']]);
$estado = $stmt->fetchColumn();

// 3) Redirigir según estado
if ($estado === 'pendiente') {
    header('Location: espera.php');
    exit;
}
if ($estado === 'rechazado') {
    header('Location: rechazado.php');
    exit;
}

// 4) Inicializar variables
$errores     = [];
$fecha       = $_POST['FchTrabajo']      ?? '';
$horas       = $_POST['HsTrabaj']        ?? '';
$valor_hora  = 120; // Valor por hora
$valor       = 0;
$registro_existente = null;

// 5) Verificar si existe registro previo
$check = $pdo->prepare('SELECT * FROM contribucion WHERE IdUsuario = :uid');
$check->execute(['uid' => $_SESSION['id_usuario']]);
$registro_existente = $check->fetch(PDO::FETCH_ASSOC);

// 6) Procesar envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($fecha === '') {
        $errores[] = 'Selecciona la fecha de trabajo.';
    }
    if (!is_numeric($horas) || $horas <= 0 || $horas > 12) {
        $errores[] = 'Ingresa un número de horas válido (máximo 12).';
    }

    if (empty($errores)) {
        // Calcular valor: horas × 120
        $valor = $horas * $valor_hora;

        if ($registro_existente) {
            // Actualizar registro existente
            $stmt = $pdo->prepare(
              'UPDATE contribucion
                 SET FchTrabajo = :fch, HsTrabaj = :hrs, ValorContri = :vlr
               WHERE IdUsuario = :uid'
            );
        } else {
            // Insertar nuevo registro (sin IdUsuario, que es auto_increment)
            $stmt = $pdo->prepare(
              'INSERT INTO contribucion
                 (IdUsuario, CI, FchTrabajo, HsTrabaj, ValorContri)
               VALUES
                 (:uid, :fch, :hrs, :vlr)'
            );
        }
        
        $stmt->execute([
          'uid'  => $_SESSION['id_usuario'],
          'fch'  => $fecha,
          'hrs'  => $horas,
          'vlr'  => $valor,
        ]);

        header('Location: registro_horas.php?ok=1');
        exit;
    }
}

// Calcular valor mostrado en el formulario si hay horas
$valor_mostrado = ($horas && is_numeric($horas)) ? $horas * $valor_hora : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Horas</title>
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
  </div>

  <div id="login">
    <h2>Registro de Horas Trabajadas</h2>

    <a href="frontend.php">← Volver al inicio</a><br>

    <?php if (!empty($_GET['ok'])): ?>
      <p style="color:green;">Horas registradas con éxito.</p>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
      <ul style="color:red; list-style:none; padding:0;">
        <?php foreach ($errores as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form action="registro_horas.php" method="post">
      <p>
        <label>Fecha de trabajo:</label><br>
        <input
          type="date"
          name="FchTrabajo"
          value="<?= htmlspecialchars($fecha) ?>"
          required
          style="width:100%;"
        >
      </p>
      <p>
        <label>Horas trabajadas:</label><br>
        <input
          type="number"
          name="HsTrabaj"
          id="HsTrabaj"
          step="0.25"
          min="0.25"
          max="12"
          value="<?= htmlspecialchars($horas) ?>"
          required
          style="width:100%;"
        >
      </p>
      <p>
        <label>Valor estimado (automático):</label><br>
        <input
          type="text"
          id="ValorContri"
          value="$<?= number_format($valor_mostrado, 2) ?>"
          disabled
          style="width:100%; background:#f0f0f0;"
        >
      </p>
      <p>
        <button type="submit">Guardar Horas</button>
      </p>
    </form>

    <script>
      // Actualizar valor automáticamente cuando cambian las horas
      document.getElementById('HsTrabaj').addEventListener('input', function() {
        const horas = parseFloat(this.value) || 0;
        const valor = horas * 120;
        document.getElementById('ValorContri').value = '$' + valor.toFixed(2);
      });
    </script>
  </div>
</body>
</html>
