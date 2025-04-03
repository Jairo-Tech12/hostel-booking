<?php
session_start();

// Example array of hostels with available rooms (this could be fetched from a database)
$hostels = [
    'Green Hostel' => '101',
    'Blue Hostel' => '202',
    'Red Hostel' => '303',
    'Yellow Hostel' => '404'
];

// Fetch the hostel chosen by the user from the session
$hostel = isset($_SESSION['hostel']) ? $_SESSION['hostel'] : 'No hostel selected';

// Check if the chosen hostel exists in our predefined list
if (isset($hostels[$hostel])) {
    $room_number = $hostels[$hostel];
} else {
    $hostel = 'No hostel selected';
    $room_number = 'No room available';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Booking Confirmation</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS if needed -->
</head>
<body>
    <div class="hostel-details-container">
        <h2>Hostel Booking Details</h2>

        <!-- Displaying the chosen hostel and room number -->
        <p><strong>Hostel:</strong> <?php echo htmlspecialchars($hostel); ?></p>
        <p><strong>Room Number:</strong> <?php echo htmlspecialchars($room_number); ?></p>

        <!-- Button to confirm booking -->
        <form action="confirm_booking.php" method="POST">
            <input type="hidden" name="hostel" value="<?php echo htmlspecialchars($hostel); ?>">
            <input type="hidden" name="room" value="<?php echo htmlspecialchars($room_number); ?>">
            <button type="submit">Confirm Booking</button>
        </form>
    </div>
</body>
</html>
