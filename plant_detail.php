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

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize plant data variables
$plant = null;

// Get the scientific name from the URL
if (isset($_GET['scientific_name'])) {
    $scientific_name = $_GET['scientific_name'];

    // Prepare and execute the query to fetch plant details
    $stmt = $conn->prepare("SELECT * FROM plant_table WHERE Scientific_Name = ?");
    $stmt->bind_param("s", $scientific_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $plant = $result->fetch_assoc(); // Fetch plant details into an associative array
    } else {
        $error_message = "Plant details not found.";
    }

    $stmt->close();
} else {
    $error_message = "No plant selected.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbarium Archive - Plant Detail</title>

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

    <h1 class="Plant_detail_heading text-center">Plant <span class="title-green">Details</span></h1>
    <div class="plant_detail_container">
        <?php if ($plant): ?>
        <!-- Display plant details -->
        <div class="plant_detail_img_container">
            <?php
            // Decode the plants_image field (JSON format) from the database
            $images = json_decode($plant['plants_image'], true);
            $plant_photo = $images[0] ?? "img/default_plant.jpg"; // Default image if not provided
            $herbarium_photo = $images[1] ?? "img/default_herbarium.jpg"; // Default herbarium image
            ?>
            <img src="<?php echo htmlspecialchars($plant_photo); ?>" class="plant-img"
                alt="<?php echo htmlspecialchars($plant['Common_Name']); ?>">
            <img src="<?php echo htmlspecialchars($herbarium_photo); ?>" class="herbarium-img"
                alt="<?php echo htmlspecialchars($plant['Common_Name']); ?> Herbarium">
        </div>
        <div class="plant_detail_details">
            <h2><?php echo htmlspecialchars($plant['Scientific_Name']); ?></h2>
            <h5>Common Name: <?php echo htmlspecialchars($plant['Common_Name']); ?></h5>
            <p><strong>Family:</strong> <?php echo htmlspecialchars($plant['family']); ?></p>
            <p><strong>Genus:</strong> <?php echo htmlspecialchars($plant['genus']); ?></p>
            <p><strong>Species:</strong> <?php echo htmlspecialchars($plant['species']); ?></p>

            <!-- PDF Download Button -->
            <?php
            // Check if a description (PDF) exists
            $description_pdf = htmlspecialchars($plant['description']);
            if (!empty($description_pdf) && file_exists($description_pdf)): ?>
            <a href="<?php echo $description_pdf; ?>" class="classify-button" download>
                <i class="fas fa-download"></i> Download Description (PDF)
            </a>
            <?php else: ?>
            <p>No description available for download.</p>
            <?php endif; ?>
            <a href="contribute.php" class="classify-button">Go Back</a>
        </div>
        <?php else: ?>
        <!-- Display error message if plant not found -->
        <p class="text-danger text-center"><?php echo $error_message ?? 'No plant details available.'; ?></p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>