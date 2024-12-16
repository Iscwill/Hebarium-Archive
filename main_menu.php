<?php
session_start();

// Check if the user is logged in and if the user type is "user"
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'user') {
    // Redirect to login page if not logged in or not a "user"
    header("Location: login.php");
    exit();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- GOOGLE FONTS LINK -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap" rel="stylesheet">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <section class="main_menu_about" id="main_menu_about">
        <h2 class="deco-title">Herb-Archive</h2>
        <div class="box-container">
            <div class="image">
                <img src="img/topimg.jpg" alt="">
            </div>
            <div class="content">
                <h3 class="title">Contribute to Global Plant Biodiversity</h3>
                <p>Join our mission to preserve plant species by uploading your own herbarium specimens. Help
                    researchers identify and classify plants from around the world.</p>
            </div>
        </div>
    </section>

    <section class="main_selection" id="main_selection">
        <h2 class="heading">Options</h2>
        <div class="box-container">
            <div class="box">
                <img src="img/classify1.png" alt="">
                <div class="content">
                    <h3>Plant Classification</h3>
                    <a href="classify.php" class="classify-button">Go Now <span class="arrow">&rarr;</span></a>
                </div>
            </div>
            <div class="box">
                <img src="img/classify2.png" alt="">
                <div class="content">
                    <h3>Tutorial</h3>
                    <a href="tutorial.php" class="classify-button">Go Now <span class="arrow">&rarr;</span></a>
                </div>
            </div>
            <div class="box">
                <img src="img/classify3.png" alt="">
                <div class="content">
                    <h3>Identify</h3>
                    <a href="identify.php" class="classify-button">Go Now <span class="arrow">&rarr;</span></a>
                </div>
            </div>
            <div class="box">
                <img src="img/classify4.png" alt="">
                <div class="content">
                    <h3>Contribution</h3>
                    <a href="contribute.php" class="classify-button">Go Now <span class="arrow">&rarr;</span></a>
                </div>
            </div>
        </div>
    </section>

    <div class="top-section">
        <h5>OUR MISSION</h5>
        <h3>Contribute to <br> Plant Biodiversity Research</h3>
        <div class="row">
            <div class="col-md-4 py-3 py-md-0">
                <div class="card">
                    <div class="card-body">
                        <h1>Simple Contribution Process</h1>
                        <p>Upload your herbarium specimen <br> with just a few steps</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-3 py-md-0">
                <div class="card">
                    <div class="card-body">
                        <h1>Collaborate with Researchers</h1>
                        <p>Help scientists classify and identify <br> plants globally</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-3 py-md-0">
                <div class="card">
                    <div class="card-body">
                        <h1>Preserving Plant Diversity</h1>
                        <p>Every contribution aids in <br> biodiversity conservation</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="line" style="width: 100%; height: 2px; background-color: #009900;"></div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>