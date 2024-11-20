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

$recommendations = [];
$skillsTested = []; // Array to store tested skills

// Define learning resources based on performance
while ($row = $result->fetch_assoc()) {
    $skillID = $row['SkillID'];
    $score = $row['Score'];

   // Fetch skill category based on SkillID
$skillQuery = "SELECT Category FROM skills WHERE SkillID = ?";
$skillStmt = $conn->prepare($skillQuery);
$skillStmt->bind_param("i", $skillID);
$skillStmt->execute();
$skillResult = $skillStmt->get_result();
$skillRow = $skillResult->fetch_assoc();
$skillCategory = $skillRow['Category']; // Use 'Category' here


    // Add the skill to the tested skills array
    $skillsTested[] = ['SkillID' => $skillID, 'SkillName' => $skillCategory];

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

    // Determine recommendations based on percentage
    if ($percentage < 50) {
        $recommendations[] = [
            'skillName' => $skillCategory, // Associate recommendations with skill name
            'type' => 'Improve',
            'videos' => [
                ['title' => 'Just a Password is enough to get in!', 'url' => 'https://www.youtube.com/embed/wtCDiS-mZQQ'],
                ['title' => 'Cybersecurity Basics', 'url' => 'https://www.youtube.com/embed/njPY7pQTRWg'],
                ['title' => 'Ever heard of Cyberwarfare?', 'url' => 'https://youtube.com/embed/U_7CGl6VWaQ']
            ],
            'articles' => [
                ['title' => 'The Importance of Strong Passwords', 'url' => 'https://example.com/strong-passwords'],
                ['title' => 'Cybersecurity Fundamentals', 'url' => 'https://example.com/cybersecurity-fundamentals']
            ],
            'courses' => [
                ['title' => 'Introduction to Cybersecurity', 'url' => 'https://example.com/cybersecurity-course']
            ]
        ];
    } elseif ($percentage >= 60 && $percentage < 80) {
        $recommendations[] = [
            'skillName' => $skillCategory,
            'type' => 'Good Job! Here’s some extra learning!',
            'videos' => [
                ['title' => 'Advanced Password Management Techniques', 'url' => 'https://www.youtube.com/embed/example'],
                ['title' => 'Best Practices in Network Security', 'url' => 'https://www.youtube.com/embed/example2']
            ],
            'articles' => [
                ['title' => 'Enhancing Your Network Security Skills', 'url' => 'https://example.com/network-security-skills']
            ],
            'courses' => [
                ['title' => 'Intermediate Cybersecurity', 'url' => 'https://example.com/intermediate-cybersecurity']
            ]
        ];
    } elseif ($percentage >= 80) {
        $recommendations[] = [
            'skillName' => $skillCategory,
            'type' => 'Excellent Work! Level Up Your Skills!',
            'videos' => [
                ['title' => 'Mastering Cybersecurity Essentials', 'url' => 'https://www.youtube.com/embed/example3'],
            ],
            'articles' => [
                ['title' => 'Future Trends in Cybersecurity', 'url' => 'https://example.com/future-trends']
            ],
            'courses' => [
                ['title' => 'Advanced Cybersecurity Strategies', 'url' => 'https://example.com/advanced-cybersecurity']
            ]
        ];
    }

    $totalStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Learning Path</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General reset and styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Navbar styling */
        nav {
            display: flex;
            justify-content: center;
            background-color: #1e1e1e;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        nav a {
            color: #e0e0e0;
            padding: 10px 20px;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
            font-size: 1.1rem;
            border-radius: 30px;
            transition: background 0.3s, transform 0.3s;
        }

        nav a:hover {
            background-color: #76c7c0;
            color: #121212;
            transform: scale(1.05);
        }

        /* Recommendations container styling */
        .recommendation-container {
            margin: 40px auto;
            padding: 30px;
            background-color: #f4f4f4;
            border-radius: 12px;
            max-width: 1100px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
            /*transition: transform 0.3s ease;*/
        }

        .recommendation-container:hover {
            /*transform: translateY(-5px);*/
        }

        h2 {
            color: #76c7c0;
            margin-bottom: 20px;
            font-size: 1.0rem;
        }

        /* Kebab menu styling */
        .kebab-menu {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 30px;
            color: #76c7c0;
            cursor: pointer;
        }

        .kebab-menu:hover {
            transform: translateY(-5px);
        }

        .skills-list {
            display: none;
            position: absolute;
            top: 35px;
            right: 0;
            background-color: #666;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(1, 1, 1, 1);
            padding: 15px;
            color: #666;
        }

        .skills-list ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .skills-list ul li {
            margin: 10px 0;
        }

        .skills-list ul li a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
         
        }

        .skills-list ul li a:hover {
            color: #64b5b0;
        }

        /* Grid system */
        .video-container, .article-container, .course-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Video, article, and course cards */
        .video, .article, .course {
            background-color:#040708;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            box-shadow: 0px 6px 14px rgba(0, 0, 0, 0.4):
        }

        .video:hover, .article:hover, .course:hover {
            background-color: white;
        }

        .video p, .article a, .course a {
            margin-top: 10px;
            font-size: 1rem;
            color: #39696d;
            text-decoration: none;
        }

        .video iframe {
            width: 100%;
            height: 160px;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .video, .article, .course {
                grid-column: span 2;
            }
        }

        @media (max-width: 480px) {
            .video, .article, .course {
                grid-column: span 1;
            }
        }
    </style>
</head>

<body>

    <h1>Personal Learning Path</h1>

    <div class="kebab-menu" id="kebabMenu">
        &#x22EE;
        <div class="skills-list" id="skillsList">
            <ul>
                <?php foreach ($skillsTested as $skill): ?>
                    <li><a href="#<?php echo strtolower($skill['SkillName']); ?>"><?php echo $skill['SkillName']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php foreach ($recommendations as $recommendation): ?>
    <div class="recommendation-container" id="<?php echo strtolower($recommendation['skillName']); ?>">
        <h2><?php echo $recommendation['skillName']; ?>: <?php echo $recommendation['type']; ?></h2>

        <div class="video-container">
            <?php foreach ($recommendation['videos'] as $video): ?>
                <div class="video">
                    <iframe src="<?php echo $video['url']; ?>" allowfullscreen></iframe>
                    <p><?php echo $video['title']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="article-container">
            <?php foreach ($recommendation['articles'] as $article): ?>
                <div class="article">
                    <a href="<?php echo $article['url']; ?>" target="_blank"><?php echo $article['title']; ?></a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="course-container">
            <?php foreach ($recommendation['courses'] as $course): ?>
                <div class="course">
                    <a href="<?php echo $course['url']; ?>" target="_blank"><?php echo $course['title']; ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <script>
        // Kebab Menu Toggle
        const kebabMenu = document.getElementById('kebabMenu');
        const skillsList = document.getElementById('skillsList');

        kebabMenu.addEventListener('click', () => {
            skillsList.style.display = skillsList.style.display === 'none' || skillsList.style.display === '' ? 'block' : 'none';
        });

        // Hide skills menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!kebabMenu.contains(event.target)) {
                skillsList.style.display = 'none';
            }
        });
    </script>

</body>

</html>
