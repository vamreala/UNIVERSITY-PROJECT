<?php
session_start();

// Include database connection file
require_once "db_connection.php";

// Initialize the feedback message variable
$feedbackMessage = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if an answer has been selected
    if (isset($_POST['answer_index']) && isset($_POST['correct_answer_index']) && isset($_POST['question_id'])) {
        // Retrieve the selected answer index, correct answer index, and question ID from the form
        $selectedAnswerIndex = intval($_POST['answer_index']);
        $correctAnswerIndex = intval($_POST['correct_answer_index']);
        $questionID = $_POST['question_id'];

        // Store the answered question ID in the session
        $_SESSION['answered_questions'][] = $questionID;

        // Compare the selected answer index with the correct answer index
        if ($selectedAnswerIndex === $correctAnswerIndex) {
            $feedbackMessage = '<span style="color: green;">&#10004; Correct answer!</span>';
        } else {
            $feedbackMessage = '<span style="color: red;">&#10008; Incorrect answer.</span>';
        }
    } else {
        // Required data missing
        $feedbackMessage = '<span style="color: red;">Invalid form submission. Please try again.</span>';
    }
}

// Return the feedback message as JSON
$jsonResponse = json_encode(["feedbackMessage" => $feedbackMessage]);
header("Content-Type: application/json");
echo $jsonResponse;
// End of PHP script
?>
