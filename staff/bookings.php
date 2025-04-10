<?php
session_start();

// Database connection
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'seku';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all bookings
$sql = "SELECT * FROM bookings ORDER BY booking_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings | SEKU University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c3e50;
            padding-top: 20px;
            transition: width 0.3s ease-in-out;
        }

        .sidebar.hidden {
            width: 0;
            overflow: hidden;
            transition: width 0.3s ease-in-out;
        }

        .main-content.expanded {
            margin-left: 0;
            width: 100%;
        }

        .toggle-btn.shifted {
            left: 10px !important;
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

        .table-responsive {
            overflow-x: auto;
            white-space: nowrap;
        }

        .table {
            table-layout: fixed;
            width: 100%;
        }

        .table th, .table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            text-align: center;
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
</head>
<body>

<!-- Sidebar Navigation -->
<nav class="sidebar" id="sidebar">
    <h3 class="text-center text-white">Staff Dashboard</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="staff.php">
                <i class="bi bi-person"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="hostel.php">
                <i class="bi bi-building"></i> Manage Hostel
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="bookings.php">
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
        <li class="nav-item">
    <a class="nav-link" href="issue-item.php"> 
        <i class="bi bi-check-circle"></i> Issued Items  
    </a>
</li>

    </ul>

    <a href="logout.php" class="btn btn-danger text-center mt-3">Logout</a>
</nav>

<!-- Toggle Button -->
<button class="toggle-btn" id="toggle-btn"><i class="bi bi-list"></i></button>

<div class="main-content">
    <div class="container">
        <h2 class="text-center">All Bookings</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                        <th>S/No</th>
                        <th>Student Name</th>
                        <th>Reg Number</th>
                        <th>Hostel</th>
                        <th>Room Number</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['reg_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['hostel_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                            </tr>
                        <?php } 
                    } else { ?>
                        <tr><td colspan="6">No bookings found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-btn').addEventListener('click', function () {
        let sidebar = document.getElementById('sidebar');
        let mainContent = document.querySelector('.main-content');

        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('expanded');

        if (sidebar.classList.contains('hidden')) {
            this.style.left = "10px";
        } else {
            this.style.left = "260px";
        }
    });
</script>

</body>
</html>
