<?php
session_start();

// Check if the user is logged in and has the correct role
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'auditor') {
    // Redirect the user to the login page or another page if not logged in or unauthorized
    header("Location: login.php");
    exit();
}

// Get the username and role from the session
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditor Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="pen_tester_style.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="centered">
    <h2><u>Auditor</u> Dashboard</h2>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
            <ul class="nav-list">
    <li class="nav-item"><a href="auditor_dashboard.php" class="nav-link active">Dashboard</a></li>
    <li class="nav-item"><a href="#" class="nav-link">Projects</a></li>
    <?php
                // Check the role to determine the URL for Home and Profile
                if ($role == 'pen tester') {
                    echo '<li class="nav-item"><a href="profile.php" class="nav-link">Profile</a></li>';
                    echo '<li class="nav-item"><a href="home.php?username=' . urlencode($username) . '&role=' . urlencode($role) . '" class="nav-link">Home</a></li>';
                } else {
                    echo '<li class="nav-item"><a href="home.php?username=' . urlencode($username) . '&role=' . urlencode($role) . '" class="nav-link">Home</a></li>';
                    echo '<li class="nav-item"><a href="profile.php?username=' . urlencode($username) . '&role=' . urlencode($role) . '" class="nav-link">Profile</a></li>';
                }
    ?>
            </ul>

                </nav>
        </div>
    </header>

   <!-- Main Content -->
<div class="main-content container">
    <div class="tasks-section">
        <div class="task-column">
            <div class="task-item" style="background-color: transparent; border: none;">
                <a href="a_ts.php">
                <img src="auditor22.png" alt="Thumbnail" style="width: 100%; height: 80%; object-fit: cover; border-radius: 10px;">
                </a>
                <p class="task-description"><b>TEST SKILLS.</b></p>
                <p class="task-description" >test knowledge on topics</p>
            </div>
            
            <div class="task-item" style="background-color: transparent; border: none;">
                <a href="a_gi.php">
                <img src="auditor44.png" alt="Thumbnail" style="width: 100%; height: 80%; object-fit: cover; border-radius: 10px;">
                </a>
                <p class="task-description"><b>GAPS IDENTIFIED.</b></p>
                <p class="task-description">tips for improvement</p>
            </div>       
        </div>
        <div class="task-column">
            <div class="task-item" style="background-color: transparent; border: none;">
                <a href="a_tp.php">
                <img src="auditor33.png" alt="Thumbnail" style="width: 100%; height: 80%; object-fit: cover; border-radius: 10px;">
                </a>
                <p class="task-description"><b>TRACK PROGRESS.</b></p>
                <p class="task-description">analyse your scores</p>
             </div>
           
            <div class="task-item" style="background-color: transparent; border: none;">
                <a href="a_ca.php">
                <img src="auditor55.png" alt="Thumbnail" style="width: 100%; height: 80%; object-fit: cover; border-radius: 10px;">
                </a>
                <p class="task-description"><b>COMPETENCY ALIGNMENT.</b></p>
                <p class="task-description">organizational expectations</p>
            </div>     
        </div>
        <div class="task-column">
            <div class="task-item" style="background-color: transparent; border: none;">
                <a href="a_sw.php">
                <img src="auditor11.png" alt="Thumbnail" style="width: 100%; height: 80%; object-fit: cover; border-radius: 10px;">
                </a>
                <p class="task-description"><b>STRENGTHS & WEAKNESSES.</b></p>
                <p class="task-description">areas needing improvement</p>  
            </div>
            
            <div class="task-item" style="background-color: transparent; border: none;">
                <a href="a_plep.php">       
                <img src="auditor66.png" alt="Thumbnail" style="width: 100%; height: 80%; object-fit: cover; border-radius: 10px;">
                </a>
                <p class="task-description"><b>PERSONAL LEARNING PATH.</b></p>
                <p class="task-description">recommendations</p>
            </div>
        </div>
    </div>
</div>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 CYBERSECURITY-TESTER. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript to toggle active link in the navigation menu -->
    <script src="pen_tester_script.js"></script>
</body>
</html>
