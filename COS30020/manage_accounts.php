<?php
session_start();

// Check if the user is logged in and if the user type is "admin"
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'admin') {
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

// Function to generate unique filenames
function generateUniqueFilename($directory, $filename) {
    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
    $baseName = pathinfo($filename, PATHINFO_FILENAME); // Filename without extension
    $counter = 0;

    // Generate a new filename if the file already exists
    while (file_exists($directory . $filename)) {
        $counter++;
        $filename = $baseName . "($counter)." . $fileExtension;
    }

    return $filename;
}

// Initialize variables
$errors = [];
$successMessage = "";

// Handle add user form submission
if (isset($_POST['add_user'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $type = trim($_POST['type']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $contact_number = trim($_POST['contact_number']);
    $hometown = trim($_POST['hometown']);
    $profile_image = $_FILES['profile_image']['name'];

    // Validate email
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/", $email)) {
        $errors[] = "Invalid email address. It must contain '@' and end with '.com'.";
    }

    // Check if email already exists
    $stmt_check_email = $conn->prepare("SELECT email FROM account_table WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();
    if ($stmt_check_email->num_rows > 0) {
        $errors[] = "This email is already in use.";
    }
    $stmt_check_email->close();

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (!preg_match("/^(?=.*\d)(?=.*[!@#$%^&])[a-zA-Z\d!@#$%^&]{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters, include a number, and a special character.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Validate user type
    if (empty($type) || !in_array($type, ['user', 'admin'])) {
        $errors[] = "Invalid user type.";
    }

    // Handle profile image upload
    $profile_image_path = "";
    $target_dir = "img/profile_images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (!empty($profile_image)) {
        $profile_image = generateUniqueFilename($target_dir, $profile_image);
        $profile_image_path = $target_dir . $profile_image;
        $imageFileType = strtolower(pathinfo($profile_image_path, PATHINFO_EXTENSION));

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Invalid image file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        } elseif (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image_path)) {
            $errors[] = "Failed to upload profile image.";
        }
    } else {
        // Set default image based on gender
        $profile_image_path = $gender === "Male" ? "img/profile_images/boys.jpg" : "img/profile_images/girl.png";
    }

    // If no errors, proceed to insert user
    if (empty($errors)) {
        // Insert into user_table
        $stmt_user = $conn->prepare("INSERT INTO user_table (email, first_name, last_name, dob, gender, contact_number, hometown, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_user->bind_param("ssssssss", $email, $first_name, $last_name, $dob, $gender, $contact_number, $hometown, $profile_image_path);
        if ($stmt_user->execute()) {
            // Insert into account_table
            $stmt_account = $conn->prepare("INSERT INTO account_table (email, password, type) VALUES (?, ?, ?)");
            $stmt_account->bind_param("sss", $email, $hashed_password, $type);
            if ($stmt_account->execute()) {
                $successMessage = "User added successfully!";
                header("Location: manage_accounts.php");
                exit();
            } else {
                $errors[] = "Failed to add user to account_table: " . $stmt_account->error;
            }
            $stmt_account->close();
        } else {
            $errors[] = "Failed to add user to user_table: " . $stmt_user->error;
        }
        $stmt_user->close();
    }
}


// Handle delete user request
if (isset($_GET['delete_user'])) {
    $email = $_GET['delete_user'];

    $stmt_user = $conn->prepare("DELETE FROM user_table WHERE email = ?");
    $stmt_user->bind_param("s", $email);
    $stmt_user->execute();
    $stmt_user->close();

    $stmt_account = $conn->prepare("DELETE FROM account_table WHERE email = ?");
    $stmt_account->bind_param("s", $email);
    $stmt_account->execute();
    $stmt_account->close();

    $successMessage = "User deleted successfully!";
    header("Location: manage_accounts.php");
    exit();
}

// Handle edit user form submission
if (isset($_POST['edit_user'])) {
    $current_email = $_POST['current_email']; // Original email (hidden field)
    $email = $_POST['email']; // New email
    $password = trim($_POST['password']); // New password
    $type = $_POST['type']; // User type
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $contact_number = trim($_POST['contact_number']);
    $hometown = trim($_POST['hometown']);
    $profile_image = $_FILES['profile_image']['name'];

    // Validate email
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/", $email)) {
        $errors[] = "Invalid email address. It must contain '@' and end with '.com'.";
    }

    // Prepare profile image handling
    if (!empty($profile_image)) {
        $target_dir = "img/profile_images/";

        // Ensure the directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $profile_image = generateUniqueFilename($target_dir, $profile_image);
        $profile_image_path = $target_dir . $profile_image;
        $imageFileType = strtolower(pathinfo($profile_image_path, PATHINFO_EXTENSION));

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Invalid image file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        } elseif (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image_path)) {
            $errors[] = "Failed to upload profile image.";
        } else {
            $stmt = $conn->prepare("UPDATE user_table SET email = ?, first_name = ?, last_name = ?, dob = ?, gender = ?, contact_number = ?, hometown = ?, profile_image = ? WHERE email = ?");
            $stmt->bind_param("sssssssss", $email, $first_name, $last_name, $dob, $gender, $contact_number, $hometown, $profile_image_path, $current_email);
            if (!$stmt->execute()) {
                $errors[] = "Failed to update user with profile image: " . $stmt->error;
            } else {
                $successMessage = "User updated successfully!";
            }
            $stmt->close();
        }
    } else {
        $stmt = $conn->prepare("UPDATE user_table SET email = ?, first_name = ?, last_name = ?, dob = ?, gender = ?, contact_number = ?, hometown = ? WHERE email = ?");
        $stmt->bind_param("ssssssss", $email, $first_name, $last_name, $dob, $gender, $contact_number, $hometown, $current_email);
        if (!$stmt->execute()) {
            $errors[] = "Failed to update user: " . $stmt->error;
        } else {
            $successMessage = "User updated successfully!";
        }
        $stmt->close();
    }

   // Update password if provided
    if (!empty($password)) {
        if (!preg_match("/^(?=.*\d)(?=.*[!@#$%^&])[a-zA-Z\d!@#$%^&]{8,}$/", $password)) {
            $errors[] = "Password must be at least 8 characters, include a number, and a special character.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE account_table SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $current_email);
            if (!$stmt->execute()) {
                $errors[] = "Failed to update password: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Update user type
    $stmt = $conn->prepare("UPDATE account_table SET type = ? WHERE email = ?");
    $stmt->bind_param("ss", $type, $current_email);
    if (!$stmt->execute()) {
        $errors[] = "Failed to update user type: " . $stmt->error;
    } else {
        $successMessage = "User type updated successfully!";
        header("Location: manage_accounts.php");
        exit();
    }
    $stmt->close();
}

// Fetch all users and their types
$sql = "SELECT user_table.*, account_table.type 
        FROM user_table 
        LEFT JOIN account_table 
        ON user_table.email = account_table.email";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
function sanitize_id($email) {
    return str_replace(['@', '.', '-'], '_', $email);
}
?>









<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbarium Archive - Manage Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Navbar Styling */
    .navbar-dark {
        background-color: #003366;
    }

    .dropdown-menu {
        background-color: #003366;
    }

    .dropdown-menu .dropdown-item {
        color: #fff;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #00509e;
    }


    .container {
        margin-top: 30px;
    }

    .btn-primary {
        background-color: #003366;
        border: none;
    }

    .btn-primary:hover {
        background-color: #00509e;
    }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Herbarium Archive</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="main_menu_admin.php">Home</a>
                    </li>
                    <!-- Admin Options Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Admin Options
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="manage_accounts.php">Manage User Accounts</a></li>
                            <li><a class="dropdown-item" href="manage_plants.php">Manage Plant Data</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center">Manage User Accounts</h1>

        <div class="container mt-4">
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php elseif (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <p><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
            <?php endif; ?>
        </div>


        <!-- Add User Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="manage_accounts.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">User Type</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number">
                            </div>
                            <div class="mb-3">
                                <label for="hometown" class="form-label">Hometown</label>
                                <input type="text" class="form-control" id="hometown" name="hometown">
                            </div>
                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image"
                                    accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- User Table -->
        <h4 class="mb-4">Existing Users</h4>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Contact Number</th>
                        <th>Hometown</th>
                        <th>Type</th>
                        <th>Profile Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <?php $sanitized_id = sanitize_id($user['email']); ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['dob']); ?></td>
                        <td><?php echo htmlspecialchars($user['gender']); ?></td>
                        <td><?php echo htmlspecialchars($user['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($user['hometown']); ?></td>
                        <td><?php echo htmlspecialchars($user['type']); ?></td>
                        <td class="text-center">
                            <?php if (!empty($user['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image"
                                class="img-thumbnail" width="50">
                            <?php else: ?>
                            <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <!-- Always display the Edit button -->
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $sanitized_id; ?>">Edit</button>
                                <!-- Conditionally display the Delete button only for non-admin users -->
                                <?php if ($user['type'] !== 'admin'): ?>
                                <a href="manage_accounts.php?delete_user=<?php echo urlencode($user['email']); ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?php echo $sanitized_id; ?>" tabindex="-1"
                        aria-labelledby="editModalLabel<?php echo $sanitized_id; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="manage_accounts.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $sanitized_id; ?>">Edit
                                            User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Form fields -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password (Leave blank to keep
                                                current)</label>
                                            <input type="password" class="form-control" name="password"
                                                placeholder="Enter new password">
                                        </div>
                                        <input type="hidden" name="current_email"
                                            value="<?php echo htmlspecialchars($user['email']); ?>">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name"
                                                value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name"
                                                value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="dob" class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" name="dob"
                                                value="<?php echo htmlspecialchars($user['dob']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="Male"
                                                    <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male
                                                </option>
                                                <option value="Female"
                                                    <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact_number" class="form-label">Contact Number</label>
                                            <input type="text" class="form-control" name="contact_number"
                                                value="<?php echo htmlspecialchars($user['contact_number']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="hometown" class="form-label">Hometown</label>
                                            <input type="text" class="form-control" name="hometown"
                                                value="<?php echo htmlspecialchars($user['hometown']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="type" class="form-label">User Type</label>
                                            <select class="form-control" name="type" required>
                                                <option value="user"
                                                    <?php echo $user['type'] === 'user' ? 'selected' : ''; ?>>User
                                                </option>
                                                <option value="admin"
                                                    <?php echo $user['type'] === 'admin' ? 'selected' : ''; ?>>Admin
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="profile_image" class="form-label">Current Profile Image</label>
                                            <div class="mb-2">
                                                <?php if (!empty($user['profile_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>"
                                                    alt="Profile Image" class="img-thumbnail"
                                                    style="width: 100px; height: 100px;">
                                                <?php else: ?>
                                                <span class="text-muted">No image available</span>
                                                <?php endif; ?>
                                            </div>
                                            <label for="profile_image" class="form-label">Update Profile Image</label>
                                            <input type="file" class="form-control" name="profile_image"
                                                accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_user">Save
                                            Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>