<?php
session_start();
require 'conexiones.php'; // $pdo

if (empty($_SESSION['id_usuario']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

$errors = [];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$UbicInterna = $NumPuerta = $Estado = $CI = '';

// Lista de miembros para dropdown
$stm = $pdo->query('SELECT CI, Nombre FROM miembro ORDER BY Nombre');
$miembros = $stm->fetchAll(PDO::FETCH_ASSOC);

// Si edición, cargar datos
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM vivienda WHERE IdVivienda = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) die('Vivienda no encontrada.');
    $UbicInterna = $row['UbicInterna'];
    $NumPuerta = $row['NumPuerta'];
    $Estado = $row['Estado'];
    $CI = $row['CI'];
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $UbicInterna = trim($_POST['UbicInterna'] ?? '');
    $NumPuerta = trim($_POST['NumPuerta'] ?? '');
    $Estado = trim($_POST['Estado'] ?? 'disponible');
    $CI = $_POST['CI'] ?? null; // '' o null para desasignar

    if ($UbicInterna === '') $errors[] = 'Ubicación interna es obligatoria.';
    if ($NumPuerta === '') $errors[] = 'Número de puerta es obligatorio.';
    $allowed = ['disponible','ocupada','mantenimiento'];
    if (!in_array($Estado, $allowed, true)) $errors[] = 'Estado inválido.';

    // Validar CI si fue seleccionado
    if ($CI) {
        $s = $pdo->prepare('SELECT COUNT(*) FROM miembro WHERE CI = :ci');
        $s->execute(['ci' => $CI]);
        if ($s->fetchColumn() == 0) $errors[] = 'CI seleccionado no existe.';
    } else {
        $CI = null; // guardar null si no seleccionado
    }

    if (empty($errors)) {
        if ($id) {
            $sql = 'UPDATE vivienda SET UbicInterna=:ubic, NumPuerta=:num, Estado=:est, CI=:ci WHERE IdVivienda=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['ubic'=>$UbicInterna, 'num'=>$NumPuerta, 'est'=>$Estado, 'ci'=>$CI, 'id'=>$id]);
            header('Location: viviendas_admin.php?msg=updated'); exit;
        } else {
            $sql = 'INSERT INTO vivienda (UbicInterna, NumPuerta, Estado, CI) VALUES (:ubic, :num, :est, :ci)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['ubic'=>$UbicInterna, 'num'=>$NumPuerta, 'est'=>$Estado, 'ci'=>$CI]);
            header('Location: /ProyectoFinal/admin/viviendas_admin.php?msg=created'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
      <link rel="stylesheet" href="admin/admin2.css">
<head><meta charset="utf-8"><title><?= $id ? 'Editar' : 'Crear' ?> Vivienda</title></head>
<body>
    <div class="vivienda-form-box">
        <h2><?= $id ? 'Editar' : 'Crear' ?> Vivienda</h2>
        <form method="post" action="">
            <?php if ($errors): ?>
                <ul style="color:red;"><?php foreach ($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul>
            <?php endif; ?>

            <p><label>Ubicación interna<br>
                <input type="text" name="UbicInterna" value="<?= htmlspecialchars($UbicInterna) ?>" required>
            </label></p>

            <p><label>Número de puerta<br>
                <input type="text" name="NumPuerta" value="<?= htmlspecialchars($NumPuerta) ?>" required>
            </label></p>

            <p><label>Estado<br>
                <select name="Estado">
                    <option value="disponible" <?= $Estado === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="ocupada" <?= $Estado === 'ocupada' ? 'selected' : '' ?>>Ocupada</option>
                    <option value="mantenimiento" <?= $Estado === 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                </select>
            </label></p>

            <p><label>Asignar a miembro (CI)<br>
                <select name="CI">
                    <option value="">-- Sin asignar --</option>
                    <?php foreach ($miembros as $m): ?>
                        <option value="<?= htmlspecialchars($m['CI']) ?>" <?= $m['CI'] === $CI ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['Nombre'].' — '.$m['CI']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label></p>

            <p><button type="submit"><?= $id ? 'Actualizar' : 'Crear' ?></button>
                <a href="admin/viviendas_admin.php">Volver</a></p>
        </form>
    </div>
</body>
</html>
