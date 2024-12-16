<?php
session_start();
$errors = [];
$email_error = '';
$password_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate email and password fields
    if (empty($email)) $email_error = "Email is required.";
    if (empty($password)) $password_error = "Password is required.";

    // If no validation errors, proceed with login check
    if (empty($email_error) && empty($password_error)) {
        // Retrieve user data from the database
        $user = get_user_by_email($email);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Login successful - set session variables
                $_SESSION['email'] = $email;
                $_SESSION['type'] = $user['type'];

                // Redirect based on user type
                if ($user['type'] === 'admin') {
                    header("Location: main_menu_admin.php"); // Admin main menu
                } else {
                    header("Location: main_menu.php"); // User main menu
                }
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "Email not found.";
        }
    }
}

// Function to retrieve user data by email from the database
function get_user_by_email($email) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "PlantBiodiversity";
    $port = 3307;

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $database, $port);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Query to fetch user data
    $stmt = $conn->prepare("SELECT u.email, a.password, a.type FROM user_table u JOIN account_table a ON u.email = a.email WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_info = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user_info;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Herbarium Archive</title>

    <!-- BOOTSTRAP CDN LINK -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- STYLE CSS LINK -->
    <link rel="stylesheet" href="style/style.css">
    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- GOOGLE FONTS LINK -->
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="centered-container-login">
        <h2 class="deco-title">Herbarium Archive</h2>
        <h2 class="deco-title1">Herbarium Archive</h2>
        <h2 class="deco-title2">Herbarium Archive</h2>
        <div class="login-container">

            <!-- Logo Section -->
            <div class="login-logo">
                <img src="./img/logo.png" alt="Herbarium Archive Logo">
            </div>

            <!-- Login Title -->
            <div class="login-title">Login to Herbarium Archive</div>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
            <div class="text-danger">
                <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="login.php" method="POST">
                <!-- Email Address Field -->
                <div class="email_form">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    <?php if (!empty($email_error)): ?>
                    <small class="text-danger"><?php echo $email_error; ?></small>
                    <?php endif; ?>
                </div>

                <!-- Password Field -->
                <div class="password_form">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <?php if (!empty($password_error)): ?>
                    <small class="text-danger"><?php echo $password_error; ?></small>
                    <?php endif; ?>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-button">Login</button>
            </form>

            <!-- Register Link -->
            <div class="mt-3">
                <p>Don't have an account? <a href="registration.php" class="register-button">Register here</a></p>
            </div>

            <!-- Go Back Button -->
            <div class="mt-4 text-center">
                <a href="index.php" class="classify-button">Go Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>