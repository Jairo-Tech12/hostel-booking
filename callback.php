<?php
// Receive and log response
$data = file_get_contents('php://input');
$logFile = "mpesa_callback.log";
file_put_contents($logFile, $data . PHP_EOL, FILE_APPEND);

// Convert JSON to array
$response = json_decode($data, true);

if (isset($response['Body']['stkCallback'])) {
    $callback = $response['Body']['stkCallback'];
    
    if ($callback['ResultCode'] == 0) {
        // Successful transaction
        $amount = $callback['CallbackMetadata']['Item'][0]['Value'];
        $mpesaCode = $callback['CallbackMetadata']['Item'][1]['Value'];
        $phone = $callback['CallbackMetadata']['Item'][3]['Value'];

        // Save to database (Example: MySQL)
        $conn = new mysqli("localhost", "root", "", "booking_db");
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO payments (phone, amount, mpesa_code) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $phone, $amount, $mpesaCode);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        echo json_encode(["success" => "Payment received"]);
    } else {
        echo json_encode(["error" => "Payment failed"]);
    }
}
?>
