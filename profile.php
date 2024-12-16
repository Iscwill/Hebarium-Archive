<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "PlantBiodiversity";
$port = 3307;

// Create a database connection
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$userData = [];
$profileImage = 'img/profile_images/default.png'; // Default profile image

// Fetch logged-in user email from session
$userEmail = $_SESSION['email'];

// Retrieve user data from the database
$stmt = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc(); // Fetch user data into an associative array

    // Check if the user has a profile image, otherwise use default based on gender
    if (!empty($userData['profile_image']) && file_exists($userData['profile_image'])) {
        $profileImage = htmlspecialchars($userData['profile_image']);
    } else {
        // Set default profile image based on gender
        $profileImage = ($userData['gender'] === 'Male') ? 'img/profile_images/boys.jpg' : 'img/profile_images/girl.png';
    }
} else {
    // If user data not found, redirect to an error page or logout
    header("Location: logout.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>

    <!-- STYLE CSS LINK -->
    <link rel="stylesheet" href="style/style.css">
    <!-- BOOTSTRAP CDN LINK -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- GOOGLE FONTS LINK -->
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap" rel="stylesheet">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="profile-wrapper">
        <div class="container-profile">
            <div class="profile-card">
                <div class="profile-image">
                    <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="profile-image">
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($userData['first_name'] . " " . $userData['last_name']); ?></h1>
                    <p><strong>Student ID:</strong> 102769620</p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                </div>
            </div>

            <div class="declaration">
                <p>
                    “I declare that this assignment is my individual work. I have not worked collaboratively nor have I
                    copied from any other student's work or from any other source. I have not engaged another party to
                    complete this assignment. I am aware of the University’s policy with regards to plagiarism. I have
                    not allowed, and will not allow, anyone to copy my work with the intention of passing it off as his
                    or her own work.”
                </p>
            </div>

            <div class="profile-buttons mt-3 d-flex justify-content-center text-center">
                <div class="m-3">
                    <a href="index.php" class="classify-button">Home <span class="arrow">&rarr;</span></a>
                </div>
                <div class="m-3">
                    <a href="about.php" class="classify-button">About<span class="arrow">&rarr;</span></a>
                </div>
                <div class="m-3">
                    <a href="main_menu.php" class="classify-button">Back<span class="arrow">&rarr;</span></a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>