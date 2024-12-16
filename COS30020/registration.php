<?php
session_start();
$errors = [];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and validate form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $hometown = trim($_POST['hometown'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $contact_number = trim($_POST['contact_number'] ?? '');

    // Validate fields
    if (empty($first_name)) $errors['first_name'] = 'First Name is required.';
    if (empty($last_name)) $errors['last_name'] = 'Last Name is required.';
    if (empty($dob)) $errors['dob'] = 'Date of Birth is required.';
    if (empty($gender)) $errors['gender'] = 'Gender is required.';
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }
    if (empty($password)) {
    $errors['passwordErr'] = "Password is required";
} elseif (!preg_match("/^(?=.*\d)(?=.*[!@#$%^&])[a-zA-Z\d!@#$%^&]{8,}$/", $password)) {
    $errors['passwordErr'] = "Password must be at least 8 characters, include a number, and a symbol";
}

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }
    if (!empty($contact_number) && !preg_match('/^\d{10,15}$/', $contact_number)) {
        $errors['contact_number'] = 'Contact number must be between 10 and 15 digits.';
    }

    // Determine default profile image based on gender
    $profileImagePath = null;
    if ($gender === "Male") {
        $profileImagePath = "img/profile_images/boys.jpg"; // Default boys image
    } elseif ($gender === "Female") {
        $profileImagePath = "img/profile_images/girl.png"; // Default girls image
    }

    // If no errors, process registration
    if (empty($errors)) {
        $servername = "localhost";
        $username = "root";
        $db_password = ""; // Database password
        $port = 3307;
        $conn = new mysqli($servername, $username, $db_password, "PlantBiodiversity", $port);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT email FROM user_table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['email'] = 'A user with this email already exists.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into user_table
            $stmt = $conn->prepare("INSERT INTO user_table (email, first_name, last_name, dob, gender, contact_number, hometown, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $email, $first_name, $last_name, $dob, $gender, $contact_number, $hometown, $profileImagePath);

            if ($stmt->execute()) {
                // Insert into account_table
                $stmt = $conn->prepare("INSERT INTO account_table (email, password, type) VALUES (?, ?, ?)");
                $type = 'user'; // Default to user
                $stmt->bind_param("sss", $email, $hashedPassword, $type);

                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Registration successful!';
                    header("Location: login.php");
                    exit();
                } else {
                    $errors['general'] = 'Error saving account information: ' . $stmt->error;
                }
            } else {
                $errors['general'] = 'Error saving user information: ' . $stmt->error;
            }
        }

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
    <title>Herbarium Archive - Main Menu</title>
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
    <div class="centered-container-update">
        <h2 class="deco-title">Herbarium Archive</h2>
        <h2 class="deco-title1">Herbarium Archive</h2>
        <h2 class="deco-title2">Herbarium Archive</h2>
        <div class="registration-container">
            <form action="registration.php" method="POST">

                <!-- First Name -->
                <div class="update-fn">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                        value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    <?php if (isset($errors['first_name'])): ?>
                    <div id="error_first_name" class="text-danger"><?php echo $errors['first_name']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Last Name -->
                <div class="update-ln">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                        value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    <?php if (isset($errors['last_name'])): ?>
                    <div id="error_last_name" class="text-danger"><?php echo $errors['last_name']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Date of Birth -->
                <div class="update-dob">
                    <label for="dob">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob"
                        value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                    <?php if (isset($errors['dob'])): ?>
                    <div id="error_dob" class="text-danger"><?php echo $errors['dob']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Gender -->
                <div class="update-gender">
                    <label for="gender">Gender</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="female" name="gender" value="Female"
                            <?php echo (!isset($_POST['gender']) || $_POST['gender'] == 'Female') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="male" name="gender" value="Male"
                            <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <?php if (isset($errors['gender'])): ?>
                    <div id="error_gender" class="text-danger"><?php echo $errors['gender']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Contact Number -->
                <div class="update-hometown">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number"
                        value="<?php echo htmlspecialchars($_POST['contact_number'] ?? ''); ?>">
                    <?php if (isset($errors['contact_number'])): ?>
                    <div id="error_contact_number" class="text-danger"><?php echo $errors['contact_number']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Email -->
                <div class="update-email">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <?php if (isset($errors['email'])): ?>
                    <div id="error_email" class="text-danger"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Hometown -->
                <div class="update-hometown">
                    <label for="hometown">Hometown</label>
                    <input type="text" class="form-control" id="hometown" name="hometown"
                        value="<?php echo htmlspecialchars($_POST['hometown'] ?? ''); ?>">
                    <?php if (isset($errors['hometown'])): ?>
                    <div id="error_hometown" class="text-danger"><?php echo $errors['hometown']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Password -->
                <div class="update-hometown">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <?php if (isset($errors['password'])): ?>
                    <div id="error_password" class="text-danger"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- Confirm Password -->
                <div class="update-hometown">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    <?php if (isset($errors['confirm_password'])): ?>
                    <div id="error_confirm_password" class="text-danger"><?php echo $errors['confirm_password']; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="login-button">Submit Form</button>
                <div class="mt-4 text-center">
                    <a href="registration.php" class="classify-button">Reset</a>
                </div>
                <div class="text-center">
                    <a href="index.php" class="classify-button">Go Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>