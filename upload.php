<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['upload']) && !empty($_FILES['files']['name'][0])) {
    // Get the selected folder name or create a new one
    $folder_name = isset($_POST['folder_name']) ? trim($_POST['folder_name']) : '';
    $new_folder = isset($_POST['new_folder']) ? trim($_POST['new_folder']) : '';

    // If a new folder is entered, use that instead
    if (!empty($new_folder)) {
        $folder_name = $new_folder;

        // Insert new folder into database if it doesn't exist
        $stmt = $pdo->prepare("INSERT INTO folders (folder_name) VALUES (?) ON DUPLICATE KEY UPDATE folder_name=folder_name");
        $stmt->execute([$folder_name]);
    }

    // Ensure folder exists
    $folder_path = "uploads/" . $folder_name;
    if (!empty($folder_name) && !file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    // Process file upload
    foreach ($_FILES['files']['name'] as $key => $filename) {
        $destination = $folder_path . "/" . basename($_FILES["files"]["name"][$key]);

        if (move_uploaded_file($_FILES["files"]["tmp_name"][$key], $destination)) {
            // Save file details in database
            $stmt = $pdo->prepare("INSERT INTO files (filename, filepath, folder_name) VALUES (?, ?, ?)");
            $stmt->execute([$filename, $destination, $folder_name]);
        }
    }

    header("Location: dashboard.php"); // Redirect back to dashboard
    exit();
}


?>
