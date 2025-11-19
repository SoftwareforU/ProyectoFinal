<?php
session_start();
require '../conexiones.php';

// Debug
error_log("DELETE REQUEST - GET: " . print_r($_GET, true));
error_log("SESSION ROLE: " . $_SESSION['role']);

// Protege la página
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
error_log("ID a eliminar: " . $id);

// Validar que ID es válido
if ($id <= 0) {
    $_SESSION['error'] = 'ID de usuario inválido';
    header('Location: index.php');
    exit;
}

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare('SELECT IdUsuario FROM Usuario WHERE IdUsuario = :id');
    $stmt->execute(['id' => $id]);
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = 'El usuario no existe';
        header('Location: index.php');
        exit;
    }
    
    // 1) Borrar en Miembro primero (si hay relación)
    $stmt = $pdo->prepare('DELETE FROM Miembro WHERE CI IN (SELECT CI FROM Usuario WHERE IdUsuario = :id)');
    $stmt->execute(['id' => $id]);
    
    // 2) Luego borrar en Usuario
    $stmt = $pdo->prepare('DELETE FROM Usuario WHERE IdUsuario = :id');
    $stmt->execute(['id' => $id]);
    
    $_SESSION['success'] = 'Usuario eliminado correctamente';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al eliminar: ' . $e->getMessage();
}

header('Location: usuarios.php');
exit;
