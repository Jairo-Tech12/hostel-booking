<?php
session_start();

// ✅ Ensure the student is logged in
if (!isset($_SESSION['student_id']) || !isset($_SESSION['reg_number'])) {
    die("User is not logged in. Please log in first.");
}

$student_id = $_SESSION['student_id'];
$reg_number = $_SESSION['reg_number'];

include 'config.php';

// ✅ Fetch student name
$stmt = $conn->prepare("SELECT name FROM students WHERE reg_number = ?");
$stmt->bind_param("s", $reg_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();
$name = $student['name'];

// ✅ Retrieve and validate booking form data (POST)
$hostel_id = isset($_POST['hostel_id']) ? (int)$_POST['hostel_id'] : 0;
$hostel_name = isset($_POST['hostel_name']) ? htmlspecialchars(trim($_POST['hostel_name'])) : '';
$hostel_price = isset($_POST['amount']) ? (float)str_replace(',', '', $_POST['amount']) : 0;
$room_number = isset($_POST['room_number']) ? htmlspecialchars($_POST['room_number']) : '';

// ✅ Validate inputs
if ($hostel_id <= 0 || empty($hostel_name) || $hostel_price <= 0 || empty($room_number)) {
    echo "<div style='color:red; padding:20px; text-align:center; font-family:Arial;'>
            <h2>Missing Information</h2>
            <p>Required parameters are missing or invalid.</p>
            <p><a href='javascript:history.back()'>Go Back</a></p>
          </div>";
    exit();
}

// ✅ Insert booking into bookings table
$insert = $conn->prepare("INSERT INTO bookings (student_id, name, reg_number, hostel_name, room_number) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("issss", $student_id, $name, $reg_number, $hostel_name, $room_number);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f2f5f8;
        }

        .confirmation-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 40px 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-radius: 10px;
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 15px;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }

        strong {
            color: #000;
        }

        .btn-home {
            margin-top: 25px;
            padding: 12px 25px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s ease;
        }

        .btn-home:hover {
            background: #0d47a1;
        }

        @media (max-width: 600px) {
            .confirmation-container {
                padding: 30px 20px;
                margin: 30px 10px;
            }

            p {
                font-size: 15px;
            }

            .btn-home {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <?php if ($insert->execute()): ?>
        <?php
        // Optional: clear session values
        unset($_SESSION['room_number']);
        unset($_SESSION['hostel_id']);
        unset($_SESSION['hostel_name']);
        unset($_SESSION['hostel_price']);
        ?>
        <h2>Booking Confirmed!</h2>
        <p>Thank you, your booking has been successfully recorded.</p>
        <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room_number); ?></p>
        <p><strong>Hostel:</strong> <?php echo htmlspecialchars($hostel_name); ?></p>
        <p><strong>Amount Paid:</strong> Ksh <?php echo number_format($hostel_price, 2); ?></p>

        <a href="home.php" class="btn-home">Return to Home Page</a>
    <?php else: ?>
        <h2 style="color: red;">Booking Failed</h2>
        <p>There was an error processing your booking.</p>
        <p>Error: <?php echo htmlspecialchars($conn->error); ?></p>
        <a href="home.php" class="btn-home">Return to Home Page</a>
    <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
