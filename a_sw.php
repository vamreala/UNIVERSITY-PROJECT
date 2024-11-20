<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Retrieve name and role from the session
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch total skills and attempted skills
$totalSkillsQuery = "SELECT COUNT(*) AS totalSkills FROM skills"; // Assuming you have a skills table
$attemptedSkillsQuery = "SELECT COUNT(*) AS attemptedSkills FROM userperformances WHERE UserID = ? AND Score IS NOT NULL";

$totalSkillsResult = $conn->query($totalSkillsQuery);
$totalSkillsRow = $totalSkillsResult->fetch_assoc();
$totalSkills = $totalSkillsRow['totalSkills'];

$stmt = $conn->prepare($attemptedSkillsQuery);
$stmt->bind_param("i", $_SESSION['UserID']); // Using user_id directly
$stmt->execute();
$attemptedSkillsResult = $stmt->get_result();
$attemptedSkillsRow = $attemptedSkillsResult->fetch_assoc();
$attemptedSkills = $attemptedSkillsRow['attemptedSkills'];

$stmt->close();
$conn->close();

// Calculate attempted and not attempted counts
$notAttemptedSkills = $totalSkills - $attemptedSkills;

// Logic to determine badge messages
$badgeMessage = "";
if ($attemptedSkills == $totalSkills && $totalSkills > 0) {
    $badgeMessage = "Congratulations! You've mastered all skills! 🎉";
} elseif ($attemptedSkills > $totalSkills / 2) {
    $badgeMessage = "You're over halfway there! Keep it up! 🌟";
} elseif ($attemptedSkills == 1) {
    $badgeMessage = "Great start! You've taken your first step! 🚀";
} elseif ($attemptedSkills >= 2) {
    $badgeMessage = "Nice work! You're making progress! 🌈";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills Attempted</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Adjust the size of the canvas */
        #skillsAttemptedChart {
            max-width: 400px;
            max-height: 400px;
            width: 100%; /* Responsive width */
            height: auto; /* Let height auto-adjust */
            margin: 0 auto; /* Center the chart */
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .badge-message {
            text-align: center;
            font-size: 1.2em;
            margin: 20px 0;
            color: #4CAF50; /* Green color for positive messages */
        }
    </style>
</head>
<body>
    <h1>Skills Attempted</h1>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <div class="badge-message"><?php echo $badgeMessage; ?></div>
    <canvas id="skillsAttemptedChart"></canvas>

    <script>
        const ctx = document.getElementById('skillsAttemptedChart').getContext('2d');
        const attemptedCount = <?php echo $attemptedSkills; ?>;
        const notAttemptedCount = <?php echo $notAttemptedSkills; ?>;

        const skillsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Attempted', 'Not Attempted'],
                datasets: [{
                    data: [attemptedCount, notAttemptedCount],
                    backgroundColor: ['#36a2eb', '#ff6384'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,  // Disable aspect ratio to control size
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
    </script>
</body>
</html>
