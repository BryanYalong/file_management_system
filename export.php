<?php

require_once 'config.php';

$exportDir = 'exports/';
$zipFileName = 'files_export_' . date('Y-m-d_H-i-s') . '.zip';
$zipFilePath = $exportDir . $zipFileName;

// Ensure the exports directory exists
if (!is_dir($exportDir)) {
    mkdir($exportDir, 0777, true);
}

// Initialize ZIP archive
$zip = new ZipArchive();
if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

    // Fetch files from the database
    $stmt = $pdo->query("SELECT filename, filepath FROM files ORDER BY uploaded_at DESC");
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($files as $file) {
        $filePath = $file['filepath'];
        
        // Ensure the file exists before adding to ZIP
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($filePath));
        }
    }

    $zip->close();

    // Force download the ZIP file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFilePath) . '"');
    header('Content-Length: ' . filesize($zipFilePath));
    readfile($zipFilePath);

    // Optional: Delete the ZIP file after download
    unlink($zipFilePath);
    
    exit();
} else {
    echo "Failed to create ZIP file.";
}

?>
