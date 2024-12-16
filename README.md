link to presentation: https://youtu.be/37YMud9UJrs

1. Login Information:

    Email: isaac@gmail.com
    Password: password1234!

2. This project applies Object-Oriented Programming (OOP) principles across multiple pages for database interactions. 

For example, the database connection is demonstrated in manage_accounts.php:
    $conn = new mysqli($servername, $username, $password, $database, $port);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

3. Directories Information:
   -Profile Images Directory:
   Location:img/profile_images

   -Plant and Herbarium Images Directory:
   Location: img/

   -Description(PDF for plants information):
   Location: data/description

4. my_model Folder
   This folder contains the files generated after training a model using Teachable Machine's Image Classifier. These files are necessary for running the trained model in a web application.

   Contents:

    metadata.json:
        Contains class labels and metadata about the model.
        Used to map predictions to class names.

    model.json:
        Defines the structure of the neural network.
        Required to load the model in TensorFlow.js.

    weights.bin:
        Contains the trained weights of the model.
        Used for making predictions.
