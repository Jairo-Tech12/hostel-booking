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

// Handling form submission
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $national_id = trim($_POST['national_id']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);
    $hostel = trim($_POST['hostel']);

    // Check if any field is empty
    if (empty($username) || empty($name) || empty($national_id) || empty($email) || empty($password) || empty($phone_number) || empty($hostel)) {
        $error = "All fields are required!";
    } else {
        // Check if username, national ID, or email already exists
        $checkSql = "SELECT * FROM staff WHERE username = ? OR national_id = ? OR email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("sss", $username, $national_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username, National ID, or Email already exists!";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert staff details into the database
            $sql = "INSERT INTO staff (username, name, national_id, email, password, phone_number, hostel) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $username, $name, $national_id, $email, $hashed_password, $phone_number, $hostel);

            if ($stmt->execute()) {
                // ✅ Redirect to staff.php after successful registration
                echo "<script>
                        alert('Staff registered successfully!');
                        window.location.href = 'staff.php';
                        </script>";
                exit();
            } else {
                $error = "Something went wrong! Please try again.";
            }
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
    <title>Staff | Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    
</head>
<body>

    <div class="container">
        <div class="card">
            <h2>Staff Registration</h2>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST" action="register-staff.php">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">National ID</label>
                    <input type="text" name="national_id" class="form-control" required>
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
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Assign Hostel</label>
                    <select name="hostel" class="form-control" required>
                        <option value="" disabled selected>Select Hostel</option>
                        <option value="Victoria">Victoria</option>
                        <option value="Magadi">Magadi</option>
                        <option value="Turkana">Turkana</option>
                        <option value="Yatta">Yatta</option>
                        <option value="Elementaita">Elementaita</option>
                        <option value="Turkwel">Turkwel</option>
                        <option value="Athi">Athi</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-register">Register Staff</button>
                <a href="staff.php" class="btn btn-back mt-2">Back to Staff List</a>
            </form>
        </div>
    </div>

</body>
<style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
        }

        .card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            background: white;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
            color: #2c3e50;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #1abc9c;
            box-shadow: 0 0 5px rgba(26, 188, 156, 0.5);
        }

        .btn-register {
            background: #2c3e50;
            color: white;
            border-radius: 5px;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        .btn-register:hover {
            background: #1abc9c;
        }

        .btn-back {
            background: #dcdcdc;
            color: #2c3e50;
            border-radius: 5px;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        .btn-back:hover {
            background: #b0b0b0;
        }

        .alert {
            text-align: center;
            font-weight: bold;
        }
    </style>
</html>
