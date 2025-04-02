<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json"); // Ensure JSON response

date_default_timezone_set('Africa/Nairobi');

class MpesaStkPush {
    private $consumerKey;
    private $consumerSecret;
    private $shortcode;
    private $passkey;
    private $baseUrl;

    public function __construct() {
        // Set the correct credentials
        $this->consumerKey = "2au7GGaNKHuDgDx8XYPqGC1idIiDscyE2LV9C949coua8snH"; 
        $this->consumerSecret = "NELfFEQJZGGMnKOmc9Og0GD8gFAMJ5fwyXBFVzeeS6zjijtc6NThhLRKpIFGG0ZW"; 
        $this->shortcode = "174379"; // Use 174379 for Sandbox, or your actual Paybill for Production
        $this->passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919"; // Get this from Safaricom Developer Portal
        $this->baseUrl = "https://sandbox.safaricom.co.ke"; // Change to "https://api.safaricom.co.ke" for Production
    }

    // Function to get access token
    private function getAccessToken() {
        $credentials = base64_encode($this->consumerKey . ":" . $this->consumerSecret);
        $url = $this->baseUrl . "/oauth/v1/generate?grant_type=client_credentials";

        $headers = ["Authorization: Basic $credentials"];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($httpCode !== 200 || $error) {
            error_log("Failed to get access token: " . ($error ?: $response));
            echo json_encode(["error" => "Failed to get access token", "details" => json_decode($response, true)]);
            exit;
        }

        $result = json_decode($response, true);
        return $result['access_token'] ?? null;
    }

    // Function to initiate STK Push
    public function stkPushRequest($amount, $phone) {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ["error" => "Failed to get access token"];
        }

        $timestamp = date("YmdHis");
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        // Ensure correct phone number format (2547XXXXXXXX)
        if (substr($phone, 0, 3) !== "254") {
            $phone = "254" . substr($phone, -9);
        }

        $url = $this->baseUrl . "/mpesa/stkpush/v1/processrequest";

        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ];

        $data = [
            "BusinessShortCode" => $this->shortcode,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => (int) $amount,
            "PartyA" => $phone,
            "PartyB" => $this->shortcode,
            "PhoneNumber" => $phone,
            "CallBackURL" => "https://example.com/dummy_callback", // Dummy callback for offline testing
            "AccountReference" => "TestPayment",
            "TransactionDesc" => "Test Payment for Service"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        // Handle errors
        if ($httpCode !== 200 || $error) {
            $errorData = json_decode($response, true);
            echo json_encode(["error" => "STK Push request failed", "details" => $errorData]);
            exit;
        }

        return json_decode($response, true);
    }
}

// Handle incoming request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? null;
    $phone = $_POST['phone'] ?? null;

    if (!empty($amount) && !empty($phone)) {
        $mpesa = new MpesaStkPush();
        $response = $mpesa->stkPushRequest($amount, $phone);
        echo json_encode($response);
    } else {
        echo json_encode(["error" => "Amount and phone number are required"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method. Use POST"]);
}
?>
