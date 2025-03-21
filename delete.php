<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if "Delete All" is requested
if (isset($_GET['action']) && $_GET['action'] === 'delete_all') {
    $stmt = $pdo->query("SELECT filepath FROM files");
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($files as $file) {
        if (file_exists($file['filepath'])) {
            unlink($file['filepath']); // Delete from server
        }
    }

    $pdo->query("DELETE FROM files");

    header("Location: dashboard.php");
    exit();
}

// Delete a single file
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT filepath FROM files WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file && file_exists($file['filepath'])) {
        unlink($file['filepath']); // Remove file from server
    }

    $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
    $stmt->execute([$_GET['id']]);

    header("Location: dashboard.php");
    exit();
}
?>
