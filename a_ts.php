<?php
session_start();
require_once "db_connection.php";

//check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Function to fetch skills and images from the database based on user's level
function getSkillsFromDatabase($level, $role) {
    // Establish database connection using the existing function
    $conn = connectToDatabase();

    // Prepare and execute query to fetch distinct skills and images based on user's level-- AND ROLE
    $sql = "SELECT DISTINCT s.Category AS name, s.image 
            FROM skills s 
            JOIN questions q ON s.Category = q.Category 
            WHERE q.Difficulty = ? AND q.Role = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $level, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize array to store skills and images
    $skills = array();

    // Fetch skills and images from the result set
    while ($row = $result->fetch_assoc()) {
        // Assuming the image column contains binary data
        $imageData = $row['image'];
        // Convert binary data to base64 encoded string
        $imageBase64 = base64_encode($imageData);
        
        // Store skill name and base64 encoded image string in array
        $skills[] = array(
            'name' => $row['name'],
            'image' => $imageBase64
        );
    }

    // Close statement and database connection
    $stmt->close();

    // Return the array of skills and images
    return $skills;
}

// Fetch skills for each level
$role = 'auditor'; //----BE CHANGING THIS EVERYTIME FOR DIFFERENT ROLES
$beginnerSkills = getSkillsFromDatabase('beginner', $role);
$intermediateSkills = getSkillsFromDatabase('intermediate', $role);
$expertSkills = getSkillsFromDatabase('expert', $role);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditor Skills Test</title>
    <link rel="stylesheet" href="ts_style.css">
</head>
<body> <br>
    <div class="container">
        <br> <h1><u>Auditor</u> Skills Test</h1> <br> 

        <!-- Beginner Level Skills -->
        <div class="skill-section">
            <h2>Beginner Level</h2> <br> 
            <div class="skill-grid">
                <?php foreach ($beginnerSkills as $skill) : ?>
                    <div class="skill-card" onclick="location.href='a_skills_page.php?level=beginner&skill=<?php echo urlencode($skill['name']); ?>'">
                        <img src="data:image/png;base64,<?php echo $skill['image']; ?>" alt="<?php echo $skill['name']; ?>">
                        <div class="skill-name"><?php echo $skill['name']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Intermediate Level Skills -->
        <div class="skill-section">
            <h2>Intermediate Level</h2> <br>
            <div class="skill-grid">
                <?php foreach ($intermediateSkills as $skill) : ?>
                    <div class="skill-card" onclick="location.href='a_skills_page.php?level=intermediate&skill=<?php echo urlencode($skill['name']); ?>'">
                        <img src="data:image/png;base64,<?php echo $skill['image']; ?>" alt="<?php echo $skill['name']; ?>">
                        <div class="skill-name"><?php echo $skill['name']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Expert Level Skills -->
        <div class="skill-section">
            <h2>Expert Level</h2> <br>
            <div class="skill-grid">
                <?php foreach ($expertSkills as $skill) : ?>
                    <div class="skill-card" onclick="location.href='a_skills_page.php?level=expert&skill=<?php echo urlencode($skill['name']); ?>'">
                        <img src="data:image/png;base64,<?php echo $skill['image']; ?>" alt="<?php echo $skill['name']; ?>">
                        <div class="skill-name"><?php echo $skill['name']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
