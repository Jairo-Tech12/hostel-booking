<?php
session_start();

// Restrict access if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    
</head>
<body>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
    <h3 class="text-center text-white">Staff Dashboard</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="staff.php">
                <i class="bi bi-person"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="hostel.php">
                <i class="bi bi-building"></i> Manage Hostel
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="bookings.php">
                <i class="bi bi-journal-bookmark"></i> View Bookings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="incidents.php">
                <i class="bi bi-exclamation-triangle"></i> Incidents
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="message.php">
                <i class="bi bi-chat-dots"></i> Messages
            </a>
        </li>   
    </ul>


        <a href="logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn"><i class="bi bi-list"></i></button>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="profile-container">
            <div class="profile-card">
                <i class="bi bi-person-circle profile-icon"></i>
                <h2>Welcome, <?php echo $_SESSION['staff_name']; ?>!</h2>
                <p class="profile-info"><i class="bi bi-envelope"></i> Email: <?php echo $_SESSION['staff_email']; ?></p>
                <p class="profile-info"><i class="bi bi-house-door"></i> Assigned Hostel: <?php echo $_SESSION['staff_hostel']; ?></p>
                <a href="logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggle-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('main-content').classList.toggle('expanded');
            this.classList.toggle('shifted');
        });
    </script>

</body>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c3e50;
            padding-top: 20px;
            transition: all 0.3s ease-in-out;
        }

        .sidebar .nav-link {
            color: white !important;
            padding: 12px 20px;
            display: block;
            font-size: 18px;
        }

        .sidebar .nav-link:hover {
            background-color: #1abc9c;
        }

        

        /* Logout Button */
        .btn-logout {
            display: block;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            padding: 12px;
            border-radius: 8px;
            width: 80%;
            margin: 20px auto;
            transition: all 0.3s ease-in-out;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: linear-gradient(45deg, #c0392b, #a93226);
            transform: scale(1.05);
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s ease-in-out;
        }

        .toggle-btn {
            position: fixed;
            top: 15px;
            left: 260px;
            font-size: 24px;
            background: none;
            border: none;
            color: #2c3e50;
            cursor: pointer;
            transition: left 0.3s ease-in-out;
        }

        /* Hide sidebar */
        .sidebar.hidden {
            width: 0;
            overflow: hidden;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .toggle-btn.shifted {
            left: 10px;
        }

        /* Profile Container */
        .profile-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
        }

        .profile-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .profile-card h2 {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .profile-info {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
        }

        .profile-icon {
            font-size: 60px;
            color: #1abc9c;
            margin-bottom: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .main-content {
                margin-left: 0;
            }

            .toggle-btn {
                left: 10px;
            }
        }
    </style>
</html>
