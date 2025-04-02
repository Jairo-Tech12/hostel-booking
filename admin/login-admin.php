<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
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

// Handle login
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin'] = $admin['username'];
                header("Location: admin.php");
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Both fields are required!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Login</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    
</head>
<body>

    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
    </div>

</body>
<style>
        /* Body Styling */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #1abc9c, #2c3e50);
            backdrop-filter: blur(5px);
            font-family: 'Poppins', sans-serif;
        }

        /* Login Box */
        .login-box {
            background: rgba(255, 255, 255, 0.15);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            color: white;
        }

        .login-box h2 {
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Input Fields */
        .form-control {
            border-radius: 8px;
            border: none;
            padding: 14px;
            font-size: 16px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid #1abc9c;
            box-shadow: none;
            outline: none;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            background: #1abc9c;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #16a085;
        }

        /* Responsive Adjustments */
        @media (max-width: 500px) {
            .login-box {
                width: 90%;
                padding: 30px;
            }

            .form-control {
                font-size: 14px;
                padding: 12px;
            }

            .btn-login {
                font-size: 14px;
                padding: 10px;
            }

            .login-box h2 {
                font-size: 22px;
            }
        }
    </style>
</html>
