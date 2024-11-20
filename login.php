<?php
session_start();
require_once "db_connection.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Clear existing session data
session_unset();

// Initialize error message variable
$errorMessage = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Perform server-side validation (e.g., check if fields are empty)
    if (empty($username) || empty($password)) {
        echo "Both username and password are required.";
    } else {
        // Query database to verify user credentials
        $conn = connectToDatabase();
        $sql = "SELECT * FROM Users WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            // User found, verify password
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["PasswordHash"])) {
                // Retrieve user's role from the database
                $userID = $row["UserID"];
                $role = $row["Role"];
                $joinDate = $row["CreatedAt"];

                // Set session variables
                $_SESSION["UserID"] = $userID;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $role;
                $_SESSION["joinDate"] = $joinDate;

                //debugging statement
                echo "Session UserID:" . $_SESSION["UserID"]; // This should print the UserID


                // Redirect to home.php with username and role parameters
                header("Location: home.php?username=$username&role=$role");
                exit();
            } else {
                $errorMessage = "Incorrect password.";
            }
        } else {
            $errorMessage = "User not found.";
        }

        // Close the database connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybersecurity Tester - Login</title>
    <link rel="stylesheet" href="signup_in.css">
</head>
<body class="body" style="font-family: 'Roboto', sans-serif;background-image: url(cysera_login_wallpaper.png);background-size: cover;background-repeat: no-repeat;background-position: center;background-color: #121212; margin: 0; padding: 0;">
    <div class="container">
        <h2 class="h2">Sign-In</h2>
        <form id="loginForm" action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <p id="errorMessage" class="error-message">Incorrect username or password.</p> <!-- Error message element -->
            <p>Don't have an account? <a href="landing_page.html">Sign up</a></p>
        </form>
    </div>

    <div class="login_foooter" style="margin: 50px auto; background-color: #121212; max-height: 100px; max-width: 400px; justify-content: center;">
    <img src="cysera_full_logo.png" alt="CySERA" class="footer_img" style="height:100px; width:400px;position:center;" >
    </div>
    
    <script>
    // Function to fade in the error message
    function fadeInErrorMessage() {
        var errorMessage = document.getElementById("errorMessage");
        errorMessage.style.opacity = 0;
        errorMessage.style.transition = "opacity 0.5s";
        errorMessage.style.display = "block";
        setTimeout(function() {
            errorMessage.style.opacity = 1;
        }, 100);
    }

    // Function to fade out the error message
    function fadeOutErrorMessage() {
        var errorMessage = document.getElementById("errorMessage");
        errorMessage.style.transition = "opacity 0.5s";
        errorMessage.style.opacity = 0;
        setTimeout(function() {
            errorMessage.style.display = "none";
        }, 500);
    }

    // Display the error message with animation
    if ("<?php echo $errorMessage; ?>" !== "") {
        fadeInErrorMessage();
        setTimeout(function() {
            fadeOutErrorMessage();
        }, 3000); // Adjust the duration as needed (e.g., 3000 milliseconds = 3 seconds)
    }
</script>



    <script src="script.js"></script>
</body>
</html>
