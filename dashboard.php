<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selected_folder = isset($_GET['folder']) ? $_GET['folder'] : '';

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM files WHERE filename LIKE ? AND folder_name LIKE ? ORDER BY uploaded_at DESC");
    $stmt->execute(["$search%", $selected_folder ? $selected_folder : "%"]);
} else {
    if ($selected_folder) {
        $stmt = $pdo->prepare("SELECT * FROM files WHERE folder_name = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$selected_folder]);
    } else {
        $stmt = $pdo->query("SELECT * FROM files ORDER BY uploaded_at DESC");
    }
}

$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            padding: 20px;
            background-color: #f8f9fa;
            transition: background-color 0.5s ease-in-out;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }




/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: -180px;  /* Hide sidebar initially */
    width: 180px;
    height: 100vh;
    background: #2c3e50;
    padding-top: 20px;
    transition: left 0.3s ease-in-out; /* Smooth animation */
}

/* When sidebar is open */
.sidebar.open {
    left: 0; /* Slide in */
}

/* Toggle Button */
.toggle-btn {
    position: absolute;
    top: 30px;
    right: -40px;
    background: #2c3e50;
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    border-radius: 5px;
}

/* Sidebar Buttons */
.sidebar a {
    display: block;
    width: 140px;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s;
    text-align: center;
    margin-bottom: 10px;
}

/* Button Colors */
.home-btn { 
    background-color: rgb(56, 162, 255); 
    text-align: center;
    position: absolute;
    top: 20px;
    left: 20px;
    width: 80%;
}

.folder-btn {
    background-color: rgb(249, 162, 56);
    text-align: center; 
    position: absolute;
    top: 80px;
    left: 20px;
    width: 80%;
}

.logout-btn { 
    background-color: #d9534f; 
}

/* Hover Effects */
.sidebar a:hover {
    filter: brightness(85%);
}

/* Push main content when sidebar is open */
.main-content {
    margin-left: 20px; /* Default */
    transition: margin-left 0.3s ease-in-out;
}

/* Adjust content when sidebar is open */
.sidebar.open ~ .main-content {
    margin-left: 200px;
}




        /* File Preview */
        .thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            cursor: pointer;
        }

        /* Color Mode Button */
        #btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .text-center {
    position: fixed;
    top: 20px;  /* Adjust as needed */
    left: 50%;
    transform: translateX(-50%);
    width: auto;
    background-color: white; /* Prevent overlap issues */
    padding: 10px 20px;
    text-align: center;
    font-weight: bold;
    font-size: 24px;
    z-index: 1000; /* Keep it above other elements */
    color: rgb(13, 1, 115);
    font-family: 'Merriweather', serif;  /* Change font if needed */
    

}


    </style>
</head>
<body>

    <!-- Header -->
    <div class="text-center">
          <a>File Management System</a>
    </div>






    <div class="container">

    <!-- Folder Display -->
    <?php
    $folders = $pdo->query("SELECT * FROM folders")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="mb-3">
        <h5>FOLDERS</h5>
        <?php foreach ($folders as $folder): ?>
            <a href="dashboard.php?folder=<?php echo urlencode($folder['folder_name']); ?>" class="btn btn-secondary btn-sm">
                <?php echo htmlspecialchars($folder['folder_name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php
// Fetch existing folders from the database
$folders = $pdo->query("SELECT * FROM folders")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex align-items-center">
    <form action="upload.php" method="POST" enctype="multipart/form-data" class="mb-3">
        <!-- Folder Selection -->
        <label for="folder_name">Select Folder or Create New:</label>
        <select name="folder_name" id="folder_name" class="form-control mb-2">
            <option value="">-- Select Folder --</option>
            <?php foreach ($folders as $folder): ?>
                <option value="<?php echo htmlspecialchars($folder['folder_name']); ?>">
                    <?php echo htmlspecialchars($folder['folder_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Option to create a new folder -->
        <input type="text" name="new_folder" class="form-control mb-2" placeholder="Or enter new folder name">

        <!-- File Upload -->
        <input type="file" name="files[]" class="form-control file-input" multiple required>
        <button type="submit" name="upload" class="upload-btn btn-sm mt-2">Upload Files</button>
    </form>
</div>




        <!-- Search Form -->
         <form method="GET" class="mb-3">
            <div class="input-group search-container">
                <input type="text" name="search" id="search" class="form-control" placeholder="Search files..." value="<?php echo htmlspecialchars($search); ?>" onkeyup="filterResults()">
            </div>
        </form>

        <!-- File List -->
        <table class="table table-bordered" id="fileTable">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Uploaded At</th>
                    <th>Preview</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['filename']); ?></td>
                        <td><?php echo $file['uploaded_at']; ?></td>

                        <td>
    <?php
    $file_extension = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $pdf_extensions = ['pdf'];
    $doc_extensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    

    if (in_array($file_extension, $image_extensions)) {
        // Open image in a modal viewer instead of direct link
        echo '<img src="' . $file['filepath'] . '" class="thumbnail" onclick="openImage(\'' . $file['filepath'] . '\')">';
    } elseif (in_array($file_extension, $pdf_extensions)) {
        // Display PDF in an iframe
        echo '<iframe src="' . $file['filepath'] . '" width="100" height="50"></iframe>';
    } elseif (in_array($file_extension, $doc_extensions)) {
        // Google Docs Viewer (ensuring it loads properly)
        echo '<iframe src="https://docs.google.com/gview?url=' . urlencode("http://localhost/your_project_folder/" . $file['filepath']) . '&embedded=true" width="100" height="50"></iframe>';
    } else {
        echo '<a href="' . $file['filepath'] . '" target="_blank" class="btn btn-secondary btn-sm">View File</a>';
    }
    ?>
</td>


                        <td>
                            <a href="<?php echo $file['filepath']; ?>" download class="btn btn-success btn-sm">Download</a>
                            <a href="delete.php?id=<?php echo $file['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <button onclick="confirmExport()" class="btn btn-warning">Export Files</button>
        <a href="#" onclick="confirmDeleteAll()" class="btn btn-danger">Delete All</a>
        <button id="btn">☕</button>
    </div>



   <!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    
    <div class="home-container">
        <a href="home.php" class="home-btn">Home</a>
    </div>

    <div class="folder-container">
        <a href="folder.php" class="folder-btn">Folder</a>
    </div>

    <div class="logout-container">
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>


<!-- Main Content -->
<div class="main-content">
    <!-- Your dashboard content here -->
</div>


<!-- JavaScript for Sidebar -->
<script>
function toggleSidebar() {
    let sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("open");
}
</script>



    <script>
        function filterResults() {
            let input = document.getElementById("search").value.toLowerCase();
            let rows = document.querySelectorAll("#fileTable tbody tr");
            rows.forEach(row => {
                let filename = row.cells[0].textContent.toLowerCase();
                row.style.display = filename.startsWith(input) ? "" : "none";
            });
        }

        function confirmExport() {
            if (confirm("Are you sure you want to export all files?")) {
                window.location.href = "export.php";
            }
        }

        function confirmDeleteAll() {
            if (confirm("⚠️ WARNING: This will permanently delete all files!")) {
                window.location.href = "delete.php?action=delete_all";
            }
        }

        const colors = ["#f8f9fa", "#040404", "#f4a261", "#2a9d8f", "#264653", "#e76f51", "#cf0b8b", "#1201f7"];
        let currentColorIndex = 0;
        
        document.getElementById('btn').addEventListener('click', () => {
            currentColorIndex = (currentColorIndex + 1) % colors.length;
            document.body.style.backgroundColor = colors[currentColorIndex];
        });
    </script>


    <!-- PREVIEW -->
<script>
    function openImage(src) {
        let modal = document.createElement("div");
        modal.style.position = "fixed";
        modal.style.top = "0";
        modal.style.left = "0";
        modal.style.width = "100%";
        modal.style.height = "100%";
        modal.style.backgroundColor = "rgba(0,0,0,0.8)";
        modal.style.display = "flex";
        modal.style.justifyContent = "center";
        modal.style.alignItems = "center";
        modal.style.zIndex = "1000";

        let img = document.createElement("img");
        img.src = src;
        img.style.maxWidth = "90%";
        img.style.maxHeight = "90%";
        img.style.borderRadius = "10px";
        img.style.boxShadow = "0 0 20px rgba(255,255,255,0.3)";
        
        modal.appendChild(img);
        
        modal.onclick = function() {
            document.body.removeChild(modal);
        };

        document.body.appendChild(modal);
    }



    function showFileManagement() {
    document.getElementById("file-management").style.display = "block";  // Show file management
    document.getElementById("other-content").style.display = "none";     // Hide other content
}




</script>



</body>
</html>
