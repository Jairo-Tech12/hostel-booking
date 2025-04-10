<?php
session_start();

$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'seku';

// Connect to the database
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    echo "Session student_id is not set."; // Debugging message
    exit();
}

$student_id = $_SESSION['student_id'];

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $image_name = time() . "_" . basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ["jpg", "jpeg", "png", "gif"];

    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $query = "UPDATE students SET profile_image = ? WHERE student_id = ?";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error); // Debugging message
            }
            $stmt->bind_param("si", $target_file, $student_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file."; // Error handling for file upload
        }
    } else {
        echo "Only JPG, JPEG, PNG & GIF files are allowed."; // Error handling for invalid file type
    }
}

// Fetch student details along with hostel, room number, and issued items
$query = "SELECT s.student_id, s.name, s.reg_number, s.school, s.department, 
                  s.program, s.year_of_study, s.email, s.profile_image, 
                  b.hostel_name, b.room_number,
                  i.key_issued, i.bucket_issued, i.curtain_issued, i.mattress_issued
          FROM students s
          LEFT JOIN bookings b ON s.student_id = b.student_id
          LEFT JOIN issued_items i ON s.student_id = i.student_id
          WHERE s.student_id = ?";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error); // Debugging message
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($student_id, $name, $reg_number, $school, $department, 
                   $program, $year_of_study, $email, $profile_image, 
                   $hostel_name, $room_number, 
                   $key_issued, $bucket_issued, $curtain_issued, $mattress_issued);
$stmt->fetch();
$stmt->close();

// If no profile image, use a default one
if (empty($profile_image)) {
    $profile_image = "imgs/default-avatar.png";
}

// If hostel details are null, set default values
$hostel_name = $hostel_name ?: "Not Booked";
$room_number = $room_number ?: "Not Assigned";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Student Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1f4037, #99f2c8);
            font-family: 'Poppins', sans-serif;
            color: white;
            text-align: center;
        }

        .profile-container {
            max-width: 500px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(15px);
            box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.2);
        }

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #ffb400;
            object-fit: cover;
            cursor: pointer;
        }

        .profile-header h2 {
            margin-top: 15px;
            font-size: 24px;
            font-weight: 600;
        }

        .profile-details {
            text-align: left;
            margin-top: 20px;
        }
        .profile-details .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 16px;
        }

        .logout-btn {
            display: inline-block;
            width: 100%;
            margin-top: 25px;
            padding: 12px;
            background: #ffb400;
            color: #141e30;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            text-decoration: none;
        }

        #profilePictureInput {
            display: none;
        }

        .choose-file-btn {
            margin-top: 15px;
            padding: 10px 15px;
            background: #ffb400;
            color: #141e30;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-container">
        <button class="back-btn" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i>
        </button>
        
        <div class="profile-header">
            <form method="post" enctype="multipart/form-data">
                <label for="profilePictureInput">
                    <img id="profileImage" src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture">
                </label>
                <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*" onchange="this.form.submit();">
            </form>

            <h2><?php echo htmlspecialchars($name); ?></h2>
            <p><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($school); ?></p>
        </div>

        <div class="profile-details">
            <div class="info-item"><span><i class="fas fa-id-badge"></i> Student ID:</span><span><?php echo htmlspecialchars($student_id); ?></span></div>
            <div class="info-item"><span><i class="fas fa-book"></i> Reg Number:</span><span><?php echo htmlspecialchars($reg_number); ?></span></div>
            <div class="info-item"><span><i class="fas fa-building"></i> Department:</span><span><?php echo htmlspecialchars($department); ?></span></div>
            <div class="info-item"><span><i class="fas fa-chalkboard-teacher"></i> Program:</span><span><?php echo htmlspecialchars($program); ?></span></div>
            <div class="info-item"><span><i class="fas fa-calendar"></i> Year:</span><span><?php echo htmlspecialchars($year_of_study); ?></span></div>
            <div class="info-item"><span><i class="fas fa-envelope"></i> Email:</span><span><?php echo htmlspecialchars($email); ?></span></div>
            <div class="info-item"><span><i class="fas fa-hotel"></i> Hostel:</span><span><?php echo htmlspecialchars($hostel_name); ?></span></div>
            <div class="info-item"><span><i class="fas fa-bed"></i> Room Number:</span><span><?php echo htmlspecialchars($room_number); ?></span></div>
            <div class="info-item"><span><i class="fas fa-key"></i> Key Issued:</span><span><?php echo $key_issued ? "Yes" : "No"; ?></span></div>
            <div class="info-item"><span><i class="fas fa-bucket"></i> Bucket Issued:</span><span><?php echo $bucket_issued ? "Yes" : "No"; ?></span></div>
            <div class="info-item"><span><i class="fas fa-curtains"></i> Curtain Issued:</span><span><?php echo $curtain_issued ? "Yes" : "No"; ?></span></div>
            <div class="info-item"><span><i class="fas fa-bed"></i> Mattress Issued:</span><span><?php echo $mattress_issued ? "Yes" : "No"; ?></span></div>
        </div>

        <a href="?logout=true" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

</body>
</html>
