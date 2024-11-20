<?php
session_start();
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Retrieve user ID
$userId = $_SESSION['UserID'];

// Fetch user performance data
$query = "SELECT SkillID, Score FROM userperformances WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$feedback = [];
while ($row = $result->fetch_assoc()) {
    $skillID = $row['SkillID'];
    $score = $row['Score'];

    // Get the category name for the skill
    $categoryQuery = "SELECT Category FROM skills WHERE SkillID = ?";
    $categoryStmt = $conn->prepare($categoryQuery);
    $categoryStmt->bind_param("i", $skillID);
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->get_result();
    $categoryRow = $categoryResult->fetch_assoc();
    $category = $categoryRow['Category'] ?? 'Unknown Skill'; // Default to 'Unknown Skill' if not found

    // Get total questions for the skill
    $totalQuestionsQuery = "SELECT COUNT(*) AS TotalQuestions FROM questions WHERE SkillID = ?";
    $totalStmt = $conn->prepare($totalQuestionsQuery);
    $totalStmt->bind_param("i", $skillID);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalQuestions = $totalRow['TotalQuestions'];

    // Calculate percentage score
    $percentage = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;

   // Determine feedback based on percentage
if ($percentage >= 80) {
    $feedback[] = "🎉 Excellent work on <strong>$category</strong>! You're practically a superhero in this skill! You scored " . round($percentage, 2) . "%.";
} elseif ($percentage >= 60) {
    $feedback[] = "👍 Good job on <strong>$category</strong>! You're getting there, just a few more superhero training sessions! You scored " . round($percentage, 2) . "%.";
} else {
    $feedback[] = "😬 Oh No! Seems we need to get you back up. You need improvement in <strong>$category</strong>. Let's turn that score of " . round($percentage, 2) . "% into a dazzling display of skills!";
}


    $categoryStmt->close();
    $totalStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competency Alignment</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .feedback-container {
            margin-top: 50px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .feedback-container p {
            background: #e9f7f9;
            margin: 10px 0;
            padding: 15px;
            border-left: 5px solid #76c7c0; /* Accent color */
            border-radius: 5px;
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
    <h1>Competency Alignment</h1>
    <div class="feedback-container">
        <?php foreach ($feedback as $msg): ?>
            <p><?php echo $msg; ?></p>
        <?php endforeach; ?>
    </div>
</body>
</html>
