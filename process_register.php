<?php
// Function to validate the form data
function validate_form_data($data) {
    $errors = [];

    // First name validation
    if (empty($data['first_name'])) {
        $errors['first_name'] = "First name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $data['first_name'])) {
        $errors['first_name'] = "First name should only contain alphabets and spaces.";
    }

    // Last name validation
    if (empty($data['last_name'])) {
        $errors['last_name'] = "Last name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $data['last_name'])) {
        $errors['last_name'] = "Last name should only contain alphabets and spaces.";
    }
    // Date of birth validation
    if (empty($data['dob'])) {
        $errors['dob'] = "Date of birth is required.";
    }
    // Gender validation
    if (empty($data['gender'])) {
        $errors['gender'] = "Gender is required.";
    }
    // Email validation
    if (empty($data['email'])) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // Hometown validation
    if (empty($data['hometown'])) {
        $errors['hometown'] = "Hometown is required.";
    }

    // Password validation
    if (empty($data['password'])) {
        $errors['password'] = "Password is required.";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $data['password'])) {
        $errors['password'] = "Password must contain at least 8 characters, 1 number, and 1 symbol.";
    }

    // Confirm password validation
    if (empty($data['confirm_password'])) {
        $errors['confirm_password'] = "Confirm password is required.";
    } elseif ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    return $errors;
}


// Function to check if email already exists in the file
function email_exists($email) {
    $file_path = 'data/User/user.txt';
    if (file_exists($file_path)) {
        $file = fopen($file_path, "r");
        while (($line = fgets($file)) !== false) {
            $user_data = explode('|', $line);
            if (trim($user_data[4]) == $email) { // 4th field is email
                return true;
            }
        }
        fclose($file);
    }
    return false;
}

// Function to save user data to the text file
function save_user_data($data) {
    // Create directory if it doesn't exist
    $dir_path = 'data/User';
    if (!file_exists($dir_path)) {
        mkdir($dir_path, 0777, true);
    }

    // Prepare user data string
    $user_data = "First Name:" . $data['first_name'] . "|Last Name:" . $data['last_name'] .
        "|DOB:" . $data['dob'] . "|Gender:" . $data['gender'] . "|Email:" . $data['email'] .
        "|Hometown:" . $data['hometown'] . "|Password:" . $data['password'] . "\n";

    // Write user data to the text file
    $file_path = $dir_path . '/user.txt';
    file_put_contents($file_path, $user_data, FILE_APPEND);
}

// Check if the clear button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_errors'])) {
    // Clear all session variables related to the form
    $_SESSION = [];  // Clear all session data if you only store form data in the session
    session_destroy();  // End the session
    header('Location: ' . $_SERVER['PHP_SELF']);  // Reload the page
    exit();
}


// Clear errors after they are displayed (put this after displaying the form)
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);  // Clear the errors
} else {
    $errors = [];
}

// Process the form when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data from POST request
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $email = trim($_POST['email']);
    $hometown = trim($_POST['hometown']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate the form data
    $errors = validate_form_data([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'dob' => $dob,
        'gender' => $gender,
        'email' => $email,
        'hometown' => $hometown,
        'password' => $password,
        'confirm_password' => $confirm_password
    ]);

    // Check if email already exists
    if (email_exists($email)) {
        $errors[] = "The email address is already in use.";
    }

    // If there are no errors, save the data
    if (empty($errors)) {
        save_user_data([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'dob' => $dob,
            'gender' => $gender,
            'email' => $email,
            'hometown' => $hometown,
            'password' => $password
        ]);

        // Redirect to login page (Req-5)
        header("Location: index.php");
        exit(); 
    } else {
        // If there are errors, redirect back to the registration form with error messages
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: registration.php");
        exit();
    }
}
?>