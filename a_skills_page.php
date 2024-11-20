<?php
session_start();
require_once "db_connection.php";

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

$currentUserId = $_SESSION['UserID'];
$userRole = $_SESSION['Role']?? 'default'; // Assuming Role is stored in the session when logging in


// Retrieve the selected skill and difficulty level from the URL query parameters
$level = $_GET['level'] ?? '';
$skill = $_GET['skill'] ?? '';

// Title for the page
$pageTitle = isset($skill) ? ucwords($skill) . " - " . $level : "Page Title";

// Initialize an empty array for correct answers
$correctAnswers = [];

// Fetch all questions for the selected skill and difficulty level
$conn = connectToDatabase();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT QuestionID, QuestionText, Option1, Option2, Option3, Option4, CorrectAnswer, Explanation FROM questions WHERE Category = ? AND Difficulty = ?";
$stmt = $conn->prepare($sql);

$questions = [];

if ($stmt) {
    if (!empty($skill) && !empty($level)) {
        $stmt->bind_param("ss", $skill, $level);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $questions[] = $row;
                $correctAnswers[$row['QuestionID']] = $row['CorrectAnswer'];
            }
        } else {
            $questions[] = ["QuestionText" => "No questions found for $skill at the $level level."];
        }
    } else {
        $questions[] = ["QuestionText" => "Skill or level parameter is missing."];
    }

    $stmt->close();
} else {
    $questions[] = ["QuestionText" => "Error preparing statement: " . $conn->error];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin: auto; padding: 20px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .question { margin-bottom: 20px; }
        .option { margin-bottom: 10px; }
        .submit-btn { margin-top: 20px; }
        .popup { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(0, 0, 0, 0.8); color: #fff; padding: 20px; border-radius: 8px; z-index: 1000; text-align: center; }
        .popup.show { display: block; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center"><?php echo $pageTitle; ?></h1>

    <?php foreach ($questions as $question) : ?>
        <div class="question">
            <?php if (isset($question['QuestionText'])) : ?>
                <p><strong>Question:</strong> <?php echo $question['QuestionText']; ?></p>
                <form class="answer-form" method="post">
                    <input type="hidden" name="question_id" value="<?php echo $question['QuestionID']; ?>">
                    <div class="options">
                        <?php for ($i = 1; $i <= 4; $i++) : ?>
                            <?php if (isset($question["Option$i"])) : ?>
                                <div class="option form-check">
                                    <input type="radio" name="answers[<?php echo $question['QuestionID']; ?>]" value="<?php echo htmlspecialchars($question["Option$i"]); ?>" id="option<?php echo $i; ?>_<?php echo $question['QuestionID']; ?>" class="form-check-input">
                                    <label for="option<?php echo $i; ?>_<?php echo $question['QuestionID']; ?>" class="form-check-label"><?php echo htmlspecialchars($question["Option$i"]); ?></label>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </form>
            <?php else : ?>
                <p>No question text available.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <button type="button" class="btn btn-primary submit-btn" onclick="submitAnswers()">Submit All Answers</button>
</div>

<div id="feedbackPopup" class="popup"></div>

<script>
// Pass PHP values to JavaScript
var correctAnswers = <?php echo json_encode($correctAnswers); ?>;
var userRole = "<?php echo $userRole; ?>"; // Get user role from session

function submitAnswers() {
    var answers = {};
    document.querySelectorAll('.answer-form').forEach(function(form) {
        var questionId = form.querySelector('input[name="question_id"]').value;
        var selectedAnswer = form.querySelector('input[type="radio"]:checked');
        if (selectedAnswer) {
            answers[questionId] = selectedAnswer.value;
        }
    });

    var requestData = new URLSearchParams();
    requestData.append('user_id', <?php echo $currentUserId; ?>);
    for (var questionId in answers) {
        requestData.append('answers[' + questionId + ']', answers[questionId]);
    }

    fetch('submit_performance.php', {
        method: 'POST',
        body: requestData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        showThankYouPopup();
    })
    .catch(error => {
        console.error('There was an error:', error);
    });
}

function showThankYouPopup() {
    var popup = document.getElementById("feedbackPopup");
    popup.innerHTML = `
        <p>Your responses have been recorded and analyzed. You will be redirected to see your scores shortly.</p>
        <button id="popupCloseButton">Continue</button>
    `;
    popup.style.display = "block";

    document.getElementById('popupCloseButton').addEventListener('click', function() {
        // Redirect based on user role
        switch (userRole) {
            case 'teacher':
                window.location.href = 'teacher_dashboard.php';
                break;
            case 'student':
                window.location.href = 'student_dashboard.php';
                break;
            case 'auditor':
                window.location.href = 'auditor_dashboard.php';
                break;
            case 'pen tester':
                window.location.href = 'pen_tester_dashboard.php';
                break;
            case 'top management':
                window.location.href = 'topmngt_dashboard.php';
                break;
            case 'employee':
                window.location.href = 'employee_dashboard.php';
                break;
            // Add more cases as needed
            default:
                window.location.href = 'a_tp.php';
                break;
        }
    });
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

