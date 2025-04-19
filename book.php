<?php
include 'config.php';

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
                $result_html .= "<a href='book.php?id=" . $row['id'] . "' class='view-details'>View Details</a>";
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

$conn->close();

?>
<?php
include 'config.php';

// Set UTF-8 encoding
mysqli_set_charset($conn, "utf8");

// Fetch hostels from the database
$sql = "SELECT * FROM hostels";
$result = $conn->query($sql);

// Check if query execution was successful
if ($result === false) {
    die("Query failed: " . $conn->error);
}

// Check if any results were retrieved
if ($result->num_rows > 0) {
    echo "";
} else {
    echo "No hostels found in the database.<br>";
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
    <title>Book||Hostel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Custom CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    
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


<!-- BANNER IMAGE WITH TEXT -->
<div class="banner">
    <div class="banner-overlay">
        <h1>Book Hostel</h1>
    </div>
</div>

<!-- SEARCH SECTION -->
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

<?php echo $result_html; ?>
<p>

<div class="container my-5">
    <?php if ($result->num_rows > 0) { ?>
        <?php while ($row = $result->fetch_assoc()) { 
            // Assign 4 unique images for each hostel based on its name
            $hostel_images = [
                "Victoria Hostel" => ["images/victoria/vic4.jpg", "images/victoria/vic1.jpg", "images/victoria/vic.jpg", "images/victoria/gate.jpg"],
                "Turkwel Hostel" => ["images/turkwel/tur.jpg", "images/turkwel/tur1.jpg", "images/turkwel/tur2.jpg", "images/turkwel/tur3.jpg"],
                "Baringo Hostel" => ["images/baringo/bar.jpg", "images/baringo/bar1.jpg", "images/baringo/bar2.jpg", "images/baringo/bar4.jpg"],
                "Athi Hostel" => ["images/athi/athi1.jpg", "images/athi/athi3.jpg", "images/athi/athu2.jpg", "images/athi/athi4.jpg"],
                "Yatta Hostel" => ["images/yatta/yatta.jpg", "images/yatta/yatta1.jpg", "images/yatta/yatta2.jpg", "images/yatta/yatta3.jpg"],
                "Magadi Hostel" => ["images/magadi/maga.jpg", "images/magadi/maga1.jpg", "images/magadi/maga4.jpg", "images/magadi/maga3.jpg"],
                "Turkana Hostel" => ["images/turkana/tur.jpg", "images/turkana/tur1.jpg", "images/turkana/tur.jpg", "images/turkana/tur3.jpg"],
                "Elementaita Hostel" => ["images/elementaita/ele1.jpg", "images/elementaita/ele4.jpg", "images/elementaita/ele2.jpg", "images/elementaita/ele3.jpg"]
            ];

            // If the hostel name is not in the array, use a default set of images
            $images = isset($hostel_images[$row['name']]) ? $hostel_images[$row['name']] : ["imgs/default_1.jpg", "imgs/default_2.jpg", "imgs/default_3.jpg", "imgs/default_4.jpg"];
        ?>
            <div class="row justify-content-center mb-5">
                <div class="col-lg-12">
                    <div class="card hostel-card">
                        <div class="row g-0">
                            <!-- Image Carousel -->
                            <div class="col-md-8">
                                <div id="carousel<?php echo $row['id']; ?>" class="carousel slide hostel-slider" data-bs-ride="carousel">
                                    <!-- Indicators -->
                                    <div class="carousel-indicators">
                                        <?php foreach ($images as $index => $image) { ?>
                                            <button type="button" data-bs-target="#carousel<?php echo $row['id']; ?>" 
                                                    data-bs-slide-to="<?php echo $index; ?>" 
                                                    class="<?php echo ($index === 0) ? 'active' : ''; ?>" 
                                                    aria-current="<?php echo ($index === 0) ? 'true' : 'false'; ?>" 
                                                    aria-label="Slide <?php echo $index + 1; ?>"></button>
                                        <?php } ?>
                                    </div>

                                    <!-- Image Slides -->
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $index => $image) { ?>
                                            <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>" data-bs-interval="3000">
                                                <img src="<?php echo $image; ?>" class="d-block w-100 hostel-photo" 
                                                    alt="<?php echo htmlspecialchars($row['name']); ?> Image <?php echo $index + 1; ?>">
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <!-- Navigation Buttons -->
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $row['id']; ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $row['id']; ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Hostel Details -->
                            <div class="col-md-4 d-flex flex-column justify-content-center p-5">
                                <h2 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h2> 
                                <p class="card-text"><i class="fas fa-bed"></i> Total Rooms: <?php echo htmlspecialchars($row['total_rooms']); ?></p>
                                <p class="card-text"><i class="fas fa-wifi"></i> WiFi: Available</p>
                                <p class="card-text"><i class="fas fa-dollar-sign"></i> Price: Ksh <?php echo number_format($row['price'], 2); ?></p>
                                <p class="card-text"> Ratings: ⭐⭐⭐⭐☆ </p>

                                <!-- Book Now Button (Redirects to Payment Page) -->
                                <a href="payment.php?id=<?php echo urlencode($row['id']); ?>&name=<?php echo urlencode($row['name']); ?>&price=<?php echo urlencode($row['price']); ?>" 
    class="btn btn-primary-book w-100">
    Book Now
</a>

                            </div> <!-- End Hostel Details -->
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p class="text-center">No hostels available at the moment.</p>
    <?php } ?>
</div>

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
<!-- Ensure Bootstrap JS is Loaded -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle Navigation Menu
    function toggleNav() {
        document.querySelector(".nav-links").classList.toggle("show");
    }

    // Toggle User Menu Dropdown
    function toggleUserMenu() {
        document.querySelector('.user-menu').classList.toggle('show');
    }

    // Close the dropdown when clicking outside
    document.addEventListener('click', function (event) {
        const userMenu = document.querySelector('.user-menu');
        const userInfo = document.querySelector('.user-info');

        if (userMenu && userInfo && !userInfo.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.remove('show');
        }
    });
</script>


<style>
    
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
    color:rgb(250, 171, 0);
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


.banner {
    background: url('images/victoria/vic.jpg') no-repeat center center/cover; /* Add your banner image */
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
    margin-top: 55px;/* Avoids overlap with fixed navbar */
    margin-left: 7px; 
    padding: 20px;
}



.banner h1 {
    font-size: 50px;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
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
.search-wrapper {
    text-align: center;
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    width: 90%;
    margin: 20px auto;
}

/* Section Heading */
.search-heading {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
}

/* Search Form */
.search-form {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

/* Input Field */
.search-input {
    width: 500px;
    padding: 14px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    transition: all 0.3s ease-in-out;
}

.search-input:focus {
    border-color: #28a745;
    outline: none;
    box-shadow: 0px 0px 5px rgba(40, 167, 69, 0.5);
}

/* Search Button */
#search_button {
    padding: 12px 20px;
    background-color: #28a745;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    border-radius: 6px;
    transition: background 0.3s ease-in-out;
}

#search_button:hover {
    background-color:rgb(240, 160, 11);
}

/* LISTINGS SECTION */
.listing-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-top: 30px;
}

/* Hostel Card */
.hostel-card {
    background-color: white;
    padding: 18px;
    border-radius: 10px;
    box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.1);
    width: 320px;
    text-align: center;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.hostel-card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
}

/* Availability Labels */
.status-available {
    color: #28a745;
    font-weight: bold;
}

.status-unavailable {
    color: #dc3545;
    font-weight: bold;
}

/* Action Button */
.view-details {
    display: inline-block;
    margin-top: 12px;
    padding: 10px 16px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.3s ease-in-out;
}

.view-details:hover {
    background-color: #0056b3;
}

/* Error Message */
.no-results, .error-message {
    text-align: center;
    color: #dc3545;
    font-weight: bold;
    margin-top: 20px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
        gap: 10px;
    }

    .search-input {
        width: 100%;
    }

    #search_button {
        width: 100%;
    }
}
/* Increased Container & Image Size */
.hostel-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    background-color: white;
    width: 90%;
    margin: auto;
}

.hostel-card:hover {
    transform: scale(1.03);
}

/* Enlarged Image Section */
.hostel-slider img {
    height: 500px;
    width: 100%;
    object-fit: cover;
    border-radius: 12px 0 0 12px;
}

/* Enlarged Text & Spacing */
.card-title {
    font-size: 30px;
    font-weight: bold;
}

.card-text {
    font-size: 20px;
    margin-bottom: 10px;
}

/* Updated Book Now Button */
.btn-primary-book {
    background-color: #28a745;
    border: none;
    font-size: 18px;
    font-weight: bold;
    padding: 14px;
}

.btn-primary-book:hover {
    background-color:rgb(235, 180, 0);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hostel-card {
        width: 95%;
    }

    .hostel-slider img {
        height: 450px;
    }

    .card-title {
        font-size: 26px;
    }
}

@media (max-width: 992px) {
    .hostel-card {
        width: 100%;
    }

    .hostel-slider img {
        height: 400px;
    }

    .p-5 {
        padding: 30px !important;
    }
}

@media (max-width: 768px) {
    .hostel-card {
        width: 100%;
    }

    .hostel-slider img {
        height: 350px;
        border-radius: 12px 12px 0 0;
    }

    .p-5 {
        padding: 20px !important;
    }

    .col-md-8, .col-md-4 {
        width: 100%;
    }
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