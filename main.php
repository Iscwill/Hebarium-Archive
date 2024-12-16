<?php
$servername = "localhost";
$username = "root";
$password = "";
$port = 3307;

$conn = new mysqli($servername, $username, $password, "", $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS PlantBiodiversity";
if ($conn->query($sql) === TRUE) {
    echo "Database PlantBiodiversity created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->select_db("PlantBiodiversity");

// Create user_table
$userTableSql = "CREATE TABLE IF NOT EXISTS user_table (
    email VARCHAR(50) NOT NULL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dob DATE NULL,
    gender VARCHAR(6) NOT NULL,
    contact_number VARCHAR(15) NULL,
    hometown VARCHAR(50) NOT NULL,
    profile_image VARCHAR(100) NULL
)";
if ($conn->query($userTableSql) === TRUE) {
    echo "Table user_table created successfully or already exists.<br>";
} else {
    echo "Error creating user_table: " . $conn->error . "<br>";
}

// Create account_table
$accountTableSql = "CREATE TABLE IF NOT EXISTS account_table (
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type VARCHAR(5) NOT NULL CHECK (type IN ('admin', 'user')),
    FOREIGN KEY (email) REFERENCES user_table(email) ON DELETE CASCADE ON UPDATE CASCADE
)";
if ($conn->query($accountTableSql) === TRUE) {
    echo "Table account_table created successfully or already exists.<br>";
} else {
    echo "Error creating account_table: " . $conn->error . "<br>";
}

// Create plant_table
$plantTableSql = "CREATE TABLE IF NOT EXISTS plant_table (
    id INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Scientific_Name VARCHAR(50) NOT NULL,
    Common_Name VARCHAR(50) NOT NULL,
    family VARCHAR(100) NOT NULL,
    genus VARCHAR(100) NOT NULL,
    species VARCHAR(100) NOT NULL,
    plants_image VARCHAR(255) NULL,
    description VARCHAR(255) NULL
)";
if ($conn->query($plantTableSql) === TRUE) {
    echo "Table plant_table created successfully or already exists.<br>";
} else {
    echo "Error creating plant_table: " . $conn->error . "<br>";
}

// Add status column to plant_table if it doesn't exist
$addStatusColumnSql = "ALTER TABLE plant_table 
                       ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'";
if ($conn->query($addStatusColumnSql) === TRUE) {
    echo "Column 'status' added successfully.<br>";
} else {
    if (strpos($conn->error, 'Duplicate column name') === false) {
        echo "Error adding column 'status': " . $conn->error . "<br>";
    } else {
        echo "Column 'status' already exists.<br>";
    }
}

// Populate user_table with dummy data
$dummyUsers = [
    ["admin@swin.edu.my", "Admin", "User", "1980-01-01", "Male", "1234567890", "Kuching", "/img/admin.jpg"],
    ["isaac@example.com", "Isaac", "William", "2003-02-15", "Male", "1234567891", "Miri", "/img/admin.jpg"],
    ["jane.smith@example.com", "Jane", "Smith", "1985-10-10", "Female", "1234567892", "Sibu", "/img/admin.jpg"],
    ["alex.jones@example.com", "Alex", "Jones", "2000-07-20", "Non-Binary", "1234567893", "Bintulu", "/img/admin.jpg"]
];

$stmt = $conn->prepare("INSERT INTO user_table (email, first_name, last_name, dob, gender, contact_number, hometown, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE email=email");
foreach ($dummyUsers as $user) {
    $stmt->bind_param("ssssssss", ...$user);
    if ($stmt->execute()) {
        echo "Inserted/Updated user record for: " . $user[0] . "<br>";
    } else {
        echo "Error inserting/updating user record for: " . $user[0] . " - " . $stmt->error . "<br>";
    }
}

// Populate account_table with dummy data
$dummyAccounts = [
    ["admin@swin.edu.my", password_hash("admin", PASSWORD_DEFAULT), "admin"],
    ["isaac@example.com", password_hash("password123", PASSWORD_DEFAULT), "user"],
    ["jane.smith@example.com", password_hash("mypassword", PASSWORD_DEFAULT), "user"],
    ["alex.jones@example.com", password_hash("securepass", PASSWORD_DEFAULT), "user"]
];

$stmt = $conn->prepare("INSERT INTO account_table (email, password, type) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE email=email");
foreach ($dummyAccounts as $account) {
    $stmt->bind_param("sss", ...$account);
    if ($stmt->execute()) {
        echo "Inserted/Updated account record for: " . $account[0] . "<br>";
    } else {
        echo "Error inserting/updating account record for: " . $account[0] . " - " . $stmt->error . "<br>";
    }
}

// Populate plant_table with dummy data
$dummyPlants = [
    [NULL, "Ficus elastica", "Rubber Plant", "Moraceae", "Ficus", "elastica", '["img/con1.png", "img/con1.1.png"]', "data/description/con1.pdf", "pending"],
    [NULL, "Monstera deliciosa", "Swiss Cheese Plant", "Araceae", "Monstera", "deliciosa", '["img/con2.png", "img/con2.1.png"]', "data/description/con2.pdf", "pending"],
    [NULL, "Aloe vera", "Aloe Vera", "Asphodelaceae", "Aloe", "vera", '["img/con3.png", "img/con3.1.png"]', "data/description/con3.pdf", "pending"],
    [NULL, "Eucalyptus globulus", "Blue Gum", "Myrtaceae", "Eucalyptus", "globulus", '["img/con4.png", "img/con4.1.png"]', "data/description/con4.pdf", "pending"],
    [NULL, "Mentha spicata", "Spearmint", "Lamiaceae", "Mentha", "spicata", '["img/con5.png", "img/con5.1.png"]', "data/description/con5.pdf", "pending"]
];

$stmt = $conn->prepare("INSERT INTO plant_table (id, Scientific_Name, Common_Name, family, genus, species, plants_image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($dummyPlants as $plant) {
    $stmt->bind_param("issssssss", ...$plant);
    if ($stmt->execute()) {
        echo "Inserted/Updated plant record for: " . $plant[1] . "<br>";
    } else {
        echo "Error inserting/updating plant record for: " . $plant[1] . " - " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();

echo "Tables populated successfully with dummy data.<br>";
?>