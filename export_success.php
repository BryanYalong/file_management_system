<?php
if (!isset($_GET['file'])) {
    die("Invalid request.");
}

$filePath = urldecode($_GET['file']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Successful</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Export Successful!</h2>
    <p>Your file has been saved successfully at:</p>
    <code><?php echo htmlspecialchars($filePath); ?></code>
    <br><br>
    <a href="<?php echo $filePath; ?>" class="btn btn-primary">Download File</a>
    <a href="dashboard.php" class="btn btn-secondary">Go Back</a>
</body>
</html>
