<?php
session_start();
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

// Handle image upload
$filePath = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['plant_image'])) {
    $uploadedFile = $_FILES['plant_image'];
    if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filePath = $uploadDir . uniqid() . "." . strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        if (!move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
            $filePath = '';
            echo "<div class='alert alert-danger'>Failed to upload image.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error uploading file.</div>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbarium Archive - Identify</title>

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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js">
    </script>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5 mb-5">
        <h1 class="text-center">Identify Plant</h1>
        <form action="identify.php" method="POST" enctype="multipart/form-data" class="text-center">
            <div class="mb-3">
                <input type="file" name="plant_image" id="plant-image-input" class="form-control" accept="image/*"
                    required>
            </div>
            <button type="submit" class="classify-button">Upload and Identify</button>
        </form>

        <!-- Display Image Preview -->
        <?php if (!empty($filePath)): ?>
        <div class="mt-4 text-center">
            <h3>Uploaded Image</h3>
            <img id="uploaded-image" src="<?php echo htmlspecialchars($filePath); ?>" alt="Plant Image"
                style="max-width: 300px; max-height: 300px;" />
        </div>
        <?php endif; ?>

        <div id="ai-result" class="mt-4"></div>
    </div>

    <script>
    const modelURL = "./my_model/";
    let model;

    async function loadModel() {
        model = await tmImage.load(modelURL + "model.json", modelURL + "metadata.json");
        console.log("Model loaded successfully.");
    }

    async function predict() {
        const imgElement = document.getElementById("uploaded-image");
        if (!imgElement) return;

        const prediction = await model.predict(imgElement);
        const highestPrediction = prediction.reduce((prev, current) =>
            prev.probability > current.probability ? prev : current
        );

        if (highestPrediction.className.toLowerCase() === "not plant") {
            document.getElementById("ai-result").innerHTML =
                `<div class="alert alert-warning">Not a plant image. Data not found.</div>`;
        } else {
            const predictedPlant = highestPrediction.className;
            const accuracy = (highestPrediction.probability * 100).toFixed(2);

            document.getElementById("ai-result").innerHTML =
                `<div class="alert alert-info">Predicted Plant: <strong>${predictedPlant}</strong> (${accuracy}%)</div>`;

            // Fetch plant information and pass accuracy as well
            fetchPlantInfo(predictedPlant, accuracy);
        }
    }


    async function fetchPlantInfo(plantName, accuracy) {
        try {
            const response = await fetch(
                `fetch_plant_info.php?plant_name=${encodeURIComponent(plantName)}&accuracy=${accuracy}`
            );
            const data = await response.json();

            if (data.success) {
                const plantInfo = data.plantInfo;

                // Images
                const images = plantInfo.Images;
                const plantPhoto = images[0] || "img/default_plant.jpg";
                const herbariumPhoto = images[1] || "img/default_herbarium.jpg";

                // Carousel HTML
                let imagesHtml = '';
                if (images.length > 0) {
                    images.forEach((image, index) => {
                        imagesHtml += `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="img/${image}" class="d-block w-100" alt="Plant Image ${index + 1}">
                        </div>`;
                    });
                } else {
                    imagesHtml = `
                    <div class="carousel-item active">
                        <img src="img/default_plant.jpg" class="d-block w-100" alt="Default Plant Image">
                    </div>`;
                }

                // Display Plant Information
                document.getElementById("ai-result").innerHTML = `
                <div class="plant_detail_container">
                    <!-- Image Section -->
                    <div class="plant_detail_img_container">
                        <img src="${plantPhoto}" class="plant-img" alt="${plantInfo.Common_Name}">
                        <img src="${herbariumPhoto}" class="herbarium-img" alt="${plantInfo.Common_Name} Herbarium">
                    </div>

                    <!-- Plant Details Section -->
                    <div class="plant_detail_details">
                        <h2>${plantInfo.Scientific_Name}</h2>
                        <h5>Common Name: ${plantInfo.Common_Name}</h5>
                        <p><strong>Accuracy:</strong> ${accuracy}%</p>
                        <p><strong>Family:</strong> ${plantInfo.Family}</p>
                        <p><strong>Genus:</strong> ${plantInfo.Genus}</p>
                        <p><strong>Species:</strong> ${plantInfo.Species}</p>

                       

                        <!-- PDF Download Button -->
                        ${
                            plantInfo.Description && plantInfo.Description !== ''
                                ? `<a href="${plantInfo.Description}" download class="classify-button">
                                        <i class="fas fa-download"></i> Download Description (PDF)
                                    </a>`
                                : `<p class="text-muted">No description available for download.</p>`
                        }
                    </div>
                </div>`;
            } else {
                document.getElementById("ai-result").innerHTML =
                    `<div class="alert alert-danger">${data.message}</div>`;
            }
        } catch (error) {
            document.getElementById("ai-result").innerHTML =
                `<div class="alert alert-danger">An error occurred while fetching plant information.</div>`;
            console.error(error);
        }
    }





    document.addEventListener("DOMContentLoaded", async () => {
        await loadModel();
        <?php if (!empty($filePath)): ?>
        predict();
        <?php endif; ?>
    });
    </script>


    <?php include 'footer.php'; ?>
</body>

</html>