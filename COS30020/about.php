<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Classification</title>
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

    <?php include 'navbar.php'; ?>
    <div class="about_">
        <h1 class="text-center">Project Details</h1>

        <h2>PHP Version Used:</h2>
        <p><?php echo 'PHP Version: ' . phpversion(); ?></p>

        <h2>Tasks Completed (Assignment 1):</h2>
        <ul>
            <li>Task 1: First Page (index.php)</li>
            <li>Task 2: Main Menu Page (main_menu.php)</li>
            <li>Task 3: Plants Classification Page (classify.php)</li>
            <li>Task 4: Tutorial Page (tutorial.php)</li>
            <li>Task 5: Contribution Page (contribute.php)</li>
            <li>Task 6: View Plant detail Page (plant_detail.php)</li>
            <li>Task 7: Profile Page (profile.php)</li>
            <li>Task 8: Update Profile Page (update_profile.php)</li>
            <li>Task 9: Account Registration Page (registration.php)</li>
            <li>Task 10: Process Registration Page (process_registration.php)</li>
            <li>Task 11: Login Page (login.php)</li>
            <li>Task 12: About Page (about.php)</li>
        </ul>

        <h2>Tasks Completed (Assignment 2):</h2>
        <ul>
            <li><strong>What tasks have you not attempted or not completed?</strong>
                <ul>
                    <li>I have completed all the assigned tasks for this project. Each requirement outlined in the
                        assignment brief has been addressed and implemented successfully.</li>
                </ul>
            </li>
            <li><strong>Which parts did you have trouble with?</strong>
                <ul>
                    <li>Task 5: Setting up the integration with the Teachable Machine platform in `identify.php` was
                        challenging. Understanding how to load and use the exported model scripts effectively required
                        research and experimentation.</li>
                    <li>Ensuring that the `identify.php` correctly displayed predictions in real-time while maintaining
                        a clean and responsive user interface was another difficulty.</li>
                    <li>Handling edge cases, such as invalid image uploads or unclear predictions, required additional
                        debugging and testing to ensure a robust implementation.</li>
                </ul>
            </li>
            <li><strong>What would you like to do better next time?</strong>
                <ul>
                    <li>I would like to improve the User Interface (UI) to enhance the overall user experience and
                        ensure the design is intuitive and visually appealing. This includes improving the layout,
                        typography, and responsiveness for mobile and desktop users.</li>
                    <li>I would also like to dedicate more time to testing and optimizing the code to ensure better
                        performance and reduce potential bugs during implementation.</li>
                </ul>
            </li>
            <li><strong>What extension features/extra challenges have you done, or attempted, when creating the
                    site?</strong>
                <ul>
                    <li>Task 5: I implemented the `identify.php` functionality as an extension feature. This involved
                        integrating the Teachable Machine platform (https://teachablemachine.withgoogle.com/train/image)
                        to provide image recognition capabilities. I worked on setting up the required scripts to
                        interact with the trained model and incorporated the results into the site dynamically.</li>
                    <li>Customized the implementation to display accurate predictions and relevant feedback to users,
                        ensuring a smooth user experience.</li>
                    <li>Added error handling to gracefully manage cases where images were incompatible with the trained
                        model or where predictions were uncertain.</li>
                </ul>
            </li>
        </ul>


        <h2>Framework/3rd Party Libraries Used:</h2>
        <ul>
            <li>Bootstrap - Version 5.0.2</li>
            <li>Font Awesome - Version 6.4.2</li>
        </ul>



        <h2>Video Presentation (Assignment 1):</h2>
        <p>
            <a href="https://youtu.be/n7Cqo1MJ718" target="_blank" class="classify-button">Click here to view the video
                presentation.</a>
        </p>

        <h2>Video Presentation (Assignment 2):</h2>
        <p>
            <a href="https://youtu.be/37YMud9UJrs" target="_blank" class="classify-button">Click here to view the video
                presentation.</a>
        </p>

        <h2>Return to Home Page:</h2>
        <p>
            <a href="index.php" class="classify-button">Go to Home Page</a>
        </p>



    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>