<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "PlantBiodiversity";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$plantName = $_GET['plant_name'] ?? '';
$accuracy = $_GET['accuracy'] ?? ''; // Accuracy percentage from the request

if (!empty($plantName)) {
    $sql = "SELECT * FROM plant_table WHERE Scientific_Name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $plantName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $plantData = $result->fetch_assoc();

        $images = json_decode($plantData['plants_image'], true);
        if (!$images || !is_array($images)) {
            $images = []; // Fallback to an empty array if decoding fails
        }

        echo json_encode([
            'success' => true,
            'plantInfo' => [
                'Scientific_Name' => $plantData['Scientific_Name'],
                'Common_Name' => $plantData['Common_Name'],
                'Family' => $plantData['family'],
                'Genus' => $plantData['genus'],
                'Species' => $plantData['species'],
                'Images' => $images,
                'Description' => $plantData['description'],
                'Accuracy' => $accuracy // Include accuracy in response
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Plant data not available.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No plant name provided for search.']);
}

$conn->close();
?>