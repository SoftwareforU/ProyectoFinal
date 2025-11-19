<?php
session_start();
require 'conexiones.php';

// Obtener notificaciones de comprobantes rechazados
$notificaciones = [];
if (!empty($_SESSION['id_usuario'])) {
    $stmt = $pdo->prepare('SELECT * FROM miembrofondo WHERE IdUsuario = :uid AND EstadoPago = :estado');
    $stmt->execute([
        'uid'    => $_SESSION['id_usuario'],
        'estado' => 'rechazado'
    ]);
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="hola.css">
    <title>frontend</title>
</head>
<body>
    <?php if (!empty($notificaciones)): ?>
      <div style="background:red; color:white; padding:15px; margin:10px; border-radius:5px;">
        <h3>⚠️ Notificación importante</h3>
        <?php foreach ($notificaciones as $n): ?>
          <p>Tu comprobante de pago del <?= htmlspecialchars($n['FchPago']) ?> (monto: $<?= number_format($n['MontoC'], 2) ?>) ha sido <strong>rechazado</strong>.</p>
          <p>Por favor, verifica los datos e intenta nuevamente en la sección de pagos.</p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

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

        <div>

            

        </div>
</body>
</html>
