<?php
session_start();
require 'conexiones.php';

// Obtener el nombre de usuario desde la sesión
$usuario = $_SESSION['usuario'] ?? '';

// Inicializar variables
$miembro = null;
$mensaje = '';
$errores = [];
$rol = $_SESSION['role'] ?? '';

// Buscar el CI en la tabla usuario
if ($usuario !== '') {
    $stmt = $pdo->prepare("SELECT CI, role FROM usuario WHERE Usuarios = :usuario LIMIT 1");
    $stmt->execute(['usuario' => $usuario]);
    $row = $stmt->fetch();

    if ($row && !empty($row['CI'])) {
        $ci = $row['CI'];
        $rol = $row['role'];

        // Si se envió el formulario para modificar datos
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campos = [];
            $valores = [];
            $stmt = $pdo->prepare("DESCRIBE miembro");
            $stmt->execute();
            $columnas = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($columnas as $col) {
                if ($col === 'CI') continue;
                if (isset($_POST[$col])) {
                    // Si el campo es Edad y está vacío, guarda como NULL
                    if ($col === 'Edad' && ($_POST[$col] === '' || $_POST[$col] === null)) {
                        $campos[] = "$col = NULL";
                    } else {
                        $campos[] = "$col = :$col";
                        $valores[$col] = $_POST[$col];
                    }
                }
            }
            $valores['ci'] = $ci;

            if ($campos) {
                $sql = "UPDATE miembro SET " . implode(', ', $campos) . " WHERE CI = :ci";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($valores);
                $mensaje = 'Datos actualizados correctamente.';
            }
        }

        // Obtener los datos actualizados del miembro
        $stmt = $pdo->prepare("SELECT * FROM miembro WHERE CI = :ci LIMIT 1");
        $stmt->execute(['ci' => $ci]);
        $miembro = $stmt->fetch(PDO::FETCH_ASSOC);

        // Agrega el rol al array para mostrarlo en el perfil
        if ($miembro) {
            $miembro['role'] = $rol;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del Miembro</title>
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

<div style="display:flex; justify-content:center; align-items:center; min-height:80vh;">
    <div id="login">
        <h2>Perfil del miembro</h2>
        <?php if ($mensaje): ?>
            <p style="color:green;"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>
        <?php if ($errores): ?>
            <ul style="color:red;">
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if ($miembro): ?>
            <form method="post">
                <table border="1" cellpadding="6" cellspacing="0" width="100%">
                    <?php
                    $campos_permitidos = ['CI', 'Nombre', 'Apellido', 'Edad', 'Email', 'role'];
                    foreach ($campos_permitidos as $campo):
                        if (isset($miembro[$campo])):
                            // Mostrar "ROL" en vez de "role"
                            $th_text = ($campo === 'role') ? 'Rol' : $campo;
                    ?>
                        <tr>
                            <th><?= htmlspecialchars($th_text) ?></th>
                            <td>
                                <?php if ($campo === 'Edad'): ?>
                                    <input type="number" name="Edad" value="<?= $miembro['Edad'] !== null && $miembro['Edad'] != 0 ? htmlspecialchars($miembro['Edad']) : '' ?>">
                                <?php elseif ($campo === 'CI' || $campo === 'role'): ?>
                                    <?= htmlspecialchars($miembro[$campo]) ?>
                                <?php else: ?>
                                    <input type="text" name="<?= htmlspecialchars($campo) ?>" value="<?= htmlspecialchars($miembro[$campo]) ?>">
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </table>
                <p><button type="submit">Guardar cambios</button></p>
            </form>
        <?php elseif ($usuario !== ''): ?>
            <p>No se encontró el miembro para ese usuario.</p>
        <?php else: ?>
            <p>No se especificó ningún usuario.</p>
        <?php endif; ?>
        <p><a href="frontend.php">Volver</a></p>
    </div>
</div>
</body>
</html>