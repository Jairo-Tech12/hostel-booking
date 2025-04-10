<?php
session_start();

// Restrict access if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'seku';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assign_items'])) {
        // Assign items
        $booking_id = $_POST['booking_id'];
        $key_issued = isset($_POST['key_issued']) ? 1 : 0;
        $mattress_issued = isset($_POST['mattress_issued']) ? 1 : 0;
        $bucket_issued = isset($_POST['bucket_issued']) ? 1 : 0;
        $curtain_issued = isset($_POST['curtain_issued']) ? 1 : 0;

        // Get booking details
        $booking_sql = "SELECT * FROM bookings WHERE id = ?";
        $stmt = $conn->prepare($booking_sql);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $booking_result = $stmt->get_result();
        $booking = $booking_result->fetch_assoc();

        if ($booking) {
            // Check if record already exists
            $check_sql = "SELECT * FROM issued_items WHERE hostel_id = ? AND room_number = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("is", $booking['student_id'], $booking['room_number']);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result->num_rows > 0) {
                // Update existing record
                $update_sql = "UPDATE issued_items SET 
                              key_issued = ?, 
                              mattress_issued = ?, 
                              bucket_issued = ?, 
                              curtain_issued = ? 
                              WHERE hostel_id = ? AND room_number = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("iiiisi", $key_issued, $mattress_issued, $bucket_issued, $curtain_issued, $booking['student_id'], $booking['room_number']);
            } else {
                // Insert new record
                $insert_sql = "INSERT INTO issued_items 
                              (hostel_id, name, hostel_name, room_number, key_issued, mattress_issued, bucket_issued, curtain_issued) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("isssiiii", $booking['student_id'], $booking['name'], $booking['hostel_name'], $booking['room_number'], $key_issued, $mattress_issued, $bucket_issued, $curtain_issued);
            }

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>Items assigned successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error assigning items: " . $conn->error . "</div>";
            }
        }
    } elseif (isset($_POST['clear_items'])) {
        // Clear items
        $id = $_POST['id'];
        $update_sql = "UPDATE issued_items SET 
                      key_issued = 0, 
                      mattress_issued = 0, 
                      bucket_issued = 0, 
                      curtain_issued = 0 
                      WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Items cleared successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error clearing items: " . $conn->error . "</div>";
        }
    }
}

// Fetch all bookings
$bookings_sql = "SELECT * FROM bookings ORDER BY booking_date DESC";
$bookings_result = $conn->query($bookings_sql);

// Fetch all issued items
$issued_sql = "SELECT * FROM issued_items ORDER BY issued_date DESC";
$issued_result = $conn->query($issued_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Items | SEKU University</title>
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

        /* Sidebar */
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

        /* Improved Table Styles */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th {
            white-space: nowrap;
            position: sticky;
            top: 0;
            background: white;
        }

        .table td {
            word-break: break-word;
            vertical-align: middle;
        }

        .table th, .table td {
            padding: 0.75rem;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .form-check-input {
            transform: scale(1.5);
            margin: 0 auto;
        }

        /* Card spacing */
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
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
            
            .table td, .table th {
                padding: 0.5rem;
                font-size: 0.875rem;
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
                <a class="nav-link" href="bookings.php">
                    <i class="bi bi-journal-bookmark"></i> View Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="assign-items.php">
                    <i class="bi bi-key"></i> Assign Items
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
        <a href="logout.php" class="btn btn-danger text-center mt-3">Logout</a>
    </nav>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn"><i class="bi bi-list"></i></button>

    <div class="main-content">
        <div class="container">
            <h2 class="text-center mb-4">Hostel Items Assignment</h2>
            
            <?php echo $message; ?>

            <!-- Assign Items Form - Now full width -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Assign Items to Student</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="assign-items.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Booking</label>
                                <select name="booking_id" class="form-select" required>
                                    <option value="">-- Select Booking --</option>
                                    <?php 
                                    // Reset pointer to beginning for the select dropdown
                                    $bookings_result->data_seek(0);
                                    while ($booking = $bookings_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $booking['id']; ?>">
                                            <?php echo htmlspecialchars($booking['name']) . " - " . htmlspecialchars($booking['hostel_name']) . " (Room: " . htmlspecialchars($booking['room_number']) . ")"; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Items to Assign</label>
                                <div class="row">
                                    <div class="col-6 col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="key_issued" id="key_issued">
                                            <label class="form-check-label" for="key_issued">Room Key</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="mattress_issued" id="mattress_issued">
                                            <label class="form-check-label" for="mattress_issued">Mattress</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="bucket_issued" id="bucket_issued">
                                            <label class="form-check-label" for="bucket_issued">Bucket</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="curtain_issued" id="curtain_issued">
                                            <label class="form-check-label" for="curtain_issued">Curtain</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" name="assign_items" class="btn btn-primary w-100">Assign Items</button>
                    </form>
                </div>
            </div>

            <!-- Currently Issued Items - Now below the form -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Currently Issued Items</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Hostel</th>
                                    <th>Room</th>
                                    <th>Key</th>
                                    <th>Mattress</th>
                                    <th>Bucket</th>
                                    <th>Curtain</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Reset pointer to beginning for the table
                                $issued_result->data_seek(0);
                                if ($issued_result->num_rows > 0) { ?>
                                    <?php while ($item = $issued_result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['hostel_name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['room_number']); ?></td>
                                            <td><?php echo $item['key_issued'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'; ?></td>
                                            <td><?php echo $item['mattress_issued'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'; ?></td>
                                            <td><?php echo $item['bucket_issued'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'; ?></td>
                                            <td><?php echo $item['curtain_issued'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'; ?></td>
                                            <td>
                                                <form method="POST" action="assign-items.php" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" name="clear_items" class="btn btn-sm btn-danger">Clear</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No items have been issued yet.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggle-btn').addEventListener('click', function() {
            let sidebar = document.getElementById('sidebar');
            let mainContent = document.querySelector('.main-content');

            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');

            // Adjust the button position when sidebar is hidden
            if (sidebar.classList.contains('hidden')) {
                this.style.left = "10px";
            } else {
                this.style.left = "260px";
            }
        });
    </script>

</body>
</html>