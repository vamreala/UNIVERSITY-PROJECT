<?php
// Start the session
session_start();

// Check if the user is logged in
if(isset($_SESSION['username']) && isset($_SESSION['role'])) {
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];
} else {
    // Redirect the user to the login page if not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybersecurity Tester - Home</title>
    <link rel="stylesheet" href="homestyle.css">
    <link rel="stylesheet" href="profile_style.css">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="pen_tester_style.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
</head>
<body >

<div style="padding: 50px;">
<h1 style="color: #76c7c0; text-align:center; margin-top: 20px;"> CySERA </h1>
</div>

        <div class="logout-btn" style="background-color: #76c70; position: sticky; ">
            <ul>
            <li class="nav-item"> <a href="blog.html" class="nav-link active"> BLOG </a> </li>
            <li class="nav-item"> <a href="#" class="nav-link active" onclick="redirectToDashboard()">Dashboard</a></li> 
            <li class="nav-item"> <a href="profile.php" class="nav-link active"> Profile </a> </li>
            <li class="nav-item"> <a href="a_plep.php" class="nav-link active"> MyLearning </a> </li>
            </ul>
        </div>
    
<div class="container">
    <!-- Top div with heading and welcome message -->
    <div class="left-container" style="text-align: justify;" >
            <img src="image.png" alt="Profile Icon" class="profile-icon" style="height:100px; width:100px; border-radius: 20px;">
            <div class="user-info">
               <p class="welcome-message" style="color: #0e0606;" id="usernameDisplay"> Welcome, <?php echo $username;?>!</p>
               <p style=" color:#0e0606;"><strong>Your Role🤩:</strong> <?php echo $role; ?></p>
            </div>
    </div>

    <div class="right-container">
            <div class="profile-details">
                <h2 class="section-heading" style="text-align: center;">NEWS!</h2>
                <p class="update-message" style="text-align: center;" >You are up to date! Stay tune for new Skills to practice! </p>
                <p class="update-message" style="text-align: center;" >For more Cybersecurity Content visit Our Blog..</p>
            </div>
    </div>
 </div>
 </div>
    
        <!-- Display the profile picture based on the user's role -->
        <?php
            // Set the profile picture based on the user's role
            $profile_picture = 'defaulticon.png'; // Default profile picture
            switch ($role) {
                case 'pen tester':
                    $profile_picture = 'pentestericon.png'; // Pen tester icon
                    break;
                case 'auditor':
                    $profile_picture = 'auditoricon.png'; // auditor icon
                    break;
                case 'top management':
                    $profile_picture = 'studenticon.png';
                    break;
                case 'teacher':
                    $profile_picture = 'teachericon.png';
                    break;
                case 'employee':
                    $profile_picture = 'employeeicon.png';
                    break;
                case 'student':
                    $profile_picture = 'studenticon.png';
                    break;
    
                // Add more cases for other roles if needed
            }
            ?>

            <script>
            //THIS FUNCTION REDIRECTS THE USER TO THE DASHBOARD BASED ON THEIR ROLE---
        function redirectToDashboard() {
        switch ("<?php echo $role; ?>") {
            case 'teacher':
                window.location.href = 'teacher_dashboard.php'; // Redirect to teacher dashboard
                break;
            case 'student':
                window.location.href = 'student_dashboard.php'; // Redirect to student dashboard
                break;
            case 'auditor':
                window.location.href = 'auditor_dashboard.php'; // Redirect to auditor dashboard
                break;
            case 'employee':
                window.location.href = 'employee_dashboard.php'; // Redirect to employee dashboard
                break;
            case 'top management':
                window.location.href = 'topmngt_dashboard.php'; // Redirect to top management dashboard
                break;
            case 'pen tester':
                window.location.href = 'pen_tester_dashboard.php'; // Redirect to pen tester dashboard
                break;
            default:
                console.log("No matching role found");
                break;
        }
    }
    </script>
           
        </div>
    </div>
    
    <!-- Main container with three sections ...just MOVED THE LEFT CONTAINER TO THE TOP DIV -->
    <div class="container" style=" flex: 1; max-width: 100%; margin-left: auto; margin-right: auto; display: flex; text-align: left;">
                <h2 class="section-heading">What Is In The <u>Blog</u></h2>
                <p class="fact" style="">Millions of users are not aware of the importance of Cybersecurity Education. Users without the knowledge of Cybersecurity become the weakest link in the security chain. However, this does not mean that users with the knowledge, behave differently from those without. Organizations are advised to train their shareholders thus reducing threats that may lead to certain losses. </p>
                <p class="fact" style="">Did you know that other Cybersecurity platforms are not tailored for each and every individual in the cybersecurity sector? But who are this people directly involved in the Cybersecurity industry? <br> In The Blog you get to know what is happening in the industry! Which is better than Our Team sending you weekly emails every time! What are you waiting for... <a href="blog.html"> VISIT OUR BLOG </a></p> 
            
    </div>      
        <!--Dashboard -->
    <div class="container" style=" flex: 1; max-width: 100%; margin-left: auto; margin-right: auto; display: flex; text-align: left;">
        <div class="right-container" style="flex: 1; padding: 20px; background-color: #1e1e1e">
            
                <h2 class="section-heading" style="text-align: left;">What Is in The <u> Dashboard</u></h2>   
                <p class="fact" style="text-align:justify;">Every role in the cybersecurity industry requires its own space to expand, that is why we have The Dashboard personalised for every type of user enrolled to CySERA. Here you get to:  <li style="text-align:justify;">Test yourself 🧪</li><li style="text-align:justify;">Evaluate yourself ⚖️</li><li style="text-align:justify;">Strengthen yourself 💪</li></p>
                            
        </div>
    </div>
        <!--MyLearning -->
    <div class="container" style=" flex: 1; max-width: 100%; margin-left: auto; margin-right: auto; display: flex; text-align: left;">
        <div class="right-container" style="flex: 1; padding: 20px; background-color: #1e1e1e">
                <h2 class="section-heading" style="text-align: left;">What Do I Do In <u>MyLearning</u></h2>   
                <p class="fact" style="text-align:justify;">This is where you get your personalized content based on your Skill performance. <br>The more you test yourself the more the content!  <br>You can: <li style="text-align:justify;">Add content To Cart🧺</li><li style="text-align:justify;">Get Personalized Reminders 🔔</li><li style="text-align:justify;">Request for more content ➕🙏</li></p>
       </div>
    </div>

    <div class="container" style=" flex: 1; max-width: 100%; margin-left: auto; margin-right: auto; display: flex; justify-content: center;">
    <div class="right-container" style="flex: 1; padding: 20px; background-color: #1e1e1e">
                <h2 class="section-heading" style="text-align: center;">Live Users</h2>
                <p class="fact" style="text-align:center;">NUMBER OF USERS ENROLLED IN CySERA <h1>5+</h1></p>
       </div>
    <div class="right-container" style="flex: 1; padding: 20px; background-color: #1e1e1e">
                <h2 class="section-heading" style="text-align: center;">New on CySERA</h2>   
                <p class="fact" style="text-align:center;">NUMBER OF SKILLS IN CySERA <h1>100+</h1></p>
       </div>
    <div class="right-container" style="flex: 1; padding: 20px; background-color: #1e1e1e">
                <h2 class="section-heading" style="text-align: center;">Questions in CySERA</h2>   
                <p class="fact" style="text-align:center;">NUMBER OF QUESTIONS IN CySERA <h1>1K+</h1></p>
       </div>
    </div>

    <!-- JavaScript to display username and set profile icon based on role -->
    <script>
        // Function to extract URL query parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Get the username and role from the URL query parameters
        var username = getUrlParameter('username');
        var role = getUrlParameter('role');
        console.log('Role:', role); // Add this line to log the role value

        // Display the username on the page
        var usernameDisplay = document.getElementById('usernameDisplay');
        usernameDisplay.textContent = "Welcome, " + username + "!";

        // Set the profile icon based on the user's role
        var profileIcon = document.querySelector('.profile-icon');
        switch (role) {
            case 'teacher':
                console.log("Setting profile icon for teacher");
                profileIcon.src = 'teachericon.png'; // Replace 'teachericon.png' with the path to your teacher icon
                break;
            case 'student':
                profileIcon.src = 'studenticon.png'; // Replace 'studenticon.png' with the path to your student icon
                break;
            case 'auditor':
                profileIcon.src = 'auditoricon.png'; // Replace 'auditoricon.png' with the path to your auditor icon
                break;
            case 'employee':
                profileIcon.src = 'employeeicon.png'; // Replace 'employeeicon.png' with the path to your employee icon
                break;
            case 'top management':
                profileIcon.src = 'topmngticon.png'; // Replace 'employeeicon.png' with the path to your employee icon
                break;
            case 'pen tester':
                profileIcon.src = 'pentestericon.png'; // Replace 'employeeicon.png' with the path to your employee icon
                break;
            default:
                console.log("No matching role found, using default icon");
                profileIcon.src = 'image.png'; // Replace 'image.png' with the path to your default icon
        }
    </script>
</body>
</html>
