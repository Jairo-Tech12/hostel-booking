<?php
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
session_start();
if (!isset($_SESSION['student_id'])) {
    // Redirect to login page if student is not logged in
    header("Location: login.php");
    exit;
}

// Fetch the student details from the session (or database if needed)
$student_id = $_SESSION['student_id'];
// Fetch student details from the database (example query, adjust as per your structure)
$studentQuery = "SELECT * FROM students WHERE id = $student_id";
$studentResult = $conn->query($studentQuery);
$studentData = $studentResult->fetch_assoc();

// Fetch hostel details from the URL parameters
$hostel_id = isset($_GET['id']) ? $_GET['id'] : null;
$hostel_name = isset($_GET['name']) ? $_GET['name'] : null;
$hostel_price = isset($_GET['price']) ? $_GET['price'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment</title>
    <script>
    function sendPayment() {
        let phone = document.getElementById("phone").value.trim();
        let amount = document.getElementById("amount").value.trim();

        // Ensure phone number is in the correct format
        if (!phone.startsWith("254")) {
            phone = "254" + phone.slice(-9);
        }

        if (phone.length !== 12 || !phone.match(/^2547\d{8}$/)) {
            alert("Invalid phone number! Please enter a valid Safaricom number in the format 2547XXXXXXXX");
            return;
        }

        if (amount === "" || parseInt(amount) < 1) {
            alert("Please enter a valid amount!");
            return;
        }

        let formData = new FormData();
        formData.append("phone", phone);
        formData.append("amount", amount);
        formData.append("student_id", "<?php echo $student_id; ?>");
        formData.append("full_name", "<?php echo $studentData['full_name']; ?>");
        formData.append("email", "<?php echo $studentData['email']; ?>");
        formData.append("hostel_id", "<?php echo $hostel_id; ?>");
        formData.append("hostel_name", "<?php echo $hostel_name; ?>");
        formData.append("hostel_price", "<?php echo $hostel_price; ?>");

        console.log("Sending STK Push request to mpesa.php with:", phone, amount);

        fetch("mpesa.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) // Get response as JSON
        .then(data => {
            console.log("STK Push Response:", data);

            if (data.ResponseCode === "0") {
                alert("STK Push Sent! Check your phone to complete the payment.");
                saveBooking(phone, amount);
            } else {
                alert("Payment Failed: " + (data.errorMessage || "Unknown Error"));
                saveBooking(phone, amount); // Save the booking even if payment fails
            }
        })
        .catch(error => {
            console.error("Fetch API Error:", error);
            alert("Failed to send request. Check console for details.");
            saveBooking(phone, amount); // Save booking even on error
        });
    }

    function saveBooking(phone, amount) {
        let bookingData = new FormData();
        bookingData.append("phone", phone);
        bookingData.append("amount", amount);
        bookingData.append("hostel_id", "<?php echo $hostel_id; ?>");
        bookingData.append("hostel_name", "<?php echo $hostel_name; ?>");
        bookingData.append("hostel_price", "<?php echo $hostel_price; ?>");
        bookingData.append("student_id", "<?php echo $student_id; ?>");
        bookingData.append("full_name", "<?php echo $studentData['full_name']; ?>");
        bookingData.append("email", "<?php echo $studentData['email']; ?>");

        console.log("Saving booking with:", phone, amount);

        fetch("saveBooking.php", {
            method: "POST",
            body: bookingData
        })
        .then(response => response.text())
        .then(text => {
            console.log("Booking Response:", text);
            if (text === "success") {
                alert("Booking Successful!");
            } else {
                alert("Error saving booking: " + text);
            }
        })
        .catch(error => {
            console.error("Booking Error:", error);
            alert("Error while saving booking. Check console for details.");
        });
    }

    function goBack() {
        window.history.back(); // Goes to the previous page
    }
</script>



</head>
<body>
    <button class="back-button" onclick="goBack()">&#8592; Back</button>

    <div class="payment-container">
        <h2>M-Pesa Payment</h2>
        <form onsubmit="event.preventDefault(); sendPayment();">
            <label>Phone Number (2547xxxxxxxx):</label>
            <input type="text" id="phone" placeholder="Enter phone number" required>
            
            <label>Amount (KES):</label>
            <input type="number" id="amount" placeholder="Enter amount" required>
            <p>Hostel: <?php echo htmlspecialchars($hostel_name); ?></p>
            
            <button type="submit">Pay Now</button>
        </form>
        
    </div>
</body>
<style>
     body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding-top: 20px;
        }

        /* Back Button (Now at the top of the page) */
        .back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #00796b; /* Updated background color */
    border: none;
    width: inherit;
    cursor: pointer;
    font-size: 22px;
    cursor: pointer;
    color: white; /* Change text color to white for contrast */
    padding: 10px 15px; /* Add padding for a better hit area */
    border-radius: 5px; /* Rounded corners */
    transition: background 0.3s, transform 0.3s; /* Added transition for hover effect */
}

.back-button:hover {
    background:rgb(1, 38, 245); /* Darker green on hover */
    transform: translateY(-2px); /* Slight lift effect */
}

        /* Payment Form Container */
        .payment-container {
            background: white;
            padding: 20px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* Form Heading */
        .payment-container h2 {
            margin-bottom: 15px;
            color: #009688;
        }

        /* Input Fields */
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Payment Button */
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: #009688;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        /* Hover Effect */
        button:hover {
            background: #00796b;
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .payment-container {
                width: 90%;
            }
        }
</style>

</html>
