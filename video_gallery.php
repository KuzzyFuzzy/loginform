<?php
session_start();
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to log in first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Handle video upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $video = $_FILES['video'];
        $title = $_POST['title']; // Get the title from the form

        // Validate file type and size
        $allowedTypes = ['mp4', 'avi', 'mov', 'mkv'];
        $fileType = strtolower(pathinfo($video['name'], PATHINFO_EXTENSION));
        $maxFileSize = 50 * 1024 * 1024; // 50 MB

        if (in_array($fileType, $allowedTypes) && $video['size'] <= $maxFileSize) {
            $uploadDir = 'uploads/videos/';
            $uploadFile = $uploadDir . basename($video['name']);

            // Create the upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Move the uploaded file to the server
            if (move_uploaded_file($video['tmp_name'], $uploadFile)) {
                // Insert video info into the database
                $sql = "INSERT INTO videos (title, file_path) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);

                if (!$stmt) {
                    die("Error preparing statement: " . $conn->error);
                }

                $filePath = $uploadFile;
                $stmt->bind_param("ss", $title, $filePath);
                $stmt->execute();
                $stmt->close();
                header("Location: video_gallery.php?upload_success=true");
                exit();
            } else {
                echo "<p>Failed to upload file.</p>";
            }
        } else {
            echo "<p>Invalid file type or size.</p>";
        }
    }
}

// Handle video deletion
if (isset($_GET['delete'])) {
    $videoId = intval($_GET['delete']);
    
    // Fetch the file path of the video to be deleted
    $sql = "SELECT file_path FROM videos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $videoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();
    
    if ($video) {
        $filePath = $video['file_path'];
        
        // Delete the file from the server
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete the video record from the database
        $sql = "DELETE FROM videos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $videoId);
        $stmt->execute();
        $stmt->close();
        
        echo "<p>Video deleted successfully!</p>";
    }
}

// Default video fetch for initial page load
$sql = "SELECT * FROM videos";
$result = $conn->query($sql);
$videos = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Video Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .menu-btn {
            font-size: 20px;
            background-color: #FF1493; /* Pink for menu button */
            color: #fff;
            border: none;
            cursor: pointer;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 10;
            border-radius: 50%; /* Make the button circular */
            width: 50px; /* Set width */
            height: 50px; /* Set height */
            padding: 0; /* Remove padding to ensure circular shape */
            text-align: center; /* Center text */
            line-height: 50px; /* Align text vertically */
        }

        .logout-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #FF1493; /* Pink for button */
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            z-index: 4;
            font-size: 16px;
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #FF69B4; /* Light pink for sidebar */
            color: #fff;
            overflow-x: hidden;
            transition: 0.3s ease;
            padding-top: 20px;
            z-index: 2;
        }

        .sidebar .nav-title {
            padding: 0 60px;
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 1px solid #fff;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
            margin: 0;
        }

        .sidebar a:hover {
            background-color: #FF1493;
        }

        .sidebar .active {
            background-color: #FF1493;
        }

        .content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
            height: calc(100vh - 40px);
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .top-bar {
            display: flex;
            width: 100%;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            gap: 20px;
        }

        .search-bar {
            width: 300px;
        }

        .upload-form {
            width: 300px;
            margin-right: 20px;
        }

        .upload-form form, .search-bar form {
            display: flex;
            flex-direction: column;
        }

        .upload-form input[type="text"] {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .upload-form input[type="file"] {
            display: block;
            width: 100%;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .upload-form input[type="submit"] {
            background-color: #FF1493;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .upload-form input[type="submit"]:hover {
            background-color: #FF69B4;
        }

        .search-bar input[type="text"] {
            padding: 7px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 100%;
            margin-bottom: 10px;
        }

        .search-bar input[type="submit"] {
            background-color: #FF1493;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-bar input[type="submit"]:hover {
            background-color: #FF69B4;
        }

        .video-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .video-item {
            width: 300px;
            position: relative;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .video-item h3 {
            margin: 0;
            padding: 10px;
            background-color: #FF1493; /* Pink background for titles */
            color: #fff;
            text-align: center;
            position: relative;
        }

        .video-item video {
            width: 100%;
        }

        .more-options {
            position: absolute;
            right: 10px;
            top: 10px;
        }

        .more-options button {
            background-color: #FF1493;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .more-options-content {
            display: none;
            position: absolute;
            right: 10px;
            top: 40px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .more-options-content a {
            display: block;
            padding: 10px;
            color: #FF1493;
            text-decoration: none;
        }

        .more-options-content a:hover {
            background-color: #f4f4f4;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: inline-block;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                left: 0;
            }

            .sidebar a {
                float: right;
                width: 50%;
                box-sizing: border-box;
            }

            .content {
                margin-left: 0;
                width: 100%;
                height: calc(100vh - 60px);
            }

            .top-bar {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <button class="logout-btn" onclick="confirmLogout()">Logout</button>
    
    <div class="sidebar">
        <div class="nav-title">Gallery</div>
        <a href="dashboard.php">Dashboard</a>
        <a href="userlist.php">User</a>
        <a href="video_gallery.php" class="active">Gallery</a>
    </div>
    <div class="content">
        <h1>Video Gallery</h1>

        <!-- Success Message -->
        <?php if (isset($_GET['upload_success']) && $_GET['upload_success'] === 'true'): ?>
            <div class="success-message" id="success-message">Video uploaded successfully!</div>
        <?php endif; ?>

        <!-- Top Bar with Search Bar and Upload Form -->
        <div class="top-bar">
            <!-- Search Bar -->
            <div class="search-bar">
                <form id="search-form">
                    <input type="text" id="search-query" placeholder="Search videos...">
                </form>
            </div>
            <!-- Upload Form -->
            <div class="upload-form">
                <form action="video_gallery.php" method="post" enctype="multipart/form-data">
                    <input type="text" name="title" placeholder="Enter video title" required>
                    <input type="file" name="video" accept="video/*" required>
                    <input type="submit" value="Upload Video">
                </form>
            </div>
        </div>

        <!-- Video Display -->
        <div class="video-container" id="video-container">
            <!-- Videos will be dynamically loaded here -->
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');
            if (sidebar.style.left === '0px') {
                sidebar.style.left = '-250px';
                content.style.marginLeft = '0';
            } else {
                sidebar.style.left = '0';
                content.style.marginLeft = '250px';
            }
        }

        function toggleOptions(event) {
            const moreOptionsContent = event.target.nextElementSibling;
            moreOptionsContent.style.display = moreOptionsContent.style.display === 'block' ? 'none' : 'block';
        }

        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'logout.php';
            }
        }

        document.getElementById('search-query').addEventListener('input', function(event) {
            const query = event.target.value;

            if (query.length > 2) { // Perform search if query length is greater than 2
                fetch(`search_videos.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(videos => {
                        const videoContainer = document.getElementById('video-container');
                        videoContainer.innerHTML = '';

                        if (videos.length > 0) {
                            videos.forEach(video => {
                                const videoItem = document.createElement('div');
                                videoItem.className = 'video-item';
                                videoItem.innerHTML = `
                                    <h3>${video.title}</h3>
                                    <video controls>
                                        <source src="${video.file_path}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="more-options">
                                        <button onclick="toggleOptions(event)">⋮</button>
                                        <div class="more-options-content">
                                            <a href="?delete=${video.id}" onclick="return confirm('Are you sure you want to delete this video?');">Delete Video</a>
                                        </div>
                                    </div>
                                `;
                                videoContainer.appendChild(videoItem);
                            });
                        } else {
                            videoContainer.innerHTML = '<p>No videos found.</p>';
                        }
                    });
            } else {
                // If query length is 2 or less, show all videos
                fetch('search_videos.php?query=')
                    .then(response => response.json())
                    .then(videos => {
                        const videoContainer = document.getElementById('video-container');
                        videoContainer.innerHTML = '';

                        if (videos.length > 0) {
                            videos.forEach(video => {
                                const videoItem = document.createElement('div');
                                videoItem.className = 'video-item';
                                videoItem.innerHTML = `
                                    <h3>${video.title}</h3>
                                    <video controls>
                                        <source src="${video.file_path}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="more-options">
                                        <button onclick="toggleOptions(event)">⋮</button>
                                        <div class="more-options-content">
                                            <a href="?delete=${video.id}" onclick="return confirm('Are you sure you want to delete this video?');">Delete Video</a>
                                        </div>
                                    </div>
                                `;
                                videoContainer.appendChild(videoItem);
                            });
                        } else {
                            videoContainer.innerHTML = '<p>No videos found.</p>';
                        }
                    });
            }
        });

        // Initial page load to show all videos
        document.addEventListener('DOMContentLoaded', () => {
            fetch('search_videos.php?query=')
                .then(response => response.json())
                .then(videos => {
                    const videoContainer = document.getElementById('video-container');
                    if (videos.length > 0) {
                        videos.forEach(video => {
                            const videoItem = document.createElement('div');
                            videoItem.className = 'video-item';
                            videoItem.innerHTML = `
                                <h3>${video.title}</h3>
                                <video controls>
                                    <source src="${video.file_path}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="more-options">
                                    <button onclick="toggleOptions(event)">⋮</button>
                                    <div class="more-options-content">
                                        <a href="?delete=${video.id}" onclick="return confirm('Are you sure you want to delete this video?');">Delete Video</a>
                                    </div>
                                </div>
                            `;
                            videoContainer.appendChild(videoItem);
                        });
                    } else {
                        videoContainer.innerHTML = '<p>No videos found.</p>';
                    }
                });

            // Hide the success message after 5 seconds
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 2000); // 5000ms = 5 seconds
            }
        });
    </script>
</body>
</html>
