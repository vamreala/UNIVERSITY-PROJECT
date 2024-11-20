<?php
session_start();
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
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

// Fetch aggregated performance data based on user role
switch ($role) {
    case 'teacher':
        $query = "
            SELECT 
                COALESCE(q.Category, 'Unknown') AS Category, 
                COUNT(up.SkillID) AS TotalAttempts, 
                SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) AS CorrectAnswers,
                ROUND(
                    (SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(up.SkillID), 0)) * 100, 2
                ) AS Accuracy
            FROM userperformances up
            JOIN questions q ON up.SkillID = q.SkillID
            WHERE up.UserID = ? AND q.Role = 'teacher'
            GROUP BY q.Category
            ORDER BY q.Category
        ";
        break;

    case 'auditor':
        $query = "
            SELECT 
                COALESCE(q.Category, 'Unknown') AS Category, 
                COUNT(up.SkillID) AS TotalAttempts, 
                SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) AS CorrectAnswers,
                ROUND(
                    (SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(up.SkillID), 0)) * 100, 2
                ) AS Accuracy
            FROM userperformances up
            JOIN questions q ON up.SkillID = q.SkillID
            WHERE up.UserID = ? AND q.Role = 'auditor'
            GROUP BY q.Category
            ORDER BY q.Category
        ";
        break;

    case 'pentester':
        $query = "
            SELECT 
                COALESCE(q.Category, 'Unknown') AS Category, 
                COUNT(up.SkillID) AS TotalAttempts, 
                SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) AS CorrectAnswers,
                ROUND(
                    (SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(up.SkillID), 0)) * 100, 2
                ) AS Accuracy
            FROM userperformances up
            JOIN questions q ON up.SkillID = q.SkillID
            WHERE up.UserID = ? AND q.Role = 'pentester'
            GROUP BY q.Category
            ORDER BY q.Category
        ";
        break;

    case 'top management':
        $query = "
            SELECT 
                COALESCE(q.Category, 'Unknown') AS Category, 
                COUNT(up.SkillID) AS TotalAttempts, 
                SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) AS CorrectAnswers,
                ROUND(
                    (SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(up.SkillID), 0)) * 100, 2
                ) AS Accuracy
            FROM userperformances up
            JOIN questions q ON up.SkillID = q.SkillID
            WHERE up.UserID = ? AND q.Role = 'top management'
            GROUP BY q.Category
            ORDER BY q.Category
        ";
        break;

    case 'employee':
        $query = "
            SELECT 
                COALESCE(q.Category, 'Unknown') AS Category, 
                COUNT(up.SkillID) AS TotalAttempts, 
                SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) AS CorrectAnswers,
                ROUND(
                    (SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(up.SkillID), 0)) * 100, 2
                ) AS Accuracy
            FROM userperformances up
            JOIN questions q ON up.SkillID = q.SkillID
            WHERE up.UserID = ? AND q.Role = 'employee'
            GROUP BY q.Category
            ORDER BY q.Category
        ";
        break;

    case 'student':
    default:
        $query = "
            SELECT 
                COALESCE(q.Category, 'Unknown') AS Category, 
                COUNT(up.SkillID) AS TotalAttempts, 
                SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) AS CorrectAnswers,
                ROUND(
                    (SUM(CASE WHEN up.Score = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(up.SkillID), 0)) * 100, 2
                ) AS Accuracy
            FROM userperformances up
            JOIN questions q ON up.SkillID = q.SkillID
            WHERE up.UserID = ?
            GROUP BY q.Category
            ORDER BY q.Category
        ";
        break;
}


// Prepare and execute the statement
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if any data is returned
if ($result->num_rows > 0) {
    $performances = [];
    while ($row = $result->fetch_assoc()) {
        $performances[] = $row;
    }
} else {
    $performances = [];
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Performance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Performance Details</h1>
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Your role: <?php echo htmlspecialchars($role); ?></p>
        <?php if (count($performances) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total Attempts</th>
                        <th>Correct Answers</th>
                        <th>Accuracy (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($performances as $performance): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($performance['Category']); ?></td>
                            <td><?php echo htmlspecialchars($performance['TotalAttempts']); ?></td>
                            <td><?php echo htmlspecialchars($performance['CorrectAnswers']); ?></td>
                            <td><?php echo htmlspecialchars($performance['Accuracy']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No performance data found for this user.</p>
        <?php endif; ?>
    </div>
</body>
</html>
