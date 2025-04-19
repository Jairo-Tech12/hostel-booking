<?php
session_start(); // Start session at the top

$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'seku';

// Connect to the database
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    exit();
}

// Initialize result variable
$result_html = "";

// Check if search is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $hostel_name = trim($_POST['hostel_name']);

    if (!empty($hostel_name)) {
        // Use prepared statements to prevent SQL injection
        $sql = "SELECT * FROM hostels WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$search_term = "%" . $hostel_name . "%"; 
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();


        if ($result->num_rows > 0) {
            // Display hostel details
            $result_html .= "<h3></h3>";
            $result_html .= "<div class='hostel-container'>";

            while ($row = $result->fetch_assoc()) {
                $result_html .= "<div class='hostel-card'>";
                $result_html .= "<h4>" . htmlspecialchars($row['name']) . "</h4>";
                $result_html .= "<p><strong>Price:</strong> $" . htmlspecialchars($row['price']) . "</p>";
                $result_html .= "<p><strong>Type:</strong> " . htmlspecialchars($row['type']) . "</p>";
                $result_html .= "<p><strong>Availability:</strong> <span class='" . strtolower(htmlspecialchars($row['availability'])) . "'>" . htmlspecialchars($row['availability']) . "</span></p>";
                // Add link to the detailed hostel page
                $result_html .= "<a href='book.php?id=" . urlencode($row['id']) . "' class='view-details'>View Details</a>";
                $result_html .= "</div>";
            }

            $result_html .= "</div>"; // Close hostel container
        } else {
            $result_html .= "<p class='not-found'>No hostels found matching your search.</p>";
        }
        $stmt->close();
    } else {
        $result_html .= "<p class='error'>Please enter a hostel name.</p>";
    }
}

// Check user login status
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch student name and profile image from the database
    $query = "SELECT name, profile_image FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($name, $profile_image);
    $stmt->fetch();
    $stmt->close();

    // Set default profile image if none is available
    $profile_image = !empty($profile_image) ? $profile_image : "imgs/default-avatar.png";
} else {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy(); // Destroy session
    session_unset(); // Unset session variables
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About|Us</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    
</head>
<body>

    <!-- Navbar -->
    <nav>
    <div class="nav-left">
        <a href="/" class="logo-container">
            <img src="imgs/logo.png" alt="SEKU Logo" class="logo-img">
            <span class="university-name">SOUTH EASTERN KENYA UNIVERSITY</span>
        </a>
    </div>

    <div class="nav-right">
        <ul class="nav-links" id="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="book.php">Book Hostel</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>

        <!-- User Dropdown -->
        <div class="user-dropdown">
            <div class="user-info" onclick="toggleUserMenu()">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="user-avatar">
                <span>Hi, <?php echo htmlspecialchars($name); ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-menu" id="user-menu">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="?logout=true" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="burger" onclick="toggleNav()">&#9776;</div>
    </div>
</nav>

<p>

    <!-- Hero Section (Image under Navbar) -->
    <div class="hero">
        Welcome to University Hostels
    </div>
    <!-- <div class="search-container">
        <h3>Find Your Ideal Hostel</h3>
        <form method="POST" action="">
            <div class="search-box">
                <select name="hostel_name" required>
                    <option value="" disabled selected>Choose a Hostel</option>
                    <option value="Victoria">Victoria</option>
                    <option value="Elementaita">Elementaita</option>
                    <option value="Turkwel">Turkwel</option>
                    <option value="Magadi">Magadi</option>
                    <option value="Athi">Athi</option>
                    <option value="Yataa">Yatta</option>
                    <option value="Turkana">Turkana</option>
                </select>
                <button type="submit" name="search" class="find-btn">Find Hostel</button>
                
            </div>
        </form>
    </div> -->

<div class="result-container" id="resultContainer">
        <div class="result-image" id="hostelImage"></div>
        <div class="result-title" id="hostelName"></div>
        <p class="result-details" id="hostelDetails"></p>
    </div>
<?php echo $result_html; ?>
    <!-- About Us Section -->
<section class="about-us py-5">
    <div class="container">
        <div class="row align-items-center">
            <!-- Text Content -->
            <div class="col-lg-6">
                <h2 class="section-title">About Our University Hostels</h2>
                <p class="about-text">
                    The University is a fully-fledged institution and the successor to the South Eastern University College (SEUCO), previously a Constituent College of the University of Nairobi. Since its inception in 2008, the university has experienced tremendous growth, with a current student population of approximately 8,000.
                </p>
                <p class="about-text">
                    We take pride in our highly qualified teaching staff, including Professors and Lecturers, most of whom hold Ph.D.s and have vast experience in teaching and research. Our commitment is to produce well-rounded graduates who contribute meaningfully to the nation's development.
                </p>
                <p class="about-text">
                    SEKU envisions becoming a world-class university renowned for quality teaching, creative learning, innovative research, and strong community outreach.
                </p>
            </div>

            <!-- Image Section -->
            <div class="col-lg-6">
                <div class="image-container">
                    <img src="images/turkwel/tur2.jpg" alt="University Hostel" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Our Guiding Principles Section -->
<section class="guiding-principles py-5">
    <div class="container">
        <h3 class="section-title text-center mb-5">Our Guiding Principles</h3>
        <div class="row justify-content-center">
            
            <!-- Motto -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="info-box shadow-sm text-center">
                    <div class="icon"><i class="fas fa-quote-left"></i></div>
                    <h4 class="fw-bold">Motto</h4>
                    <p class="fst-italic">"Arid To Green"</p>
                </div>
            </div>

            <!-- Vision -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="info-box shadow-sm text-center">
                    <div class="icon"><i class="fas fa-eye"></i></div>
                    <h4 class="fw-bold">Vision</h4>
                    <p>A globally competitive Centre of Excellence in Research, Teaching, Learning, Community Service, Innovation, and Commercialization.</p>
                </div>
            </div>

            <!-- Mission -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="info-box shadow-sm text-center">
                    <div class="icon"><i class="fas fa-bullseye"></i></div>
                    <h4 class="fw-bold">Mission</h4>
                    <p>To provide quality education through research, teaching, extension, innovation, and entrepreneurship, with an emphasis on food and nutrition security, health, engineering and technology, environment, and natural resource management for sustainable development.</p>
                </div>
            </div>

            <!-- Quality Policy Statement -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="info-box shadow-sm text-center">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h4 class="fw-bold">Quality Policy</h4>
                    <p>We are committed to providing quality education that meets stakeholders' needs through teaching, research, extension, and entrepreneurship while maintaining excellence, integrity, and compliance with ISO 9001:2015 standards.</p>
                </div>
            </div>
            
        </div>
    </div>
</section>
<div class="col-lg-12">
    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d997.0081933378035!2d37.753548699999996!3d-1.3089646!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f3ef04e0d0f5f%3A0xe88f97a7c67d4d36!2sSEKU%20Main%20Campus!5e0!3m2!1sen!2ske!4v1712568123456" 
            width="100%" 
            height="400" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>





    <!-- Footer -->
    <footer class="footer">
    <div class="footer-container">
        <!-- Quick Links Section -->
        <div class="footer-column quick-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="book.php">Book Hostel</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
        
            </ul>
        </div>

        <!-- Social Links Section (Centered) -->
        <div class="footer-column social-links">
            <h3>objective</h3>
            <ul>
                <li>Streamline Booking Processes</li> 
                <li>Comrade Experience</li>
                <li>Data Security and Privacy</li>
                <li>Transparency</li>

            </ul>
        </div>

        <!-- Follow Us Section (Aligned to Right) -->
        <div class="footer-column follow-us">
            <h3>Follow Us</h3>
            <ul>
                <li><a href="https://www.instagram.com/sekumain/" class="social-icon" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i> Instagram</a></li>
                <li><a href="https://twitter.com/sekumain" class="social-icon" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i> Twitter</a></li>
                <li><a href="https://facebook.com/sekumain" class="social-icon"><i class="fab fa-facebook"></i> facebook</a></li>
                

            </ul>
        </div>
        <div class="footer-column follow-me">
            <h3>Contact us</h3>
            <ul>
                <li><a href="https://wa.me/254724341480?" class="social-icon" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a></li>
                <li><a href="mailto:jairokibet81@gmail.com" class="social-icon"><i class="fas fa-envelope"></i> Email</a></li>
                <li><a href="tel:+254724341480" class="social-icon"><i class="fas fa-phone"></i> 0724341480</a></li>
                <li><a href="#" class="social-icon"><i class="fab fa-"></i> Instagram</a></li> 

            </ul>
        </div>
    </div>
    
    <!-- Footer Bottom (Copyright) -->
    <div class="footer-bottom">
    <a href="https://www.seku.ac.ke/" target="_blank" rel="noopener noreferrer">www.seku.ac.ke</a> | <a href="#">Terms of Service</a>
    
    <p>&copy; 2025 South Eastern Kenya University. All Rights Reserved. | ISO 9001 : 2015 Certified.</p>
    </div>
</footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle Navigation Menu
    function toggleNav() {
    document.getElementById("nav-links").classList.toggle("show");
}

function toggleUserMenu() {
    document.getElementById("user-menu").classList.toggle("show");
}

// Close menu when clicking outside
document.addEventListener("click", function (event) {
    let nav = document.getElementById("nav-links");
    let userMenu = document.getElementById("user-menu");

    if (!event.target.closest(".burger") && !event.target.closest(".nav-links")) {
        nav.classList.remove("show");
    }
    if (!event.target.closest(".user-info")) {
        userMenu.classList.remove("show");
    }
});

</script>


</body>
<style>
        /* Navbar Styling */
        /* Main Navbar */
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #1c1f26;
    padding: 15px 20px;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

/* Left Section - Logo */
.nav-left {
    display: flex;
    align-items: center;
}

.logo-container {
    display: flex;
    align-items: center;
    text-decoration: none;
}

.logo-img {
    height: 50px;
    margin-right: 5px;
}

.university-name {
    font-size: 22px;
    font-weight: bold;
    color: rgb(250, 171, 0);
    font-family: 'Poppins', sans-serif;
}

/* Right Section */
.nav-right {
    display: flex;
    align-items: center;
    gap: 30px;
}

/* Navigation Links */
.nav-links {
    list-style: none;
    display: flex;
    gap: 25px;
}

.nav-links a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    font-weight: 500;
    padding: 10px 15px;
    transition: 0.3s ease-in-out;
}

.nav-links a:hover {
    color: #ffb400;
    transform: scale(1.05);
}

/* User Dropdown */
.user-dropdown {
    position: relative;
    display: flex;
    align-items: center;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    padding: 8px 15px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    transition: 0.3s ease;
}

.user-info:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid #ffb400;
    object-fit: cover;
}

/* Dropdown Menu */
.user-menu {
    display: none;
    position: absolute;
    top: 50px;
    right: 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    min-width: 180px;
    z-index: 1000;
    opacity: 0;
    transform: translateY(-10px);
    transition: 0.3s ease-in-out;
}

.user-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    color: #333;
    font-size: 14px;
    text-decoration: none;
    transition: 0.3s ease-in-out;
}

.user-menu a:hover {
    background: #ffb400;
    color: white;
}

.logout-btn {
    color: #d9534f !important;
    font-weight: bold;
}

.logout-btn i {
    color: #d9534f;
}

.user-menu.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

/* Burger Menu */
.burger {
    display: none;
    font-size: 26px;
    cursor: pointer;
    color: white;
}

/* 🔹 Responsive Styles */
@media (max-width: 1024px) {
    .nav-right {
        gap: 20px;
    }

    .university-name {
        font-size: 18px;
    }
}

/* 🔹 Mobile Responsive */
@media (max-width: 768px) {
    nav {
        padding: 10px 4%;
        height: 60px;
    }

    /* Show Burger Menu */
    .burger {
        display: block;
        cursor: pointer;
    }

    /* Mobile Menu */
    .nav-links {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        right: 10px;
        background: rgba(28, 31, 38, 0.95);
        padding: 15px;
        border-radius: 10px;
        text-align: right;
        transition: 0.3s ease-in-out;
        width: 200px;
    }

    .nav-links.show {
        display: flex;
    }

    /* Adjust user dropdown */
    .user-dropdown {
        margin-top: 10px;
    }

    .user-info {
        padding: 6px 10px;
        font-size: 14px;
    }

    .user-menu {
        position: static;
        width: 100%;
        text-align: center;
    }

    .university-name {
        font-size: 16px;
    }
}

/* Search Container */
.search-container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.15);
    text-align: center;
    width: 100%;
    max-width: 700px;
    display: flex;
    flex-direction: column;
    /* Positioning */
    position: relative; /* Allows controlled movement */
    top: 20px; /* Moves it down */
    left: 330px; /* Moves slightly to the right */
    
    /* Animation */
    transition: all 0.3s ease-in-out;
}
        .search-container h3 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Search Box */
        .search-box {
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
    width: 100%;
}

        /* Styled Dropdown */
        select {
    width: 80%;
    padding: 12px;
    border: 2px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    outline: none;
    }
    select:hover {
    border-color: rgb(72, 255, 0);
    }
    select::-ms-expand {
    display: block;
    }

    select:focus {
            border-color:rgb(255, 136, 0);
            box-shadow: 0 0 8px rgba(255, 153, 0, 0.3);
        }

        /* Different Button Styles Based on Keyword */
        .find-btn {
    width: 40%;
    padding: 10px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

        .find-btn:hover {
            background:rgb(255, 136, 0);
        }

        .explore-btn {
            width: 100%;
            padding: 12px;
            background:rgb(184, 133, 23); /* Teal for "Explore" */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .explore-btn:hover {
            background:rgb(150, 67, 19);
        }

        .browse-btn {
            width: 100%;
            padding: 12px;
            background: #fd7e14; /* Orange for "Browse" */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .browse-btn:hover {
            background: #e3640b;
        }

        .view-btn {
            width: 100%;
            padding: 12px;
            background: #6610f2; /* Purple for "View Hostel" */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .view-btn:hover {
            background: #520dc2;
        }

        /* Responsive Design */
        @media screen and (min-width: 600px) {
            .search-box {
                flex-direction: row;
            }

            select {
                width: 70%;
            }

            button {
                width: 30%;
            }
        }
/* Hostel Container */
.hostel-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

/* Hostel Card */
.hostel-card {
    background-color: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
    transition: transform 0.3s;
}

.hostel-card:hover {
    transform: scale(1.05);
}

/* Hostel Availability */
.available {
    color: green;
    font-weight: bold;
}

.unavailable {
    color: red;
    font-weight: bold;
}

/* View Details Button */
.view-details {
    text-align: center;
    display: inline-block;
    margin-top: 10px;
    padding: 8px 14px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s;
}

.view-details:hover {
    background-color:rgb(255, 187, 0);
}

.not-found, .error {
    text-align: center;
    color: red;
    font-weight: bold;
}


/* Responsive Design */
@media (max-width: 768px) {
    .search-box {
        flex-direction: column;
        gap: 10px;
    }

    input[type="text"],
    #search_btn {
        width: 100%;
    }
}
/* Hostel Container */
.hostel-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

/* Hostel Card */
.hostel-card {
    background-color: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
    transition: transform 0.3s;
}

.hostel-card:hover {
    transform: scale(1.05);
}

/* Hostel Availability */
.available {
    color: green;
    font-weight: bold;
}

.unavailable {
    color: red;
    font-weight: bold;
}

/* View Details Button */
.view-details {
    text-align: center;
    display: inline-block;
    margin-top: 10px;
    padding: 8px 14px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s;
}

.view-details:hover {
    background-color:rgb(255, 187, 0);
}

.not-found, .error {
    text-align: center;
    color: red;
    font-weight: bold;
}


/* Responsive Design */
@media (max-width: 768px) {
    .search-box {
        flex-direction: column;
        gap: 10px;
    }

    input[type="text"],
    #search_btn {
        width: 100%;
    }
}


        /* Hero Section (Image under Navbar) */
        .hero {
    background: url('images/turkwel/tur3.jpg') no-repeat center center/cover;
    width: 99%;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.7);
    border-radius: 15px;
    margin-top: 90px;/* Avoids overlap with fixed navbar */
    margin-left: 7px; 
    padding: 20px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero {
        height: 300px; /* Reduce height on tablets */
        font-size: 1.8rem;
    }
}

@media (max-width: 768px) {
    .hero {
        height: 250px; /* Reduce height on smaller screens */
        font-size: 1.5rem;
        text-shadow: 1px 1px 8px rgba(0, 0, 0, 0.7);
    }
}

@media (max-width: 480px) {
    .hero {
        height: 200px; /* Even smaller height for mobile */
        font-size: 1.2rem;
    }
}

 /* About Us Section Styling */
.about-us {
    background-color: #f8f9fa;
    text-align: justify;
}

.section-title {
    font-size: 2rem;
    font-weight: bold;
    color: #004080;
    margin-bottom: 20px;
}

.about-text {
    font-size: 1.1rem;
    color: #333;
    line-height: 1.6;
    margin-bottom: 15px;
}

/* Image Styling: Ensures Image Fills Container */
.image-container {
    width: 100%;
    height: 100%; /* Makes sure the div takes up full height */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); /* Soft shadow */
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image fills the container without distortion */
}

/* Responsive Design */
@media (max-width: 768px) {
    .section-title {
        font-size: 1.8rem;
        text-align: center;
    }
    
    .about-text {
        font-size: 1rem;
        text-align: center;
    }

    .image-container {
        height: 250px; /* Adjust for mobile screens */
    }
} 
.guiding-principles .info-box {
    height: 250px; /* Fixed height for all containers */
    overflow-y: auto; /* Enable vertical scrolling if content overflows */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px;
    box-sizing: border-box;
    border-radius: 8px; /* Optional, for rounded corners */
    transition: all 0.3s ease-in-out; /* Smooth transition for hover effect */
    cursor: pointer; /* Change cursor to pointer for interaction */
}

.guiding-principles .icon {
    font-size: 2.5rem;
    color: #007bff; /* Bootstrap primary color */
    margin-bottom: 20px;
}

.guiding-principles h4 {
    font-weight: bold;
    font-size: 1.25rem;
    color: #333;
}

.guiding-principles p {
    flex-grow: 1;
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
}

.guiding-principles .section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #343a40; /* Dark gray */
    text-transform: uppercase;
    letter-spacing: 1px; /* Slight spacing for modern touch */
}

.info-box {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background-color: #f8f9fa; /* Light gray background */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Light shadow for modern look */
    border-radius: 8px; /* Rounded corners for a modern feel */
    padding: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover transition */
}

.info-box:hover {
    transform: translateY(-5px); /* Lift effect on hover */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); /* Deeper shadow on hover */
}

.icon {
    font-size: 2rem;
    color: #007bff; /* Bootstrap primary color */
    margin-bottom: 15px;
}

h4 {
    font-size: 1.25rem;
    color: #333;
}

p {
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .guiding-principles .info-box {
        height: auto; /* Allow height to adjust for small screens */
    }
    .guiding-principles h4 {
        font-size: 1.125rem; /* Slightly smaller heading for mobile */
    }
    .guiding-principles p {
        font-size: 0.9rem; /* Slightly smaller text for mobile */
    }
}

@media (max-width: 576px) {
    .guiding-principles .icon {
        font-size: 2rem; /* Adjust icon size for small screens */
    }
}

.map-container {
    width: 94%; /* Take up 94% of the available width */
    margin: 0 auto; /* Center it horizontally */
    padding-left: 3%; /* Add 3% padding on the left */
    padding-right: 3%; /* Add 3% padding on the right */
    border-radius: 15px; /* Optional rounded corners */
    overflow: hidden; /* Ensure no overflow outside the container */
}

.map-container iframe {
    width: 100%; /* Ensure iframe fills the container */
    height: 400px; /* Fixed height for the map */
    border: 5px; /* Remove the iframe border */
}


.footer {
        background-color: #1c1f26;
        color: #ffffff;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
    }

    .footer-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr; /* 3 equal columns */
        grid-gap: 15px;
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px 0;
    
    }

    .footer-column {
        padding: 10px;
        box-sizing: border-box;
    }

    .footer-column h3 {
        font-size: 18px;
        color: #f3730a;
        margin-bottom: 15px;
        text-transform: uppercase;
        border-bottom: 2px solid #f3730a;
        display: inline-block;
        padding-bottom: 5px;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    .footer-column ul li {
        margin-bottom: 10px;
    }

    .footer-column ul li a {
        text-decoration: none;
        color: #ffffff;
        font-size: 14px;
        transition: color 0.3s ease;
    }

    .footer-column ul li a:hover {
        color: #f3730a;
    }

    .footer-column p {
        font-size: 14px;
        line-height: 1.8;
    }

    .footer-column p a {
        color: #f3730a;
        text-decoration: none;
    }

    .footer-column p a:hover {
        text-decoration: underline;
    }

    .social-links {
        list-style: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .social-links li {
        margin-bottom: 10px;
    }

    .social-links li a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #ffffff;
        font-size: 14px;
        transition: color 0.3s ease;
    }

    .social-links li a i {
        margin-right: 8px;
        font-size: 18px;
    }

    .social-links li a:hover {
        color: #f3730a;
    }

    .footer-bottom {
        text-align: center;
        margin-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        padding-top: 10px;
        font-size: 14px;
        color: white;
    }
    .footer-bottom a{
        color: white;
    }
    .footer-bottom a:hover{
        color: #f3730a;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .footer-container {
            grid-template-columns: 1fr; /* Stack all columns */
            gap: 20px;
        }

        .footer-column {
            text-align: center;
        }

        .social-links {
            justify-content: center;
            align-items: center;
        }
    }
    .footer-content {
        text-align: center; 
        padding: 20px;
    }



    </style>
</html>
