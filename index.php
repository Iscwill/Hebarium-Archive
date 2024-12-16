<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbarium Archive - Main Menu</title>

    <!-- STYLE CSS LINK -->
    <link rel="stylesheet" href="style/style.css">
    <!-- STYLE CSS LINK -->

    <!-- BOOTSTRAP CDN LINK -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- BOOTSTRAP CDN LINK -->

    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- FONT AWESOME CDN -->

    <!-- GOOGLE FONTS LINK -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@600&display=swap" rel="stylesheet">
    <!-- GOOGLE FONTS LINK -->
</head>

<body>

    <section class="index_about" id="index_about">
        <h2 class="deco-title">Herbarium Archive</h2>
        <h2 class="deco-title1">Herbarium Archive</h2>
        <h2 class="deco-title2">Herbarium Archive</h2>

        <div class="index_login_register">
            <a href="login.php" class="classify-button">Log in<span class="arrow">&rarr;</span></a>
            <a href="registration.php" class="classify-button">Register<span class="arrow">&rarr;</span></a>
        </div>
        <div class="box-container">
            <div class="image">
                <?php
                // Array of image sources
                $images = [
                    "img/herbarium1.png",
                    "img/herbarium2.png",
                    "img/herbarium3.jpg",
                ];

                // Get a random image from the array
                $randomImage = $images[array_rand($images)];
                ?>
                <img id="randomImage" src="<?php echo $randomImage; ?>" alt="">
            </div>


            <div class="content">
                <h3 class="title">Welcome to the Global <span class="title-green">Plant Biodiversity Initiative</span>
                </h3>
                <p>The Herbarium Archive is about preserving global plant biodiversity. Users can register to create an
                    account, upload their herbarium specimens, and manage their contributions. By participating, you
                    support vital research and conservation efforts, helping to protect rare species and expand our
                    understanding of plant diversity. Join our community of researchers and enthusiasts in fostering a
                    deeper appreciation for the world’s botanical treasures.</p>

                <a href="main_menu.php" class="classify-button">Main Menu<span class="arrow">&rarr;</span></a>
                <a href="about.php" class="classify-button left">About<span class="arrow">&rarr;</span></a>
            </div>

        </div>

    </section>

</body>

</html>