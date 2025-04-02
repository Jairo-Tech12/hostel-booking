<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login-admin.php");
    exit();
}

// Logout function
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login-admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SEKU Hostel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    </head>
<body>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <h3 class="text-center text-white">Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="bi bi-house-door"></i> Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="students.php"><i class="bi bi-people"></i> Students</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="staff.php"><i class="bi bi-person-badge"></i> Staff</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="hostels.php"><i class="bi bi-building"></i> Hostels</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="bookings.php"><i class="bi bi-journal-bookmark"></i> Bookings</a>
            </li>
        </ul>
        
        <!-- Logout Button -->
        <a href="?logout=true" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn"><i class="bi bi-list"></i></button>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container">
            <div class="welcome-box">
                <h1>Welcome, Admin</h1>
                <p>Manage students,staff,hostels and bookings from this panel.</p>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
            toggleBtn.classList.toggle('shifted');
        });
    </script>

</body>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
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
            color: white !important;
        }

        .sidebar .nav-link.active {
            background-color: #1abc9c;
        }

        /* Logout Button */
        .btn-logout {
            background-color: #e74c3c;
            color: white;
            border-radius: 5px;
            padding: 10px;
            border: none;
            text-align: center;
            display: block;
            width: 80%;
            margin: 20px auto;
            text-decoration: none;
            text-align: center;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background-color: #c0392b;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s ease-in-out;
        }

        .welcome-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
        }

        /* Toggle Button */
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

        /* When Sidebar is Hidden */
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
