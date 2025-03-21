<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$admin_name = $_SESSION['email'] ?? 'Admin'; // Keep the admin's name
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: rgb(8, 70, 130);
            font-family: Arial, sans-serif;
            text-align: center;
            color: white;
        }
        .loading-container {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .admin-name {
            font-size: 18px;
            margin-top: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="loading-container">
    <img src="SPCC_R.png" alt="SPCC Logo" width="100"> 
    
    <div class="loading-text"></div>
    <div class="admin-name">Welcome, <?php echo htmlspecialchars($admin_name); ?>!</div>

    <!-- Wrapper for centering the spinner -->
    <div class="spinner-wrapper">
        <div class="spinner">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>






    <script>
        setTimeout(function() {
            window.location.href = "dashboard.php"; // Redirect to DASHBOARD after loading
        }, 3865); // 3.865 seconds delay
    </script>
</body>
</html>
