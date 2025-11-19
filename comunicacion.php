<?php
// comunicacion.php
declare(strict_types=1);
session_start();

$pdo = require __DIR__ . '/conexiones.php';

if (empty($_SESSION['id_usuario'])) {
    header('Location: /ProyectoFinal/login.php');
    exit;
}

$idUsuario = (int) $_SESSION['id_usuario'];
$nombreUsuario = $_SESSION['nombre'] ?? '';

$errors = [];
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $titulo = trim($_POST['titulo'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($tipo && $titulo && $mensaje) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO comunicaciones (IdUsuario, Tipo, Titulo, Mensaje)
                VALUES (:id, :tipo, :titulo, :mensaje)
            ");
            $stmt->execute([
                ':id' => $idUsuario,
                ':tipo' => $tipo,
                ':titulo' => $titulo,
                ':mensaje' => $mensaje
            ]);
            $ok = "Tu $tipo fue enviada correctamente.";
            $titulo = $mensaje = '';
        } catch (PDOException $e) {
            $errors[] = 'Error al guardar la comunicación.';
        }
    } else {
        $errors[] = 'Todos los campos son obligatorios.';
    }
}

$stmt = $pdo->prepare("SELECT * FROM comunicaciones WHERE IdUsuario = :id ORDER BY FechaCreacion DESC");
$stmt->execute([':id' => $idUsuario]);
$misComunicaciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Enviar comunicación</title>
<link rel="stylesheet" href="comunicacion2.css">
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
</div>

<!-- El script debe ir fuera del div de la barra -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var openMenu = document.getElementById('open-menu');
    var sideMenu = document.getElementById('side-menu');
    var menuToggle = document.getElementById('menu-toggle');

    if (openMenu && sideMenu && menuToggle) {
        openMenu.onclick = function() {
            sideMenu.classList.add('active');
        };
        menuToggle.onclick = function() {
            sideMenu.classList.remove('active');
        };
    }
});
</script>

<div id="comunicacion-box">
    <h2>Enviar mensaje</h2>

    <?php if ($ok): ?><div class="alert success"><?=htmlspecialchars($ok)?></div><?php endif; ?>
    <?php if (!empty($errors)): ?><div class="alert error"><?=implode('<br>', array_map('htmlspecialchars',$errors))?></div><?php endif; ?>

    <form method="post" autocomplete="off">
      <label>Tipo:
        <select name="tipo" required>
          <option value="Queja">Queja</option>
          <option value="Consulta">Consulta</option>
          <option value="Propuesta">Propuesta</option>
        </select>
      </label>

      <label>Título:
        <input type="text" name="titulo" value="<?=htmlspecialchars($titulo ?? '')?>" maxlength="255" required>
      </label>

      <label>Mensaje:
        <textarea name="mensaje" rows="6" required><?=htmlspecialchars($mensaje ?? '')?></textarea>
      </label>

      <button type="submit">Enviar</button>
    </form>

    <h3>Mis mensajes enviados</h3>
    <?php if (empty($misComunicaciones)): ?>
      <p>No has enviado ninguna comunicación aún.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Fecha</th>
          <th>Tipo</th>
          <th>Título</th>
          <th>Mensaje</th>
          <th>Estado</th>
          <th>Respuesta del admin</th>
        </tr>
        <?php foreach ($misComunicaciones as $com): ?>
          <tr>
            <td><?= htmlspecialchars($com['FechaCreacion']) ?></td>
            <td><?= htmlspecialchars($com['Tipo']) ?></td>
            <td><?= htmlspecialchars($com['Titulo']) ?></td>
            <td><?= nl2br(htmlspecialchars($com['Mensaje'])) ?></td>
            <td><?= htmlspecialchars($com['Estado']) ?></td>
            <td>
              <?php if ($com['Respuesta']): ?>
                <div><?= nl2br(htmlspecialchars($com['Respuesta'])) ?></div>
                <small>
                  <?= $com['FechaRespuesta'] ? 'El ' . htmlspecialchars($com['FechaRespuesta']) : '' ?>
                  <?= $com['UsuarioAdmin'] ? 'por ' . htmlspecialchars($com['UsuarioAdmin']) : '' ?>
                </small>
              <?php else: ?>
                <em>Pendiente</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
    <p>
      <a href="frontend.php">
        <button type="button">Volver</button>
      </a>
    </p>
</div>

</body>
</html>
