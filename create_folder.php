<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $folder_name = trim($_POST['folder_name']);

    if (!empty($folder_name)) {
        $folder_path = "uploads/" . $folder_name;

        // Check if folder already exists
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true); // Create folder

            // Insert into database (optional)
            $stmt = $pdo->prepare("INSERT INTO folders (folder_name) VALUES (?)");
            $stmt->execute([$folder_name]);

            $_SESSION['success'] = "Folder '$folder_name' created successfully.";
        } else {
            $_SESSION['error'] = "Folder already exists.";
        }
    } else {
        $_SESSION['error'] = "Please enter a folder name.";
    }

    header("Location: dashboard.php");
    exit();
}
?>
