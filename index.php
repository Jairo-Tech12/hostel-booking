<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SEKU Hostel Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* General Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background-color: #f5f5f5;
        }

        /* Left Section (Background Image) */
        .left-section {
            flex: 1;
            background-image: url('imgs/seku.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: none; /* Hidden on small screens */
        }

        /* Right Section (Login Form) */
        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: white;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* University Logo */
        .logo img {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }

        h2 {
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #388E3C;
        }

        .error {
            color: #d9534f;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Login Links */
        .login-links {
            margin-top: 20px;
            text-align: center;
        }

        .login-links a {
            display: inline-block;
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
            margin: 5px;
            transition: 0.3s;
        }

        .login-links a:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        /* Footer */
        footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }

        /* Responsive Design */
        @media screen and (min-width: 768px) {
            .left-section {
                display: block;
            }
            body {
                flex-direction: row;
            }
        }
    </style>
</head>
<body>

    <!-- Left Section (Background Image) -->
    <div class="left-section"></div> 

    <!-- Right Section (Login Form) -->
    <div class="right-section">
        <div class="container">
            
            <!-- University Logo -->
            <div class="logo">
                <img src="imgs/logo.png" alt="SEKU Logo">
            </div>

            <h2><i class="fas fa-university"></i> SOUTH EASTERN KENYA UNIVERSITY</h2>

            <?php if (isset($error) && $error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>

            <div class="login-links">
                <p>Login as:</p>
                <a href="login.php"><i class="fas fa-user"></i> Student</a>
                <a href="staff/login.php"><i class="fas fa-user-tie"></i> Hostel Manager</a>
                <a href="admin/login-admin.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>

        <!-- Footer inside right-section but outside the login container -->
        <footer>
            <p>&copy; 2025 South Eastern Kenya University. All Rights Reserved. | ISO 9001 : 2015 Certified.</p>
        </footer>
    </div>

</body>
</html>
