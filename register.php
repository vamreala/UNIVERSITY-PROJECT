<?php
require_once "db_connection.php";

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $roles = $_POST["role"] ?? array(); // Retrieve selected roles as an array
    

    // Perform server-side validation (e.g., check if fields are empty)
    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
    } else {
        // Establish database connection
        $conn = connectToDatabase();

        // Check if username or email already exists
        $checkSql = "SELECT * FROM Users WHERE Username = ? OR Email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $checkResult = $stmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Display custom popup
            echo "<script>
                    alert('Seems like you are not new here.');
                    setTimeout(function() {
                        window.location.href = 'login.php'; // Redirect to login page
                    }, 3000);
                  </script>";
        } else {
            // Hash the password for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
            // Prepare roles data for insertion
            $rolesString = implode(", ", $roles); // Convert array of roles to comma-separated string
            
            //Insert user data into database
            $sql = "INSERT INTO Users (Username, PasswordHash, Email, Role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $hashedPassword, $email, $rolesString);

            if ($stmt->execute()) {
                // Registration successful, redirect back to registration page
                header("Location: login.php");
                exit(); // Stop further script execution
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
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
    <title>Cybersecurity Tester - Register</title>
    <link rel="stylesheet" href="signup_in.css">
</head>
<body>


    <div class="container" style="margin-top: 20px;">
    <p class="top" id="animated-text">Over 450k users have enrolled to <u>CySERA</u></p>

        <form id="registerForm" action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter Full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="somebody@gmail.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="use a STRONG password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>
            <div class="form-group">
                <label>Role:
                <span class="additional-info">Pick one Role for your Account. Failure to which you will not have a good user experience!</span>
                </label><br>
                <div class="checkbox-group">
                    <div>
                        <input type="checkbox" id="student" name="role[]" value="student">
                        <label for="student">Student</label>
                    </div>
                    <div>
                        <input type="checkbox" id="teacher" name="role[]" value="teacher">
                        <label for="teacher">Teacher</label>
                    </div>
                    <div>
                        <input type="checkbox" id="auditor" name="role[]" value="auditor">
                        <label for="auditor">Auditor</label>
                    </div>
                    <div>
                        <input type="checkbox" id="pen tester" name="role[]" value="pen tester">
                        <label for="pen tester">Pen Tester</label>
                    </div>
                    <div>
                        <input type="checkbox" id="top management" name="role[]" value="top management">
                        <label for="top management">Top Management</label>
                    </div>
                    <div>
                        <input type="checkbox" id="employee" name="role[]" value="employee">
                        <label for="employee">Employee</label>
                    </div>
                </div>
            </div>      
            <button type="submit">Register</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
