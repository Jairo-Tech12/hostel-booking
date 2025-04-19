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
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --dark-color: #2b2d42;
            --light-color: #f8f9fa;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --sidebar-bg: linear-gradient(180deg, #2b2d42 0%, #1a1a2e 100%);
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            display: flex;
            color: #333;
            line-height: 1.6;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            padding: 1.5rem 0;
            transition: all var(--transition-speed) ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar h3 {
            color: white;
            padding: 0 1.5rem 1.5rem;
            margin: 0;
            font-weight: 600;
            font-size: 1.3rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav {
            padding: 1rem 0;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 0.8rem 1.5rem;
            margin: 0.2rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white !important;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        /* Logout Button */
        .sidebar .btn-danger {
            width: calc(100% - 3rem);
            margin: 1rem 1.5rem 0;
            padding: 0.6rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .btn-danger:hover {
            background: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
            transition: margin-left var(--transition-speed) ease;
            min-height: 100vh;
        }

        /* Welcome Box */
        .welcome-box {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            margin-top: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .welcome-box h1 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.2rem;
        }

        .welcome-box p {
            color: #6c757d;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Toggle Button */
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: calc(var(--sidebar-width) + 20px);
            font-size: 1.5rem;
            background: white;
            border: none;
            color: var(--dark-color);
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1001;
        }

        .toggle-btn:hover {
            background: var(--light-color);
            transform: scale(1.1);
        }

        /* When Sidebar is Hidden */
        .sidebar.hidden {
            transform: translateX(-100%);
            box-shadow: none;
        }

        .main-content.expanded {
            margin-left: 0;
            width: 100%;
        }

        .toggle-btn.shifted {
            left: 20px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.hidden {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .toggle-btn {
                left: 20px;
            }
        }

        /* Animation for smoother transitions */
        .sidebar, .main-content, .toggle-btn {
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Modern scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <h3 class="text-center">Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link active" href="admin.php"><i class="bi bi-person"></i> Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="students.php"><i class="bi bi-people"></i> Students</a></li>
            <li class="nav-item"><a class="nav-link" href="staff.php"><i class="bi bi-person-badge"></i> Staff</a></li>
            <li class="nav-item"><a class="nav-link" href="hostels.php"><i class="bi bi-building"></i> Hostels</a></li>
            <li class="nav-item"><a class="nav-link" href="bookings.php"><i class="bi bi-journal-bookmark"></i> Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="incidents.php"><i class="bi bi-exclamation-triangle"></i> Incidents</a></li>
        </ul>
        <!-- Logout Button -->
        <a href="?logout=1" class="btn btn-danger text-center">Logout</a>
    </nav>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container">
            <div class="welcome-box">
                <h1>Welcome, Admin</h1>
                <p>Manage students, staff, hostels and bookings from this panel.</p>
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
            
            // Store sidebar state in localStorage
            const isHidden = sidebar.classList.contains('hidden');
            localStorage.setItem('sidebarHidden', isHidden);
        });

        // Check localStorage for sidebar state on page load
        document.addEventListener('DOMContentLoaded', () => {
            const isHidden = localStorage.getItem('sidebarHidden') === 'true';
            if (isHidden) {
                sidebar.classList.add('hidden');
                mainContent.classList.add('expanded');
                toggleBtn.classList.add('shifted');
            }
        });
    </script>
</body>
</html>