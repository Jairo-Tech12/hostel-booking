<?php
// Database connection
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'seku';

// Connect to the database
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize result variable for search results
$result_html = "";

// Handle hostel search
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $hostel_name = trim($_POST['hostel_name']);

    if (!empty($hostel_name)) {
        // Secure the query with prepared statements
        $sql = "SELECT * FROM hostels WHERE name LIKE ?";
        $stmt = $conn->prepare($sql);
        $search_term = "%" . $hostel_name . "%";
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Display hostel details
            $result_html .= "<h3>Available Hostels</h3>";
            $result_html .= "<div class='hostel-container'>";

            while ($row = $result->fetch_assoc()) {
                $result_html .= "<div class='hostel-card'>";
                $result_html .= "<h4>" . htmlspecialchars($row['name']) . "</h4>";
                $result_html .= "<p><strong>Price:</strong> $" . htmlspecialchars($row['price']) . "</p>";
                $result_html .= "<p><strong>Type:</strong> " . htmlspecialchars($row['type']) . "</p>";
                $result_html .= "<p><strong>Availability:</strong> <span class='" . strtolower(htmlspecialchars($row['availability'])) . "'>" . htmlspecialchars($row['availability']) . "</span></p>";
                // Link to detailed hostel page
                $result_html .= "<a href='hostel.php?id=" . $row['id'] . "' class='view-details'>View Details</a>";
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

// Start session and check user login status
session_start();
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
    if (empty($profile_image)) {
        $profile_image = "imgs/default-avatar.png";
    }
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
    <title>Home||Booking</title>
    <!-- <link rel="icon" type="image/x-icon" href="{% static 'imgs/home/favicon.ico.png' %}"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <!-- Navbar -->

<nav>
    <div class="nav-left">
        <a href="/" class="logo-container">
            <img src="imgs/logo.png" alt="SEKU Logo" class="logo-img">
            <span class="university-name">SOUTH EASTERN KENYA    UNIVERSITY</span>
        </a>
    </div>

    <div class="nav-right">
        <ul class="nav-links">
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
            <div class="user-menu">
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a href="?logout=true" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="burger" onclick="toggleNav()">&#9776;</div>
    </div>
</nav>




<p>
    <p>
        <p>
    <!-- Welcome Section -->
    <div id="carouselExampleCaptions" class="carousel slide">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img src= 'images/turkwel/tur2.jpg' alt="Image 1" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Hostel Booking</h5>
                <p>Welcome ..Book your desired Hostel and rooom..</p>
            </div>
            </div>
            <div class="carousel-item">
            <img src= 'images/victoria/vic.jpg'  alt="Image 1" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5> hostel Booking </h5>
                <p>Welcome ..Book your desired Hostel and rooom.</p>
            </div>
            </div>
            <div class="carousel-item">
            <img src='imgs/bar2.jpg' alt="Image 1" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Hostel Booking</h5>
                <p>Welcome ..Book your desired Hostel and rooom..</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src='imgs/a4.jpg'  alt="Image 1" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
            <h5>HostelBooking</h5>
            <p>Welcome ..Book your desired Hostel and rooom.</p>
            </div>
        </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
        </button>
</div>
<p>
<div class="search-container">
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
    </div>

 <div class="result-container" id="resultContainer">
        <div class="result-image" id="hostelImage"></div>
        <div class="result-title" id="hostelName"></div>
        <p class="result-details" id="hostelDetails"></p>
    </div>
<?php echo $result_html; ?>
<p>
    <p>
    <div class="container3">
        <h4>Welcome to the University Hostel</h4>
        <p>The university hostel is a home away from home for students, providing a comfortable and secure living environment.
        Our hostel is equipped with modern amenities to ensure students have the best experience while focusing on their studies.
        Each room is well-furnished, offering a comfortable bed, study table, chair, and storage space for personal belongings.</p>
    </div>

    <section class="hostel-features">
        <h2>What Seku Hostel Contains</h2>
        <div class="grid-container">

            <!-- Feature 1: Water Supply -->
            <div class="feature-card">
                <img src="book/water2.jpg" alt="Water Supply">
                <h3>Clean WateSupply</h3>
                <p>24/7 clean and safe drinking water available for all students.</p>
            </div>

            <!-- Feature 2: WiFi Internet -->
            <div class="feature-card">
                <img src="book/wifi.jpg" alt="WiFi Internet">
                <h3>High-Speed WiFi</h3>
                <p>Fast and reliable internet for study, research, and entertainment.</p>
            </div>

            <!-- Feature 3: Bathrooms -->
            <div class="feature-card">
                <img src="book/bath1.jpg" alt="Bathrooms">
                <h3>Modern Bathrooms</h3>
                <p>Hygienic and well-maintained restrooms for comfortable living.</p>
            </div>

            <!-- Feature 4: Rooms & Beds -->
            <div class="feature-card">
                <img src="book/bed.jpg" alt="Rooms and Beds">
                <h3> Rooms & Beds</h3>
                <p>Comfortable and spacious rooms designed for students.</p>
            </div>

            <!-- Feature 5: Dining Area -->
            <div class="feature-card">
                <img src="book/mess.jpg" alt="Dining Area">
                <h3>Dining Area</h3>
                <p>A well-furnished dining hall with a variety of healthy meals.</p>
            </div>

            <!-- Feature 6: CCTV Security -->
            <div class="feature-card">
                <img src="book/cct.jpg" alt="CCTV Security">
                <h3>24/7 CCTV</h3>
                <p>Secure hostel environment with round-the-clock monitoring.</p>
            </div>

            <!-- Feature 7: Health Unit -->
            <div class="feature-card">
                <img src="book/hu.jpg" alt="Health Unit">
                <h3>Health Unit</h3>
                <p>Medical assistance and health check-ups for students.</p>
            </div>

            <!-- Feature 8: Study Areas -->
            <div class="feature-card">
                <img src="book/libu.jpg" alt="Study Areas">
                <h3>Study Spaces</h3>
                <p>Quiet and well-equipped areas for studying and group work.</p>
            </div>

        </div>
    </section>
<h2>Find Your Perfect Hostel</h2>

<div class="grid-container">
    <!-- Hostel 1 -->
        <div class="hostel-card">
        <a href="book.php">
            <img src="images/turkana/tur1.jpg" alt="Seaside Hostel">
        </a>
        <div class="hostel-info">
            <h3>Turkana Hostel</h3>
            <p class="price">$4000 / month</p>
            <p>Type: Double Rooms</p>
            <p class="availability not-available">Not Available</p>
        </div>
    </div>
    

    <!-- Hostel 2 -->
    <div class="hostel-card">
        <a href="book.php">
            <img src="images/elementaita/ele1.jpg" alt="elementaita">
        </a>
        <div class="hostel-info">
            <h3>Elementaita Hostel</h3>
            <p class="price">$3500 / month</p>
            <p>Type: Double Rooms</p>
            <p class="availability available">Available</p>
        </div>
    </div>

    <!-- Hostel 3 -->
    <div class="hostel-card">
        <a href="book.php">
            <img src="images/victoria/vic.jpg" alt="Ocean View">
        </a>
        <div class="hostel-info">
            <h3>Victoria Hostel</h3>
            <p class="price">$5000 / month</p>
            <p>Type: Shared Rooms</p>
            <p class="availability available">Available</p>
        </div>
    </div>

    <!-- Hostel 4 -->
    <div class="hostel-card">
        <a href="book.php">
            <img src="images/turkwel/tur2.jpg" alt="City Hostel">
        </a>
        <div class="hostel-info">
            <h3>Turkwel Hostel</h3>
            <p class="price">$2800 / month</p>
            <p>Type: Double Rooms</p>
            <p class="availability available">Available</p>
        </div>
    </div>

    <!-- Hostel 5 -->
    <div class="hostel-card">
        <a href="book.php">
            <img src="images/athi/athi3.jpg" alt="ele1">
        </a>
        <div class="hostel-info">
            <h3>Athi Hostel</h3>
            <p class="price">$3200 / month</p>
            <p>Type: Shared Room</p>
            <p class="availability available"> Available</p>
        </div>
    </div>

    <!-- Hostel 6 -->
    <div class="hostel-card">
        <a href="book.php">
            <img src="images/baringo/bar4.jpg" alt="Lakeview Stay">
        </a>
        <div class="hostel-info">
            <h3>Baringo Hostel</h3>
            <p class="price">$4500 / month</p>
            <p>Type: Shared Rooms</p>
            <p class="availability available">Available</p>
        </div>
    </div>

    <!-- Hostel 7 -->
    <div class="hostel-card">
        <a href="book.php">
            <img src="images/yatta/yatta.jpg" alt="Mountain Base">
        </a>
        <div class="hostel-info">
            <h3>Yatta Hostel</h3>
            <p class="price">$2900 / month</p>
            <p>Type: Shared Room</p>
            <p class="availability available">Available</p>
        </div>
    </div>

    <!-- Hostel 8 -->
    <div class="hostel-card">
        <a href="book.php">
            <img src="images/magadi/maga.jpg" alt="Cozy Stay">
        </a>
        <div class="hostel-info">
            <h3>Magadi hostel</h3>
            <p class="price">$3300 / month</p>
            <p>Type: Shared Room</p>
            <p class="availability not-available">Not Available</p>
        </div>
    </div>   
    </div>
</div>


<div class="container1">
    <div class="content">
        <div class="box">
            <h2>How to Pay for Your Hostel</h2>
            <p class="description">Complete your payment in a few easy steps.</p>

            <div class="steps-list">
                <div class="instruction">
                    <span class="step-marker">1</span>
                    <p>Access your <strong>Mpesa</strong> menu.</p>
                </div>
                <div class="instruction">
                    <span class="step-marker">2</span>
                    <p>Navigate to <strong>"Lipa na Mpesa"</strong>.</p>
                </div>
                <div class="instruction">
                    <span class="step-marker">3</span>
                    <p>Choose <strong>"Paybill"</strong> and enter the business code: <strong>124567888</strong>.</p>
                </div>
                <div class="instruction">
                    <span class="step-marker">4</span>
                    <p>Use <strong>SEKUBANK</strong> as the account reference.</p>
                </div>
                <div class="instruction">
                    <span class="step-marker">5</span>
                    <p>Enter the required amount.</p>
                </div>
                <div class="instruction">
                    <span class="step-marker">6</span>
                    <p>Provide your Mpesa PIN and authorize.</p>
                </div>
                <div class="instruction">
                    <span class="step-marker">7</span>
                    <p>Wait for an Mpesa confirmation message.</p>
                </div>
            </div>

            <button class="submit-btn" onclick="showConfirmation()">Payment Completed</button>

            <p id="success-message" class="hidden">Your payment has been received. Thank you! 😊</p>
        </div>

        <!-- Image Container -->
        <div class="image-container">
            <img src="imgs/payment.jpg" alt="Payment">
        </div>
    </div>
</div>

    
</body>
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
    <a href="https://www.seku.ac.ke/" target="_blank" rel="noopener noreferrer">www.seku.ac.ke</a>| <a href="#">Terms of Service</a>
    
        <p>&copy; 2025 South Eastern Kenya University. All Rights Reserved. | ISO 9001 : 2015 Certified.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

<script>
    // Toggle User Menu Dropdown
    function toggleUserMenu() {
        document.querySelector('.user-menu').classList.toggle('show');
    }

    // Close the dropdown when clicking outside
    document.addEventListener('click', function (event) {
        const userMenu = document.querySelector('.user-menu');
        const userInfo = document.querySelector('.user-info');

        if (!userInfo.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.remove('show');
        }
    });

    // Search Hostel Function
    function searchHostel() {
        var hostelName = document.getElementById("hostelSearch").value.trim();
        if (hostelName !== "") {
            window.location.href = "search_hostel.php?hostel_name=" + encodeURIComponent(hostelName);
        } else {
            alert("Please enter a hostel name.");
        }
    }

    // Confirm Payment Function
    function confirmPayment() {
        document.getElementById("confirmation-message").classList.remove("hidden");
    }

    // Toggle Navigation for Mobile
    function toggleNav() {
        document.querySelector('.nav-links').classList.toggle('show');
    }

document.addEventListener('DOMContentLoaded', function() {
    const myCarousel = document.querySelector('#carouselExampleCaptions');
    const carousel = new bootstrap.Carousel(myCarousel, {
        interval: 5000, // Change slide every 5 seconds
        pause: 'hover', // Pause on hover
        wrap: true // Infinite looping
    });
    
    // Add smooth transition between slides
    myCarousel.addEventListener('slide.bs.carousel', function() {
        const inner = this.querySelector('.carousel-inner');
        inner.style.transition = 'transform 1s ease-in-out';
    });
});

</script>

<style>
    body{
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        background-color: #f7f7f7;
        padding-top: 70px;
        }

     /* Navigation Bar */
     * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #1c1f26;
    padding: 15px 20px;
   /* Full width */
    height: 60px;
    width: 100%;
    position: fixed; /* Sticks to top */
    top: 0;
    left: 0;
    align-items: center;
    z-index: 1000;
}

/* Left Section - Logo & Name */
.nav-left {
    display: flex;

}

.logo-container {
    display: flex;
    align-items: center;
    text-decoration: none;
}

.logo-img {
    height: 50px;
    margin-right: 2px;
}

.university-name {
    font-size: 22px;
    font-weight: bold;
    color:rgba(223, 92, 5, 0.94);
    font-family: 'Poppins', sans-serif;
}

/* Right Section - Nav Links & User Menu */
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
    overflow: hidden;
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

/* Responsive Styles */
@media (max-width: 1024px) {
    .nav-right {
        gap: 20px;
    }

    .university-name {
        font-size: 18px;
    }
}

@media (max-width: 768px) {
    nav {
        padding: 0 4%;
        height: 60px;
    }

    .burger {
        display: block;
    }

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
    }

    .nav-links.show {
        display: flex;
    }

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
h3 {
    text-align: center;
    color: #333;
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

/* Heading Styles */
h2 {
    text-align: center;
    color: #333;
    font-size: 28px;
    margin-bottom: 20px;
}
.container3 {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        h4 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            h4 {
                font-size: 20px;
            }
            
        }
        /* Hostel Features Section */
.hostel-features {
    text-align: center;
    padding: 40px;
    background-color: white;
}

.hostel-features h2 {
    color:rgb(248, 99, 0);
    margin-bottom: 30px;
    font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
}

/* Grid Container */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 0 40px;
    justify-content: center;
}

/* Feature Card Styling */
.feature-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.feature-card:hover {
    transform: scale(1.05);
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
}

.feature-card img {
    width: 100%;
    height: 150px;
    border-radius: 8px;
    object-fit: cover;
}

.feature-card h3 {
    margin-top: 15px;
    color:rgb(0, 255, 0);
    font-size: 17px;
    font-family: Georgia, 'Times New Roman', Times, serif;
}

.feature-card p {
    font-size: 0.95rem;
    margin-top: 10px;
}


        /* Grid Layout */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            max-width: 1400px;
            margin: auto;
            padding: 20px;
        }

        /* Hostel Card */
        .hostel-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .hostel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        }

        .hostel-card a {
            text-decoration: none;
            display: block;
            border-radius: 10px;
            overflow: hidden;
        }

        .hostel-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            transition: opacity 0.3s ease;
        }

        .hostel-card img:hover {
            opacity: 0.9;
        }

        .hostel-info {
            padding: 10px;
        }

        .hostel-info h3 {
            color: #333;
            font-size: 20px;
            margin: 10px 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .hostel-info p {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }

        .price {
            color:rgb(250, 172, 3);
            font-weight: bold;
        }

        .availability {
            font-weight: bold;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .available {
            background:rgb(3, 235, 57);
        }

        .not-available {
            background:rgb(255, 0, 25);
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .grid-container {
                grid-template-columns: repeat(1, 1fr);
            }
        }
 /* Center the carousel and set its size */
.carousel {
    width: 100%;
    max-width: 1290px;
    margin: auto;
    border-radius: 15px;
    overflow: hidden;
}

/* Carousel inner container */
.carousel-inner {
    transition: transform 0.7s ease-in-out; /* Smooth slide transition */
}
/* Style carousel images */
.carousel-item {
    position: relative;
    transition: opacity 1s ease-in-out; /* Fade transition */
}

.carousel-item img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 15px;
    filter: brightness(1.1) contrast(1.05);
    padding-top: 5px;
    transition: opacity 0.5s ease; 
}

/* Add subtle zoom animation to images */
.carousel-item.active img {
    transform: scale(1.05);
}

/* Style the captions */
.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    padding: 15px;
    border-radius: 10px;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    transition: all 0.5s ease;
    opacity: 0;
    transform: translateY(20px);
}

.carousel-item.active .carousel-caption {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.5s;
}
@keyframes slideIn {
    0% { opacity: 0.8; transform: scale(0.98); }
    100% { opacity: 1; transform: scale(1); }
}

/* Indicator animations */
.carousel-indicators button {
    transition: all 0.3s ease;
}

.carousel-indicators button.active {
    transform: scale(1.3);
    background-color: rgb(248, 99, 0);
}

/* Control buttons */
.carousel-control-prev,
.carousel-control-next {
    transition: opacity 0.3s ease;
}
/* Google Font */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}



/* Importing Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}
/* Main container */
.container1 {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

/* Content container (box + image) */
.content {
    display: flex;
    align-items: center;
    max-width: 1000px;
    width: 100%;
}

/* Instruction Box */
.box {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(115, 255, 0, 0.1);
    max-width: 500px;
    width: 50%;
    text-align: center;
}

/* Image container */
.image-container {
    width: 50%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures image covers the container */
    border-radius: 8px;
}

/* Responsive Design */



/* Heading */
h2 {
    color:rgb(255, 153, 0);
    font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;

}

/* Description text */
.description {
    color: #555;
    font-size: 14px;
    margin-bottom: 20px;
}

/* List of steps */
.steps-list {
    text-align: left;
}

/* Instruction container */
.instruction {
    background: #ECF0F1;
    padding: 12px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

/* Step marker */
.step-marker {
    background:rgb(10, 230, 2);
    color: white;
    font-weight: bold;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 10px;
}

/* Button styling */
.submit-btn {
    background:rgb(44, 190, 0);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 15px;
    width: 100%;
}

/* Button hover effect */
.submit-btn:hover {
    background:rgb(204, 133, 0);
}

/* Success message */
.hidden {
    display: none;
    color:rgb(216, 97, 0);
    margin-top: 15px;
    font-weight: bold;
}

/* Responsive adjustments */
@media (max-width: 500px) {
    .box {
        padding: 20px;
    }

    .instruction {
        flex-direction: column;  /* Stack text below marker */
        align-items: flex-start;
        padding: 10px;
    }
    @media (max-width: 768px) {
    .content {
        flex-direction: column;
    }

    .box, .image-container {
        width: 100%;
    }

    .image-container img {
        height: auto;
    }
}
    .step-marker {
        width: 25px;
        height: 25px;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .submit-btn {
        padding: 10px;
        font-size: 14px;
    }
}
    /* Footer Styles */
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
