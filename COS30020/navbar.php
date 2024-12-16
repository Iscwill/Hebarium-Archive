<!-- Navbar Start -->
<nav class="navbar navbar-expand-sm" id="navbar">
    <a href="index.html" class="navbar-brand" id="logo"><img src="./img/logo.png" alt=""></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
        <span><i class="fa-solid fa-bars"></i></span>
    </button>
    <div class="collapse navbar-collapse" id="mynavbar">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a href="main_menu.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="selectDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">Option</a>
                <ul class="dropdown-menu" aria-labelledby="selectDropdown">
                    <li><a href="classify.php" class="dropdown-item">Plants Classification</a></li>
                    <li><a href="tutorial.php" class="dropdown-item">Tutorial</a></li>
                    <li><a href="identify.php" class="dropdown-item">Identify</a></li>
                    <li><a href="contribute.php" class="dropdown-item">Contribution</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link">Profile</a>
            </li>
            <li class="nav-item">
                <a href="update_profile.php" class="nav-link">Update Profile</a>
            </li>
            <li class="nav-item">
                <a href="about.php" class="nav-link">About</a>
            </li>
        </ul>

        <form class="d-flex">
            <a href="logout.php" class="classify-button" class="nav-link">Log Out</a>
        </form>


    </div>
</nav>
<!-- Navbar End -->