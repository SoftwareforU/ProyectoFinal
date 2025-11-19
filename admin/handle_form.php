<?php
session_start();
require '../conexiones.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id     = (int)($_GET['id']     ?? 0);
$accion = $_GET['action'] ?? '';
if ($id <= 0 || !in_array($accion, ['accept','reject'], true)) {
    header('Location: formularios.php');
    exit;
}

$estado = $accion === 'accept' ? 'aceptado' : 'rechazado';
$pdo->prepare('UPDATE formularios SET estado = :e WHERE id = :id')
    ->execute(['e' => $estado, 'id' => $id]);

if ($accion === 'accept') {
    // actualiza rol a miembro
    $uid = $pdo->query(
      'SELECT user_id FROM formularios WHERE id = '.$id
    )->fetchColumn();
    $pdo->prepare(
      'UPDATE Usuario SET role = "miembro" WHERE IdUsuario = :uid'
    )->execute(['uid' => $uid]);
}

header('Location: formularios.php');
exit;