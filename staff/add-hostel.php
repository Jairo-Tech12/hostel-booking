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

// Handle form submission
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $type = trim($_POST['type']);
    $total_rooms = trim($_POST['total_rooms']);
    $availability = trim($_POST['availability']);

    // Validate form fields
    if (empty($name) || empty($price) || empty($type) || empty($total_rooms) || empty($availability)) {
        $error = "All fields are required!";
    } else {
        // Insert hostel details into database
        $sql = "INSERT INTO hostels (name, price, type, total_rooms, availability) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsss", $name, $price, $type, $total_rooms, $availability);

        if ($stmt->execute()) {
            echo "<script>alert('Hostel added successfully!'); window.location.href='hostel.php';</script>";
            exit();
        } else {
            $error = "Something went wrong! Please try again.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hostel | SEKU University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(to right, #34495e, #2c3e50);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        /* Full-Width Form Container */
        .form-container {
            width: 80%;
            max-width: 1000px;
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease-in-out;
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0px 12px 22px rgba(0, 0, 0, 0.4);
        }

        h2 {
            text-align: center;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 14px;
            text-transform: uppercase;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 14px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #1abc9c;
            box-shadow: 0 0 8px rgba(26, 188, 156, 0.5);
        }

        .btn-submit {
            background: linear-gradient(45deg, #1abc9c, #16a085);
            color: white;
            border-radius: 8px;
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease-in-out;
            border: none;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-submit:hover {
            background: linear-gradient(45deg, #16a085, #138d75);
            transform: scale(1.05);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        }

        .alert {
            text-align: center;
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-btn {
            display: block;
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
            transition: 0.3s;
            font-weight: bold;
        }

        .back-btn:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .form-container {
                width: 90%;
                padding: 40px;
            }
        }

        @media (max-width: 768px) {
            .form-container {
                width: 95%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Add New Hostel</h2>

        <?php if (!empty($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="add-hostel.php">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Hostel Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter hostel name" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Price (Ksh)</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter price per room" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control" required>
                        <option value="" disabled selected>Select Type</option>
                        <option value="Double">Double</option>
                        <option value="Shared">Shared</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Total Rooms</label>
                    <input type="number" name="total_rooms" class="form-control" placeholder="Enter total rooms" required>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Availability</label>
                    <select name="availability" class="form-control" required>
                        <option value="" disabled selected>Select Availability</option>
                        <option value="Available">Available</option>
                        <option value="Full">Full</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-submit">Add Hostel</button>
            <a href="hostel.php" class="back-btn">← Back to Hostel Management</a>
        </form>
    </div>

</body>
</html>
