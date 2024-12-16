<?php
session_start();

// Check if the user is logged in and is a "user"
if (!isset($_SESSION['email']) || $_SESSION['type'] !== 'user') {
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
$errors = [];
$successMessage = "";

// Pagination variables
$limit = 4; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for SQL query

// Fetch total number of records
$totalRecordsQuery = $conn->query("SELECT COUNT(*) AS total FROM plant_table");
$totalRecords = $totalRecordsQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit); // Calculate total pages

// Fetch records for the current page
$contributedPlants = [];
$result = $conn->query("SELECT * FROM plant_table LIMIT $limit OFFSET $offset");
if ($result && $result->num_rows > 0) {
    $contributedPlants = $result->fetch_all(MYSQLI_ASSOC);
}

// Function to generate a unique file name
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


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $scientific_name = trim($_POST['scientific_name']);
    $common_name = trim($_POST['common_name']);
    $family = trim($_POST['family']);
    $genus = trim($_POST['genus']);
    $species = trim($_POST['species']);

    // Validate inputs
    if (empty($scientific_name)) $errors[] = "Scientific Name is required.";
    if (empty($common_name)) $errors[] = "Common Name is required.";
    if (empty($family)) $errors[] = "Family is required.";
    if (empty($genus)) $errors[] = "Genus is required.";
    if (empty($species)) $errors[] = "Species is required.";

    // Handle plant photo upload
    $plant_photo_path = "";
    if (isset($_FILES['plant_photo']) && $_FILES['plant_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "img/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $plant_photo_name = generateUniqueFileName($uploadDir, $_FILES['plant_photo']['name']);
        $plant_photo_path = $uploadDir . $plant_photo_name;
        $plantFileType = strtolower(pathinfo($plant_photo_path, PATHINFO_EXTENSION));
        $allowedImageTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($plantFileType, $allowedImageTypes)) {
            if ($_FILES['plant_photo']['size'] <= 5 * 1024 * 1024) { // 5MB size limit
                if (!move_uploaded_file($_FILES['plant_photo']['tmp_name'], $plant_photo_path)) {
                    $errors[] = "Failed to upload plant photo.";
                }
            } else {
                $errors[] = "Plant photo must not exceed 5MB.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed for plant photo.";
        }
    }

    // Handle herbarium photo upload
    $herbarium_photo_path = "";
    if (isset($_FILES['herbarium_photo']) && $_FILES['herbarium_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "img/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $herbarium_photo_name = generateUniqueFileName($uploadDir, $_FILES['herbarium_photo']['name']);
        $herbarium_photo_path = $uploadDir . $herbarium_photo_name;
        $herbariumFileType = strtolower(pathinfo($herbarium_photo_path, PATHINFO_EXTENSION));
        $allowedImageTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($herbariumFileType, $allowedImageTypes)) {
            if ($_FILES['herbarium_photo']['size'] <= 5 * 1024 * 1024) { // 5MB size limit
                if (!move_uploaded_file($_FILES['herbarium_photo']['tmp_name'], $herbarium_photo_path)) {
                    $errors[] = "Failed to upload herbarium photo.";
                }
            } else {
                $errors[] = "Herbarium photo must not exceed 5MB.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed for herbarium photo.";
        }
    }

    // Handle description file upload
    $description_file_path = "";
    if (isset($_FILES['description_file']) && $_FILES['description_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "plants_description/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $descriptionFileType = strtolower(pathinfo($_FILES['description_file']['name'], PATHINFO_EXTENSION));

        if ($descriptionFileType === 'pdf') {
            if ($_FILES['description_file']['size'] <= 7 * 1024 * 1024) { // 7MB size limit
                $description_file_name = generateUniqueFileName($uploadDir, $_FILES['description_file']['name']);
                $description_file_path = $uploadDir . $description_file_name;
                if (!move_uploaded_file($_FILES['description_file']['tmp_name'], $description_file_path)) {
                    $errors[] = "Failed to upload description file.";
                }
            } else {
                $errors[] = "Description file must not exceed 7MB.";
            }
        } else {
            $errors[] = "Only PDF files are allowed for the description file.";
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $images_json = json_encode([$plant_photo_path, $herbarium_photo_path], JSON_UNESCAPED_SLASHES);
        $stmt = $conn->prepare("INSERT INTO plant_table (Scientific_Name, Common_Name, family, genus, species, plants_image, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $scientific_name, $common_name, $family, $genus, $species, $images_json, $description_file_path);

        if ($stmt->execute()) {
            $successMessage = "Plant information uploaded successfully!";
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to prevent resubmission
            exit();
        } else {
            $errors[] = "Failed to store data. Please try again.";
        }

        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbarium Archive - Contributions</title>
    <link rel="stylesheet" href="style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-4">
        <h1 class="heading text-center">Contribute <span>Plants</span></h1>
        <div class="row">
            <div class="col-md-6">
                <!-- Contribution form -->
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
                <?php elseif (!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>

                <form action="contribute.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="scientific_name" class="form-label">Scientific Name</label>
                        <input type="text" class="form-control" id="scientific_name" name="scientific_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="common_name" class="form-label">Common Name</label>
                        <input type="text" class="form-control" id="common_name" name="common_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="family" class="form-label">Family</label>
                        <input type="text" class="form-control" id="family" name="family" required>
                    </div>
                    <div class="mb-3">
                        <label for="genus" class="form-label">Genus</label>
                        <input type="text" class="form-control" id="genus" name="genus" required>
                    </div>
                    <div class="mb-3">
                        <label for="species" class="form-label">Species</label>
                        <input type="text" class="form-control" id="species" name="species" required>
                    </div>
                    <div class="mb-3">
                        <label for="plant_photo" class="form-label">Plant Photo</label>
                        <input type="file" class="form-control" id="plant_photo" name="plant_photo" accept="image/*"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="herbarium_photo" class="form-label">Herbarium Photo</label>
                        <input type="file" class="form-control" id="herbarium_photo" name="herbarium_photo"
                            accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="description_file" class="form-label">Description (PDF)</label>
                        <input type="file" class="form-control" id="description_file" name="description_file"
                            accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Contribute</button>
                </form>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <?php foreach ($contributedPlants as $plant): ?>
                    <?php
                    $scientific_name = htmlspecialchars($plant['Scientific_Name']);
                    $common_name = htmlspecialchars($plant['Common_Name']);
                    $family = htmlspecialchars($plant['family']);
                    $genus = htmlspecialchars($plant['genus']);
                    $species = htmlspecialchars($plant['species']);
                    $images = json_decode($plant['plants_image'], true);
                    $plant_photo = $images[0] ?? "img/default_plant.jpg";
                    $herbarium_photo = $images[1] ?? "img/default_herbarium.jpg";
                    ?>
                    <div class="col-md-6 py-2">
                        <div class="card h-100">
                            <div class="img-container">
                                <img src="<?php echo $plant_photo; ?>" class="card-img-top"
                                    alt="<?php echo $common_name; ?>">
                                <img src="<?php echo $herbarium_photo; ?>" class="herbarium-img"
                                    alt="<?php echo $common_name; ?> Herbarium">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $scientific_name; ?></h5>
                                <p class="card-text">Common Name: <?php echo $common_name; ?></p>
                                <!-- View Details button -->
                                <a href="plant_detail.php?scientific_name=<?php echo urlencode($scientific_name); ?>"
                                    class="btn btn-primary mt-2">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination links -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>