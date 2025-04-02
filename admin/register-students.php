<?php
session_start();

// Database connection
$host = "localhost";
$dbUsername = "root"; // Change if needed
$dbPassword = ""; // Change if needed
$dbName = "seku";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize success & error messages
$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $reg_number = trim($_POST['reg_number']);
    $school = trim($_POST['school']);
    $department = trim($_POST['department']);
    $program = trim($_POST['program']);
    $year_of_study = trim($_POST['year_of_study']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate required fields
    if (empty($student_id) || empty($name) || empty($reg_number) || empty($school) || empty($department) || empty($program) || empty($year_of_study) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 3) {
        $error = "Password must be at least 6 characters!";
    } else {
        // Check if student ID, registration number, or email already exists
        $checkSql = "SELECT * FROM students WHERE student_id = ? OR reg_number = ? OR email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("sss", $student_id, $reg_number, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Student ID, Registration Number, or Email already exists!";
        } else {
            // Hash the password before storing
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert student details into database
            $sql = "INSERT INTO students (student_id, name, reg_number, school, department, program, year_of_study, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssiss", $student_id, $name, $reg_number, $school, $department, $program, $year_of_study, $email, $hashedPassword);

            if ($stmt->execute()) {
                $success = "Student registered successfully!";
            } else {
                $error = "Something went wrong! Please try again.";
            }
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | Registration</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<!-- Registration Form -->
    <div class="registration-container">
        <h2>Student Registration</h2>

        <?php if (!empty($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Student ID</label>
                <input type="text" name="student_id" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Registration Number</label>
                <input type="text" name="reg_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">School</label>
                <input type="text" name="school" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Program</label>
                <input type="text" name="program" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Year of Study</label>
                <select name="year_of_study" class="form-control" required>
                    <option value="" disabled selected>Select Year</option>
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>
                    <option value="3">Year 3</option>
                    <option value="4">Year 4</option>
                </select>
            </div>

            <button type="submit" class="btn btn-custom btn-register">Register Student</button>
            <a href="students.php" class="btn btn-custom btn-back">Back</a>
        </form>
    </div>

</body>
<style>
        /* General Styling */
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        /* Navigation Bar */
        .navbar {
            width: 100%;
            background: #00796b;
        }

        .navbar-brand {
            font-weight: bold;
            color: white;
        }

        .nav-link {
            color: white;
        }

        .nav-link:hover {
            color: #c8e6c9;
        }

        /* Registration Form Container */
        .registration-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 550px;
            text-align: center;
            margin-top: 80px; /* Prevents overlap with navbar */
        }

        /* Form Styling */
        .form-control {
            border-radius: 6px;
            padding: 14px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #00796b;
            box-shadow: 0 0 8px rgba(0, 121, 107, 0.3);
        }

        .btn-custom {
            border-radius: 6px;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            transition: all 0.3s ease-in-out;
            margin-top: 15px;
            border: none;
        }

        .btn-register {
            background: #00796b;
            color: white;
        }

        .btn-register:hover {
            background: #005f56;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .registration-container {
                width: 95%;
                padding: 30px;
            }

            .form-control {
                font-size: 14px;
                padding: 12px;
            }

            .btn-custom {
                font-size: 14px;
                padding: 12px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</html>
