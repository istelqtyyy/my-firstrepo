<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Upload & Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CSS -->
    <style>
        /* Styles for left and right sections */
        .container {
            display: flex;
            justify-content: space-between;
        }

        .upload-section,
        .file-section {
            width: 45%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .file-section {
            max-height: 400px;
            /* Limit the height to add a scrollbar */
            overflow-y: auto;
            /* Add vertical scrollbar if content overflows */
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #fff;
            margin: 5px 0;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        li a {
            text-decoration: none;
            color: #333;
        }

        li a:hover {
            color: #007bff;
            text-decoration: underline;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 10px;
            width: calc(100% - 120px);
            font-size: 16px;
        }

        .search-bar button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #0056b3;
        }

        /* Styles for the Back button */
        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff7f50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #ff6347;
        }
    </style>
</head>

<body>

    <!-- Home Icon -->
    <a href="../dashboard.php" style="text-decoration: none; color: #333;">
        <i class="fas fa-home" style="font-size: 24px;"></i> <!-- Updated home icon -->
    </a>

    <h2>Document Management</h2>

    <div class="container">
        <!-- Left Section: Upload Document -->
        <div class="upload-section">
            <h3>Upload Document</h3>
            <!-- File Upload Form -->
            <form action="core1.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="document" required>
                <button type="submit" name="upload">Upload</button>
            </form>

            <!-- Back Button -->
            <button class="back-button" onclick="goBack()">Back to Previous Activity</button>
        </div>

        <!-- Right Section: Uploaded Files and CRUD -->
        <div class="file-section">
            <h3>Uploaded Documents</h3>

            <!-- Search Bar -->
            <div class="search-bar">
                <form method="GET" action="core1.php">
                    <input type="text" name="search" placeholder="Search documents...">
                    <button type="submit">Search</button>
                </form>
            </div>

            <?php
            // Set the target directory for uploaded files
            $targetDir = "uploads/";

            // Handle File Upload
            if (isset($_POST['upload'])) {
                // Check if the directory exists, create it if it doesn't
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // Create directory with proper permissions
                }

                // Define the path where the file will be stored
                $targetFile = $targetDir . basename($_FILES["document"]["name"]);
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                // Allowed file types for security
                $allowedFileTypes = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];

                // Validate file type
                if (!in_array($fileType, $allowedFileTypes)) {
                    echo "<p class='error'>Sorry, only PDF, DOC, DOCX, PNG, JPG, and JPEG files are allowed.</p>";
                } else {
                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($_FILES["document"]["tmp_name"], $targetFile)) {
                        echo "<p class='success'>File uploaded successfully: " . basename($_FILES["document"]["name"]) . "</p>";
                    } else {
                        echo "<p class='error'>Sorry, there was an error uploading your file.</p>";
                    }
                }
            }

            // Handle Delete
            if (isset($_GET['delete'])) {
                $fileToDelete = $targetDir . basename($_GET['delete']);
                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete);
                    echo "<p class='success'>File deleted successfully.</p>";
                } else {
                    echo "<p class='error'>File not found.</p>";
                }
            }

            // Handle Rename
            if (isset($_POST['rename'])) {
                $oldName = $targetDir . basename($_POST['old_name']);
                $newName = $targetDir . basename($_POST['new_name']);
                if (file_exists($oldName)) {
                    rename($oldName, $newName);
                    echo "<p class='success'>File renamed successfully.</p>";
                } else {
                    echo "<p class='error'>File not found.</p>";
                }
            }

            // Display the Uploaded Files Section with CRUD
            echo "<ul>";

            // Ensure the target directory is defined and exists
            if (is_dir($targetDir)) {
                // Get the list of files
                $files = scandir($targetDir);

                // Search functionality
                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        // Display files that match the search term
                        if (empty($searchTerm) || strpos($file, $searchTerm) !== false) {
                            echo "<li>
                                    <a href='uploads/$file' target='_blank'>$file</a> 
                                    <a href='?delete=$file' style='color:red;'>[Delete]</a>
                                    <form action='core1.php' method='POST' style='display:inline;'>
                                        <input type='hidden' name='old_name' value='$file'>
                                        <input type='text' name='new_name' placeholder='Rename file'>
                                        <button type='submit' name='rename'>Rename</button>
                                    </form>
                                  </li>";
                        }
                    }
                }
            } else {
                echo "<p>No files found in the uploads directory.</p>";
            }

            echo "</ul>";
            ?>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back(); // Go back to the previous page
        }
    </script>

</body>

</html>