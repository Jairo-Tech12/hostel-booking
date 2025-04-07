<?php
session_start();

// Secure parameter retrieval with validation
$hostel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$hostel_name = isset($_GET['name']) ? htmlspecialchars(trim($_GET['name'])) : '';
$hostel_price = isset($_GET['price']) ? (float)str_replace(',', '', $_GET['price']) : 0;

// Validate parameters
if ($hostel_id <= 0 || empty($hostel_name) || $hostel_price <= 0) {
    echo "<div style='color:red; padding:20px; text-align:center; font-family:Arial;'>
            <h2>Missing Information</h2>
            <p>Required parameters are missing or invalid.</p>
            <p>Example: confirm.php?id=10&name=victoria&price=3900.00</p>
            <p><a href='javascript:history.back()'>Go Back</a></p>
          </div>";
    exit();
}

// Store in session
$_SESSION['hostel_id'] = $hostel_id;
$_SESSION['hostel_name'] = $hostel_name;
$_SESSION['hostel_price'] = $hostel_price;

// Function to assign next available room number (ascending)
function assignRoomNumber() {
    $file = 'last_room.txt';

    // If the file doesn't exist, start from 1
    if (!file_exists($file)) {
        file_put_contents($file, "1");
    }

    // Get last room number
    $last = (int)file_get_contents($file);

    // Set limit if needed (e.g., 500)
    if ($last > 500) {
        return 'FULL'; // or handle however you'd like
    }

    // Format room number (e.g., 001, 045)
    $room_number = sprintf("%03d", $last);

    // Increment and save
    file_put_contents($file, $last + 1);

    return $room_number;
}

// Assign room
$assigned_room = assignRoomNumber();

// If hostel is full
if ($assigned_room === 'FULL') {
    echo "<div style='color:red; padding:20px; text-align:center; font-family:Arial;'>
            <h2>No Rooms Available</h2>
            <p>Sorry, all rooms have been assigned.</p>
            </div>";
    exit();
}

$_SESSION['room_number'] = $assigned_room;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Hostel Booking</title>
</head>
<body>
    <div class="container">
        <h1>Confirm Your Hostel Booking</h1>
        
        <div class="details">
            <h2>Booking Details</h2>
            
            <div class="detail-row">
                <span class="detail-label">Hostel ID:</span>
                <span class="detail-value"><?php echo htmlspecialchars($hostel_id); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Hostel Name:</span>
                <span class="detail-value"><?php echo htmlspecialchars($hostel_name); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Amount:</span>
                <span class="detail-value">ksh<?php echo number_format((float)$hostel_price, 2); ?></span>
            </div>
        </div>
        
        <div class="room-assignment">
            <h2>Your Room Assignment</h2>
            <p>We have automatically assigned you the following room number:</p>
            <div class="room-number">
                <?php echo $_SESSION['room_number']; ?>
            </div>
            <p class="note">Note: This room assignment is subject to availability and may change upon arrival.</p>
        </div>
        
        <div class="buttons">
            <form action="process_booking.php" method="post">
                <input type="hidden" name="hostel_id" value="<?php echo htmlspecialchars($hostel_id); ?>">
                <input type="hidden" name="hostel_name" value="<?php echo htmlspecialchars($hostel_name); ?>">
                <input type="hidden" name="room_number" value="<?php echo $_SESSION['room_number']; ?>">
                <input type="hidden" name="amount" value="<?php echo htmlspecialchars($hostel_price); ?>">
                <button type="submit" class="btn btn-accept">Accept & Continue</button>
            </form>
            
            <form action="book.php" method="post">
                <input type="hidden" name="hostel_id" value="<?php echo htmlspecialchars($hostel_id); ?>">
                <button type="submit" class="btn btn-decline">Decline</button>
            </form>
        </div>
    </div>
</body>
<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .details h2 {
            color: #3498db;
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e0e0e0;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .room-number {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
            text-align: center;
            padding: 20px;
            background: #3498db;
            border-radius: 5px;
            margin: 25px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-accept {
            background-color: #2ecc71;
            color: white;
        }
        .btn-accept:hover {
            background-color: #27ae60;
        }
        .btn-decline {
            background-color: #e74c3c;
            color: white;
        }
        .btn-decline:hover {
            background-color: #c0392b;
        }
        .note {
            font-size: 13px;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</html>