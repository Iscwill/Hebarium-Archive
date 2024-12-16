<?php
session_start();

// Check if the user is logged in and if the user type is "admin"
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'admin') {
    // Redirect to login page if not logged in or not an "admin"
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbarium Archive - Admin Main Menu</title>


    <!-- BOOTSTRAP CDN LINK -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- GOOGLE FONTS LINK -->
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap" rel="stylesheet">
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


    /* Main Menu Section Styling */
    .main_menu_section {
        margin-top: 50px;
    }

    .main_menu_section .text-muted {
        font-size: 20px;
        text-spacing: 20px;
    }

    .option-card {
        border: none;
        transition: transform 0.3s, box-shadow 0.3s;
        text-align: center;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .option-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .option-card i {
        font-size: 50px;
        color: #003366;
        margin-bottom: 15px;
    }

    .option-card h3 {
        font-weight: bold;
    }

    .option-card p {
        color: #666;
    }

    /* Button Styling */
    .classify-button {
        background-color: #003366;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
        display: inline-block;
        margin-top: 15px;
    }

    .classify-button:hover {
        background-color: #00509e;
    }

    .line {
        height: 3px;
        background-color: #003366;
        margin: 20px 0;
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
                        <a class="nav-link" href="index.php">Home</a>
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

    <!-- Welcome Section -->
    <section class="main_menu_section text-center">
        <div class="admin-container">
            <!-- <h2 class="deco-title">Herbarium Archive</h2>
            <h2 class="deco-title1">Herbarium Archive</h2> -->
            <p class="text-muted">Manage and oversee all activities, including user accounts and plant data.</p>
        </div>
    </section>

    <!-- Admin Options Section -->
    <section class="main_menu_section">
        <div class="container">
            <h2 class="heading text-center mb-4">Admin Options</h2>
            <div class="row justify-content-center">
                <!-- Manage User Accounts -->
                <div class="col-md-4 mb-4">
                    <div class="option-card">
                        <i class="fas fa-user-cog"></i>
                        <h3>Manage User Accounts</h3>
                        <p>Oversee, edit, and manage user account details efficiently.</p>
                        <a href="manage_accounts.php" class="classify-button">Go Now <span
                                class="arrow">&rarr;</span></a>
                    </div>
                </div>

                <!-- Manage Plant Data -->
                <div class="col-md-4 mb-4">
                    <div class="option-card">
                        <i class="fas fa-leaf"></i>
                        <h3>Manage Plant Data</h3>
                        <p>Approve, edit, or delete plant contributions to maintain data integrity.</p>
                        <a href="manage_plants.php" class="classify-button">Go Now <span class="arrow">&rarr;</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>