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

// Initialize variables
$errors = [];
$successMessage = "";

// Define upload directories
$uploadDirImages = "img/";
$uploadDirDescription = "plants_description/";
if (!is_dir($uploadDirImages)) mkdir($uploadDirImages, 0777, true);
if (!is_dir($uploadDirDescription)) mkdir($uploadDirDescription, 0777, true);

// Function to ensure unique filenames
function getUniqueFileName($uploadDir, $fileName)
{
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
    $newFileName = $baseName;
    $counter = 1;

    while (file_exists($uploadDir . $newFileName . "." . $fileExtension)) {
        $newFileName = $baseName . "(" . $counter . ")";
        $counter++;
    }

    return $newFileName . "." . $fileExtension;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect = false;

    // Approve or Reject Plant
    if (isset($_POST['approve_plant']) || isset($_POST['reject_plant'])) {
        $plant_id = $_POST['plant_id'];
        $status = isset($_POST['approve_plant']) ? 'approved' : 'rejected';

        $stmt = $conn->prepare("UPDATE plant_table SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $plant_id);

        if ($stmt->execute()) {
            $successMessage = "Plant record status updated successfully!";
            $redirect = true; // Indicate redirect is needed
        } else {
            $errors[] = "Failed to update plant record status: " . $stmt->error;
        }

        $stmt->close();
    }

    // Add Plant
    if (isset($_POST['add_plant'])) {
        $scientific_name = $_POST['Scientific_Name'];
        $common_name = $_POST['Common_Name'];
        $family = $_POST['family'];
        $genus = $_POST['genus'];
        $species = $_POST['species'];
        $descriptionPath = "";
        $plantPhotoPath = "";
        $herbariumPhotoPath = "";

        // Handle Plant Photo
        if (isset($_FILES['plant_photo']) && $_FILES['plant_photo']['error'] === UPLOAD_ERR_OK) {
            $plantPhotoPath = $uploadDirImages . getUniqueFileName($uploadDirImages, $_FILES['plant_photo']['name']);
            if (!move_uploaded_file($_FILES['plant_photo']['tmp_name'], $plantPhotoPath)) {
                $errors[] = "Failed to upload plant photo.";
            }
        }

        // Handle Herbarium Photo
        if (isset($_FILES['herbarium_photo']) && $_FILES['herbarium_photo']['error'] === UPLOAD_ERR_OK) {
            $herbariumPhotoPath = $uploadDirImages . getUniqueFileName($uploadDirImages, $_FILES['herbarium_photo']['name']);
            if (!move_uploaded_file($_FILES['herbarium_photo']['tmp_name'], $herbariumPhotoPath)) {
                $errors[] = "Failed to upload herbarium photo.";
            }
        }

        // Handle Description File
        if (isset($_FILES['description']) && $_FILES['description']['error'] === UPLOAD_ERR_OK) {
            $descriptionPath = $uploadDirDescription . getUniqueFileName($uploadDirDescription, $_FILES['description']['name']);
            if (!move_uploaded_file($_FILES['description']['tmp_name'], $descriptionPath)) {
                $errors[] = "Failed to upload description file.";
            }
        }

        // Insert Plant Record
if (empty($errors)) {
    // Add JSON_UNESCAPED_SLASHES to prevent escaping forward slashes
    $imagesJson = json_encode([$plantPhotoPath, $herbariumPhotoPath], JSON_UNESCAPED_SLASHES);
    $stmt = $conn->prepare("INSERT INTO plant_table (Scientific_Name, Common_Name, family, genus, species, plants_image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssssss", $scientific_name, $common_name, $family, $genus, $species, $imagesJson, $descriptionPath);

    if ($stmt->execute()) {
        $successMessage = "Plant record added successfully!";
        $redirect = true; // Indicate redirect is needed
    } else {
        $errors[] = "Failed to add plant record: " . $stmt->error;
    }

    $stmt->close();
}

    }

    // Edit Plant
    if (isset($_POST['edit_plant'])) {
        $plant_id = $_POST['plant_id'];
        $scientific_name = $_POST['Scientific_Name'];
        $common_name = $_POST['Common_Name'];
        $family = $_POST['family'];
        $genus = $_POST['genus'];
        $species = $_POST['species'];
        $descriptionPath = $_POST['existing_description']; // Default to existing description path
        $images = json_decode($_POST['existing_images'], true); // Existing images
        $plantPhotoPath = $images[0];
        $herbariumPhotoPath = $images[1];

        // Update Plant Photo
        if (isset($_FILES['plant_photo']) && $_FILES['plant_photo']['error'] === UPLOAD_ERR_OK) {
            $plantPhotoPath = $uploadDirImages . getUniqueFileName($uploadDirImages, $_FILES['plant_photo']['name']);
            move_uploaded_file($_FILES['plant_photo']['tmp_name'], $plantPhotoPath);
        }

        // Update Herbarium Photo
        if (isset($_FILES['herbarium_photo']) && $_FILES['herbarium_photo']['error'] === UPLOAD_ERR_OK) {
            $herbariumPhotoPath = $uploadDirImages . getUniqueFileName($uploadDirImages, $_FILES['herbarium_photo']['name']);
            move_uploaded_file($_FILES['herbarium_photo']['tmp_name'], $herbariumPhotoPath);
        }

        // Update Description File
        if (isset($_FILES['description']) && $_FILES['description']['error'] === UPLOAD_ERR_OK) {
            $descriptionPath = $uploadDirDescription . getUniqueFileName($uploadDirDescription, $_FILES['description']['name']);
            move_uploaded_file($_FILES['description']['tmp_name'], $descriptionPath);
        }

        if (empty($errors)) {
            $imagesJson = json_encode([$plantPhotoPath, $herbariumPhotoPath]);
            $stmt = $conn->prepare("UPDATE plant_table SET Scientific_Name = ?, Common_Name = ?, family = ?, genus = ?, species = ?, plants_image = ?, description = ? WHERE id = ?");
            $stmt->bind_param("sssssssi", $scientific_name, $common_name, $family, $genus, $species, $imagesJson, $descriptionPath, $plant_id);

            if ($stmt->execute()) {
                $successMessage = "Plant record updated successfully!";
                $redirect = true; // Indicate redirect is needed
            } else {
                $errors[] = "Failed to update plant record: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    // Redirect to the same page to prevent duplicate form submissions
    if ($redirect) {
        header("Location: manage_plants.php");
        exit();
    }
}

// Handle deletion of plant record
if (isset($_GET['delete_plant'])) {
    $plant_id = intval($_GET['delete_plant']);

    // Query to delete the plant record
    $stmt = $conn->prepare("DELETE FROM plant_table WHERE id = ?");
    $stmt->bind_param("i", $plant_id);

    if ($stmt->execute()) {
        $successMessage = "Plant record deleted successfully!";
    } else {
        $errors[] = "Failed to delete plant record: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all plant records
$sql = "SELECT id, Scientific_Name, Common_Name, family, genus, species, plants_image, description, IFNULL(status, 'pending') as status FROM plant_table";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$plants = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbarium Archive - Manage Plants</title>
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

    .btn-approve {
        background-color: #28a745;
        border: none;
    }

    .btn-reject {
        background-color: #dc3545;
        border: none;
    }

    .thumbnail {
        width: 80px;
        height: auto;
        margin-right: 5px;
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
        <h1 class="text-center">Manage Plant Records</h1>

        <div class="mt-4">
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

        <!-- Add Plant -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPlantModal">Add Plant</button>

        <!-- Plant Records Table -->
        <h4>Existing Plants</h4>
        <h4 class="mb-4">Plant Records</h4>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Scientific Name</th>
                        <th>Common Name</th>
                        <th>Family</th>
                        <th>Genus</th>
                        <th>Species</th>
                        <th>Description</th>
                        <th>Photos</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plants as $plant): 
                $images = json_decode($plant['plants_image'], true);
                $plantPhoto = $images[0] ?? 'img/default_plant.jpg';
                $herbariumPhoto = $images[1] ?? 'img/default_herbarium.jpg';
            ?>
                    <tr>
                        <td><?php echo htmlspecialchars($plant['id']); ?></td>
                        <td><?php echo htmlspecialchars($plant['Scientific_Name']); ?></td>
                        <td><?php echo htmlspecialchars($plant['Common_Name']); ?></td>
                        <td><?php echo htmlspecialchars($plant['family']); ?></td>
                        <td><?php echo htmlspecialchars($plant['genus']); ?></td>
                        <td><?php echo htmlspecialchars($plant['species']); ?></td>
                        <td>
                            <?php if (!empty($plant['description'])): ?>
                            <a href="<?php echo htmlspecialchars($plant['description']); ?>" target="_blank"
                                class="btn btn-link btn-sm">View Description</a>
                            <?php else: ?>
                            <span class="text-muted">No Description</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <img src="<?php echo $plantPhoto; ?>" class="img-thumbnail" width="50" alt="Plant Photo">
                            <img src="<?php echo $herbariumPhoto; ?>" class="img-thumbnail" width="50"
                                alt="Herbarium Photo">
                        </td>
                        <td>
                            <span
                                class="badge <?php echo $plant['status'] === 'approved' ? 'bg-success' : ($plant['status'] === 'rejected' ? 'bg-danger' : 'bg-warning'); ?>">
                                <?php echo htmlspecialchars($plant['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <!-- Approve/Reject -->
                                <form action="manage_plants.php" method="POST" class="d-inline">
                                    <input type="hidden" name="plant_id" value="<?php echo $plant['id']; ?>">
                                    <button type="submit" name="approve_plant" class="btn btn-success btn-sm"
                                        <?php echo $plant['status'] === 'approved' ? 'disabled' : ''; ?>>Approve</button>
                                    <button type="submit" name="reject_plant" class="btn btn-danger btn-sm"
                                        <?php echo $plant['status'] === 'rejected' ? 'disabled' : ''; ?>>Reject</button>
                                </form>
                                <!-- Edit -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editPlantModal<?php echo $plant['id']; ?>">Edit</button>
                                <!-- Delete -->
                                <a href="manage_plants.php?delete_plant=<?php echo $plant['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this plant record?');">Delete</a>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Plant Modal -->
                    <div class="modal fade" id="editPlantModal<?php echo $plant['id']; ?>" tabindex="-1"
                        aria-labelledby="editPlantModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="manage_plants.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Plant Record</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="plant_id" value="<?php echo $plant['id']; ?>">
                                        <input type="hidden" name="existing_images"
                                            value='<?php echo htmlspecialchars($plant['plants_image']); ?>'>
                                        <input type="hidden" name="existing_description"
                                            value="<?php echo htmlspecialchars($plant['description']); ?>">

                                        <div class="mb-3">
                                            <label for="Scientific_Name" class="form-label">Scientific Name</label>
                                            <input type="text" class="form-control" name="Scientific_Name"
                                                value="<?php echo htmlspecialchars($plant['Scientific_Name']); ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="Common_Name" class="form-label">Common Name</label>
                                            <input type="text" class="form-control" name="Common_Name"
                                                value="<?php echo htmlspecialchars($plant['Common_Name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="family" class="form-label">Family</label>
                                            <input type="text" class="form-control" name="family"
                                                value="<?php echo htmlspecialchars($plant['family']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="genus" class="form-label">Genus</label>
                                            <input type="text" class="form-control" name="genus"
                                                value="<?php echo htmlspecialchars($plant['genus']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="species" class="form-label">Species</label>
                                            <input type="text" class="form-control" name="species"
                                                value="<?php echo htmlspecialchars($plant['species']); ?>" required>
                                        </div>
                                        <!-- Display Current Plant Photo -->
                                        <div class="mb-3">
                                            <label class="form-label">Current Plant Photo</label>
                                            <div class="mb-2">
                                                <?php if (!empty($plantPhoto)): ?>
                                                <img src="<?php echo htmlspecialchars($plantPhoto); ?>"
                                                    alt="Plant Photo" class="img-thumbnail"
                                                    style="width: 150px; height: 150px;">
                                                <?php else: ?>
                                                <span class="text-muted">No plant photo available</span>
                                                <?php endif; ?>
                                            </div>
                                            <label for="plant_photo" class="form-label">Update Plant Photo</label>
                                            <input type="file" class="form-control" name="plant_photo" accept="image/*">
                                        </div>

                                        <!-- Display Current Herbarium Photo -->
                                        <div class="mb-3">
                                            <label class="form-label">Current Herbarium Photo</label>
                                            <div class="mb-2">
                                                <?php if (!empty($herbariumPhoto)): ?>
                                                <img src="<?php echo htmlspecialchars($herbariumPhoto); ?>"
                                                    alt="Herbarium Photo" class="img-thumbnail"
                                                    style="width: 150px; height: 150px;">
                                                <?php else: ?>
                                                <span class="text-muted">No herbarium photo available</span>
                                                <?php endif; ?>
                                            </div>
                                            <label for="herbarium_photo" class="form-label">Update Herbarium
                                                Photo</label>
                                            <input type="file" class="form-control" name="herbarium_photo"
                                                accept="image/*">
                                        </div>
                                        <!-- Description File -->
                                        <div class="mb-3">
                                            <label class="form-label">Current Description File</label>
                                            <div class="mb-2">
                                                <?php if (!empty($plant['description'])): ?>
                                                <span class="text-info">
                                                    <?php echo htmlspecialchars(basename($plant['description'])); ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="text-muted">No description file available</span>
                                                <?php endif; ?>
                                            </div>
                                            <label for="description" class="form-label">Update Description File</label>
                                            <input type="file" class="form-control" name="description" accept=".pdf">
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_plant">Save
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

    <!-- Add Plant Modal -->
    <div class="modal fade" id="addPlantModal" tabindex="-1" aria-labelledby="addPlantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="manage_plants.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Plant Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="Scientific_Name" class="form-label">Scientific Name</label>
                            <input type="text" class="form-control" name="Scientific_Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="Common_Name" class="form-label">Common Name</label>
                            <input type="text" class="form-control" name="Common_Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="family" class="form-label">Family</label>
                            <input type="text" class="form-control" name="family" required>
                        </div>
                        <div class="mb-3">
                            <label for="genus" class="form-label">Genus</label>
                            <input type="text" class="form-control" name="genus" required>
                        </div>
                        <div class="mb-3">
                            <label for="species" class="form-label">Species</label>
                            <input type="text" class="form-control" name="species" required>
                        </div>
                        <div class="mb-3">
                            <label for="plant_photo" class="form-label">Plant Photo</label>
                            <input type="file" class="form-control" name="plant_photo" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="herbarium_photo" class="form-label">Herbarium Photo</label>
                            <input type="file" class="form-control" name="herbarium_photo" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description File</label>
                            <input type="file" class="form-control" name="description" accept=".pdf" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_plant">Add Plant</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>