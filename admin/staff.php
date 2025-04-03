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

// Handle staff deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM staff WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Staff deleted successfully!'); window.location.href='staff.php';</script>";
    } else {
        echo "<script>alert('Error deleting staff!');</script>";
    }
}

// Fetch all staff members
$sql = "SELECT * FROM staff ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<?php

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
    <title>Staff | Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">


</head>
<body>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <h3 class="text-center text-white">Admin Panel</h3>
        <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link active" href="admin.php"><i class="bi bi-person"></i> Profile</a>
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
        
        <li class="nav-item">
                <a class="nav-link" href="incidents.php">
                    <i class="bi bi-exclamation-triangle"></i> Incidents
                </a>
            </li>
            </ul>
        <!-- Logout Button -->
        <a href="logout.php" class="btn btn-danger text-center mt-3">Logout</a>
    </nav>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn"><i class="bi bi-list"></i></button>

    <div class="main-content" id="main-content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Registered Staff</h2>
                <a href="register-staff.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Staff</a>
            </div>

            <!-- Search Box -->
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="🔍 Search by Name...">
            </div>

            <!-- Staff Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>National ID</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Hostel</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="staffTable">
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['national_id']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone_number']; ?></td>
                                <td><?php echo $row['hostel']; ?></td>
                                <td><button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggle-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('main-content').classList.toggle('expanded');
            this.classList.toggle('shifted');
        });

        document.getElementById('search').addEventListener('input', function() {
            let searchValue = this.value.toLowerCase();
            let rows = document.querySelectorAll('#staffTable tr');

            rows.forEach(row => {
                let name = row.cells[1].innerText.toLowerCase();
                row.style.display = name.includes(searchValue) ? "" : "none";
            });
        });

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this staff member?")) {
                window.location.href = "staff.php?delete_id=" + id;
            }
        }
    </script>

</body>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
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

        

        .btn-logout {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 5px;
            width: 80%;
            margin: 20px auto;
            text-align: center;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background-color: #c0392b;
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
