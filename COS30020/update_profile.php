<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "PlantBiodiversity";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$userEmail = $_SESSION['email'];
$errors = [];
$successMessage = "";

// Function to generate unique filenames
function getUniqueFileName($uploadDir, $fileName)
{
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
    $newFileName = $baseName;
    $counter = 1;

    while (file_exists($uploadDir . $newFileName . "." . $fileExtension)) {
        $newFileName = $baseName . "[" . $counter . "]";
        $counter++;
    }

    return $newFileName . "." . $fileExtension;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = trim($_POST['dob']);
    $gender = $_POST['gender'];
    $newEmail = trim($_POST['email']); // New email
    $hometown = trim($_POST['hometown']);
    $password = trim($_POST['password']);
    $contact_number = trim($_POST['contact_number']);

    // Validate inputs
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($dob)) $errors[] = "Date of birth is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($hometown)) $errors[] = "Hometown is required.";
    if (!empty($contact_number) && !preg_match('/^\d{10,15}$/', $contact_number)) {
        $errors[] = "Contact number must be between 10 and 15 digits.";
    }

    // Check if the new email already exists in the database (if changed)
    if ($newEmail !== $userEmail) {
        $stmt = $conn->prepare("SELECT email FROM user_table WHERE email = ?");
        $stmt->bind_param("s", $newEmail);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "The email is already in use by another account.";
        }
        $stmt->close();
    }

    // Password validation
    if (!empty($password)) {
        if (!preg_match("/^(?=.*\d)(?=.*[!@#$%^&])[a-zA-Z\d!@#$%^&]{8,}$/", $password)) {
            $errors[] = "Password must be at least 8 characters, include a number, and a special character.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }
    } else {
        $stmt = $conn->prepare("SELECT password FROM account_table WHERE email = ?");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $hashedPassword = $result->num_rows > 0 ? $result->fetch_assoc()['password'] : null;
        $stmt->close();
    }

    // Handle profile image upload
    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "img/profile_images/";
        $imageFileType = strtolower(pathinfo($_FILES['imageUpload']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($imageFileType, $allowedTypes)) {
            if ($_FILES['imageUpload']['size'] <= 5 * 1024 * 1024) { // 5MB size limit
                $newFileName = getUniqueFileName($uploadDir, $_FILES['imageUpload']['name']);
                $profileImage = $uploadDir . $newFileName;

                if (!move_uploaded_file($_FILES['imageUpload']['tmp_name'], $profileImage)) {
                    $errors[] = "Failed to upload profile image.";
                }
            } else {
                $errors[] = "Profile image must not exceed 5MB.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed for the profile image.";
        }
    } else {
        // Keep the existing profile image if no new image is uploaded
        $stmt = $conn->prepare("SELECT profile_image FROM user_table WHERE email = ?");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $profileImage = $userData['profile_image'] ?? null;
        $stmt->close();
    }

    // If no errors, update the database
    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            // Update `user_table`
            $stmt = $conn->prepare("UPDATE user_table SET first_name = ?, last_name = ?, dob = ?, gender = ?, hometown = ?, contact_number = ?, profile_image = ?, email = ? WHERE email = ?");
            $stmt->bind_param("sssssssss", $first_name, $last_name, $dob, $gender, $hometown, $contact_number, $profileImage, $newEmail, $userEmail);
            $stmt->execute();
            $stmt->close();

            // Update `account_table`
            $stmt = $conn->prepare("UPDATE account_table SET password = ?, email = ? WHERE email = ?");
            $stmt->bind_param("sss", $hashedPassword, $newEmail, $userEmail);
            $stmt->execute();
            $stmt->close();

            // Update session email immediately
            $_SESSION['email'] = $newEmail;

            $conn->commit();
            $successMessage = "Profile updated successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}

// Fetch user data again after update
$stmt = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$profileImage = $userData['profile_image'] ?? null;

$stmt->close();

$conn->close();
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="centered-container-update">
        <h2 class="deco-title">Herbarium Archive</h2>
        <h2 class="deco-title1">Herbarium Archive</h2>
        <h2 class="deco-title2">Herbarium Archive</h2>
        <div class="update-container">
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
            <?php elseif (!empty($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="login-logo">
                    <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image" class="rounded-circle"
                        style="width: 150px; height: 150px;">
                </div>

                <!-- Form Fields -->
                <div class="update-fn">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                        value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>
                </div>
                <div class="update-ln">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                        value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>
                </div>
                <div class="update-dob">
                    <label for="dob">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob"
                        value="<?php echo htmlspecialchars($userData['dob']); ?>" required>
                </div>
                <div class="update-gender">
                    <label for="gender">Gender</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="Male" <?php echo ($userData['gender'] === 'Male') ? 'selected' : ''; ?>>Male
                        </option>
                        <option value="Female" <?php echo ($userData['gender'] === 'Female') ? 'selected' : ''; ?>>
                            Female</option>
                    </select>
                </div>
                <div class="update-email">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                </div>
                <div class="update-hometown">
                    <label for="hometown">Hometown</label>
                    <input type="text" class="form-control" id="hometown" name="hometown"
                        value="<?php echo htmlspecialchars($userData['hometown']); ?>" required>
                </div>
                <div class="update-hometown">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Enter a new password">
                </div>
                <div class="update-hometown">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number"
                        value="<?php echo htmlspecialchars($userData['contact_number']); ?>">
                </div>

                <div class="update-hometown">
                    <label for="imageUpload">Profile Image</label>
                    <input type="file" class="form-control" id="imageUpload" name="imageUpload" accept="image/*">
                </div>

                <button type="submit" class="login-button">Update Profile</button>
                <div class="mt-4 text-center">
                    <a href="main_menu.php" class="cancel-custom-button">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>