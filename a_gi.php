<?php
session_start();
require_once "db_connection.php";

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Retrieve the username and role from the session
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Connect to the database
$conn = connectToDatabase();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user ID based on the username
$query = "SELECT UserID FROM users WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Check if user ID was found
if (!$userId) {
    echo "User not found.";
    exit();
}

// Function to analyze performance and identify gaps
function analyzePerformance($userId) {
    $conn = connectToDatabase();
    
    // Get scores and total questions for each skill along with skill categories
    $sql = "SELECT userperformances.SkillID, Score, 
                   (SELECT COUNT(*) FROM questions WHERE SkillID = userperformances.SkillID) AS TotalQuestions,
                   skills.Category AS SkillName  -- Use 'Category' to get the skill name
            FROM userperformances 
            JOIN skills ON userperformances.SkillID = skills.SkillID
            WHERE UserID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $gaps = [];
    $threshold = 50; // Adjust this threshold as needed

    while ($row = $result->fetch_assoc()) {
        $score = $row['Score'];
        $totalQuestions = $row['TotalQuestions'];
        $skillName = $row['SkillName'];

        // Check if score is below threshold and not a full score
        if ($score < $threshold && $score < $totalQuestions) {
            $percentage = ($score / $totalQuestions) * 100; // Calculate percentage
            // Store message with skill name and gap details
            $gaps[] = "Improvements needed for $skillName. You scored $score out of $totalQuestions questions (".round($percentage, 2)."%).";
        }
    }

    $stmt->close();
    $conn->close();

    return $gaps;
}


// Analyze performance and identify gaps
$identifiedGaps = analyzePerformance($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaps Identified</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .gaps-identified ul {
            list-style-type: none;
            padding: 0;
        }
        .gaps-identified li {
            background: #e9f7f9;
            margin: 10px 0;
            padding: 15px;
            border-left: 5px solid #76c7c0; /* Accent color */
            border-radius: 5px;
        }
        .gaps-identified li:hover {
            background: #d1f3f7; /* Lighten on hover */
            cursor: pointer;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center">Gaps Identified</h1>
    <div class="gaps-identified">
        <?php if (!empty($identifiedGaps)): ?>
            <ul>
                <?php foreach ($identifiedGaps as $gap): ?>
                    <li><?php echo htmlspecialchars($gap); ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Consider revising these topics or seeking additional resources.</p>
        <?php else: ?>
            <p>No gaps identified! Great job!</p>
        <?php endif; ?>
    </div>
    <div class="footer">© <?php echo date("Y"); ?> Your Company Name. All rights reserved.</div>
</div>
</body>
</html>
