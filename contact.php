<?php
// Start session
session_start();

include 'config.php';

// Initialize variables
$result_html = "";
$name = "";
$profile_image = "imgs/default-avatar.png";



// Check user session for student login
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch student name and profile image
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
    session_destroy();
    session_unset();
    header("Location: login.php");
    exit();
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

// Handle Comment Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'], $_POST['reg_no'], $_POST['message'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $reg_no = htmlspecialchars(trim($_POST['reg_no']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!empty($name) && !empty($reg_no) && !empty($message)) {
        $sql = "INSERT INTO comments (name, reg_no, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $reg_no, $message);

        if ($stmt->execute()) {
            echo "<script>alert('Comment submitted successfully!'); window.location.href='contact.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}

// Handle Incident Report Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['incident-title'])) {
    $title = $_POST['incident-title'];
    $description = $_POST['incident-description'];
    $category = $_POST['incident-category'];
    $priority = $_POST['incident-priority'];
    $attachment = "";

    // Handle file upload (if any)
    if (isset($_FILES['incident-attachments']) && $_FILES['incident-attachments']['error'] == 0) {
        $file_name = $_FILES['incident-attachments']['name'];
        $file_tmp = $_FILES['incident-attachments']['tmp_name'];
        $upload_dir = "uploads/"; // Folder to store uploaded files
        $attachment = $upload_dir . basename($file_name);

        // Move uploaded file to the uploads directory
        move_uploaded_file($file_tmp, $attachment);
    }

    // Prepare SQL query to insert incident details
    $sql = "INSERT INTO incidents (title, description, category, priority, attachment)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $title, $description, $category, $priority, $attachment);

    if ($stmt->execute()) {
        // Redirect to the same page to prevent duplicate submissions
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Close database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>contact||Us</title>
    <!-- <link rel="icon" type="image/x-icon" href="{% static 'imgs/home/favicon.ico.png' %}"> -->

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


    <!-- Welcome Section -->
    <div id="carouselExampleCaptions" class="carousel slide">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
        
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img src= 'contact/c3.jpg' alt="Image 1" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5>Contact us</h5>
                <p>Welcome ...Reach us,call us and talk to us.</p>
            </div>
            </div>
            <div class="carousel-item">
            <img src= 'contact/c5.jpg'  alt="Image 1" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5> contact us </h5>
                <p>Welcome ...reach us in any social media accounts..</p>
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
    </div>

 <div class="result-container" id="resultContainer">
        <div class="result-image" id="hostelImage"></div>
        <div class="result-title" id="hostelName"></div>
        <p class="result-details" id="hostelDetails"></p>
    </div>
<?php echo $result_html; ?> -->
<p>

<div class="wrapper">
    <div class="row justify-content-center">
        <!-- Left Side: Form Container -->
        <div class="col-lg-5 col-md-6 col-sm-12 mb-4">
            <div class="form-container">
                <h2>Write Message</h2>
                <form action="contact.php" method="POST">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="reg_no">Reg No:</label>
                    <input type="text" id="reg_no" name="reg_no" required>
                    
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4" required></textarea>
                    
                    <button type="submit" class="custom-submit-btn">Submit</button>
                </form>
            </div>
        </div>

        <!-- Right Side: Contact Information -->
        <div class="col-lg-5 col-md-6 col-sm-12 mb-4">
            <div class="contact-info">
                <h3>Contact Us</h3>
                <div class="social-links">
                    <a href="https://www.facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook"></i> Facebook</a>
                    <a href="https://www.instagram.com" target="_blank" class="social-icon"><i class="fab fa-instagram"></i> Instagram</a>
                    <a href="https://twitter.com" target="_blank" class="social-icon"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="https://wa.me/yourphonenumber" target="_blank" class="social-icon"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                    <a href="https://www.tiktok.com" target="_blank" class="social-icon"><i class="fab fa-tiktok"></i> TikTok</a>
                </div>
                <div class="contact-details">
                    <p><i class="fas fa-envelope"></i> Email: <a href="mailto:your-email@example.com">seku@hostels.ac.ke</a></p>
                    <p><i class="fas fa-phone"></i> Phone: <a href="tel:+25472435346">+254 724 35346</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<h3 class="form-title">Report an Incident</h3>
<form class="incident-form" action="contact.php" method="POST" enctype="multipart/form-data">
    <div class="input-group">
    <label for="incident-title">Incident Title</label>
    <input type="text" id="incident-title" name="incident-title" placeholder="Enter incident title" required>
</div>

    <div class="input-group">
    <label for="incident-description">Incident Description</label>
    <textarea id="incident-description" name="incident-description" placeholder="Describe the incident" required></textarea>
    </div>

    <div class="input-group">
    <label for="incident-category">Category</label>
    <select id="incident-category" name="incident-category" required>
        <option value="maintenance">Maintenance</option>
        <option value="security">Security</option>
        <option value="accident">accident</option>
        <option value="rule_violation">Rule Violation</option>
    </select>
    </div>
<div class="input-group">
    <label for="incident-priority">Priority</label>
    <select id="incident-priority" name="incident-priority" required>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>
    </div>

    <div class="input-group">
    <label for="incident-attachments">Attachments (Optional)</label>
    <input type="file" id="incident-attachments" name="incident-attachments">
    </div>

    <button type="submit" class="submit-btn">Submit Incident</button>
</form>

        

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
    <a href="https://www.seku.ac.ke/" target="_blank" rel="noopener noreferrer">www.seku.ac.ke</a> | <a href="#">Terms of Service</a>
    
    <p>&copy; 2025 South Eastern Kenya University. All Rights Reserved. | ISO 9001 : 2015 Certified.</p>
    </div>
</footer>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

<script>
    // Toggle Navigation Menu
    function toggleNav() {
        document.querySelector(".nav-links").classList.toggle("show");
    }

    // Search Hostel Function
    function searchHostel() {
        var hostelName = document.getElementById("hostelSearch").value.trim();
        if (hostelName !== "") {
            window.location.href = "search_hostel.php?hostel_name=" + encodeURIComponent(hostelName);
        } else {
            alert("Please enter a hostel name.");
        }
    }
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
    body{
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        background-color: #f7f7f7
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

.carousel {
    margin-top: 100px;
    width: 100%; /* Adjust width as needed */
    max-width: 1290px; /* Prevents it from getting too large */
    margin: auto; /* Centers the carousel */
    border-radius: 15px; /* Rounds the corners */
    overflow: hidden; /* Prevents image overflow */
    transition: transform 0.3s ease;
}

/* Style carousel images */
.carousel-inner img {
    width: 100%; /* Ensures images fill the carousel */
    height: 400px; /* Adjust height for consistency */
    object-fit: cover; /* Ensures images cover the area without distortion */
    border-radius: 15px; /* Smooth rounded corners */
    filter: brightness(1.1) contrast(1.05); /* Enhances image clarity */
    padding-top: 5px;
    transition: transform 0.3s ease;
}

/* Style the captions */
.carousel-caption {
    background: rgba(0, 0, 0, 0.5); 
    padding: 15px;
    border-radius: 10px;
    color: #fff; /* Ensures text is visible */
    font-family: 'Poppins', sans-serif; /* Modern font */
}

/* Style caption headings */
.carousel-caption h5 {
    font-size: 24px;
    font-weight: bold;
    color:rgb(248, 99, 0)
}

/* Style caption text */
.carousel-caption p {
    font-size: 12px;
}


        /* Main Wrapper - Flexbox for Row Layout */
       /* General Styles */
/* General Styles */
/* General Styles */
.wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin: 0 auto;
    padding: 60px 5%;
    max-width: 3000px;
}

.row {
    width: 100%;
}

.form-container, .contact-info {
    width: 100%;
    max-width: 90%; /* Ensures both sections use 90% of the page */
    margin: 0 auto;
    padding: 30px;
    border-radius: 12px;
    background-color: #ffffff;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.form-container:hover, .contact-info:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.form-container h2, .contact-info h3 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
    font-weight: 700;
}

.form-container label {
    display: block;
    margin-bottom: 10px;
    font-size: 1rem;
    font-weight: 600;
    color: #555;
}

.form-container input,
.form-container textarea {
    width: 100%;
    padding: 14px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    background-color: #f9f9f9;
    transition: border 0.3s ease;
}

.form-container input:focus,
.form-container textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.4);
}

.form-container button.custom-submit-btn {
    background-color:rgb(0, 255, 42);
    color: white;
    border: none;
    padding: 15px 50px;
    border-radius: 8px;
    cursor: pointer;
    width: 350px; /* Use auto width to adjust dynamically */
    max-width:700px; 
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.form-container button.custom-submit-btn:hover {
    background-color:rgb(255, 187, 1);
    transform: scale(1.05);
}

/* Styling Contact Info Section */
.contact-info {
    font-family: 'Arial', sans-serif;
}

.contact-info h3 {
    font-size: 2rem;
    color: #333;
    font-weight: 700;
    margin-bottom: 25px;
}

.social-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.social-icon {
    display: flex;
    align-items: center;
    font-size: 1.25rem;
    color: #555;
    text-decoration: none;
    padding: 10px;
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.social-icon i {
    margin-right: 10px;
}

.social-icon:hover {
    background-color: #f0f0f0;
    color:rgb(255, 187, 0);
}

.contact-details p {
    font-size: 1rem;
    color: #555;
    margin: 15px 0;
}

.contact-details a {
    text-decoration: none;
    color:rgba(66, 204, 3, 0.8);
    font-weight: 600;
    transition: color 0.3s ease;
}

.contact-details a:hover {
    color:rgb(179, 161, 0);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-container,
    .contact-info {
        width: 100%; /* Use 100% width on smaller screens */
        margin-bottom: 20px;
    }

    .form-container h2, .contact-info h3 {
        font-size: 1.5rem;
    }

    .form-container input,
    .form-container textarea,
    .form-container button.custom-submit-btn {
        padding: 12px;
    }
}

/* Global Styles */


h3.form-title {
  text-align: center;
  color: #333;
  font-size: 26px;
  font-weight: 600;
  margin: 40px 0;
}

.incident-form {
  width: 90%;
  max-width: 650px;
  margin: 0 auto;
  padding: 30px;
  background-color: #fff;
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease;
}

.incident-form:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
}

.input-group {
  margin-bottom: 20px;
}

label {
  display: block;
  font-size: 16px;
  color: #555;
  margin-bottom: 8px;
}

input[type="text"],
textarea,
select,
input[type="file"] {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
  background-color: #f1f3f6;
  transition: border-color 0.3s ease;
}

input[type="text"]:focus,
textarea:focus,
select:focus,
input[type="file"]:focus {
  outline: none;
  border-color: #007bff;
}

textarea {
  resize: vertical;
  min-height: 120px;
}

input[type="file"] {
  padding: 10px 12px;
}

button[type="submit"] {
  width: 100%;
  padding: 14px;
  background-color: #28a745;
  color: #fff;
  font-size: 16px;
  font-weight: 500;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
  background-color: #218838;
}

button[type="submit"]:focus {
  outline: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .incident-form {
    padding: 20px;
  }

  h3.form-title {
    font-size: 22px;
    margin: 30px 0;
  }

  input[type="text"],
  textarea,
  select,
  input[type="file"] {
    padding: 10px;
  }

  button[type="submit"] {
    padding: 12px;
  }
}

/* Mobile Friendly */
@media (max-width: 480px) {
  h3.form-title {
    font-size: 20px;
    margin: 20px 0;
  }

  .incident-form {
    padding: 15px;
  }

  input[type="text"],
  textarea,
  select,
  input[type="file"] {
    padding: 8px;
  }

  button[type="submit"] {
    padding: 10px;
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
</styl>
</html>