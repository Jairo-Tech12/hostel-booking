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
            const hostel = document.getElementById('hostel').value;
            window.location.href = 'hostel.php?hostel=' + encodeURIComponent(hostel);

            // Ensure phone number is in 2547XXXXXXXX format
            if (!phone.startsWith("254")) {
                phone = "254" + phone.slice(-9);
            }

            if (phone.length !== 12) {
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

            console.log("Sending request to mpesa.php with:", phone, amount);

            fetch("mpesa.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text()) // First, get response as text
            .then(text => {
                console.log("Raw response:", text);
                try {
                    let data = JSON.parse(text); // Try parsing it as JSON
                    if (data.error) {
                        alert("Payment Failed: " + data.error);
                    } else {
                        alert("STK Push Sent! Check your phone to complete the payment.");
                    }
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    alert("Invalid response from server. Check console for details.");
                }
            })
            .catch(error => {
                console.error("Fetch API Error:", error);
                alert("Failed to send request. Check console for details.");
            });
        }

        function goBack() {
            window.history.back(); // Goes to the previous page
        }
        function validateAndRedirect(event) {
        const phone = document.getElementById('phone').value;
        const amount = document.getElementById('amount').value;
        
        // Check if phone number and amount are entered
        if (!phone || !amount) {
            alert('Please enter both phone number and amount.');
            return;
        }
        
        // Redirect to hostel.php if validation is successful
        window.location.href = 'hostel.php';
    }
    </script>
</head>

<body>
    <!-- Back Button Moved to Upper Page -->
    <button class="back-button" onclick="goBack()">&#8592; Back</button>

    <div class="payment-container">
    <h2>M-Pesa Payment</h2>
    <form onsubmit="event.preventDefault(); sendPayment();">
        <label>Phone Number (2547xxxxxxxx):</label>
        <input type="text" id="phone" placeholder="Enter phone number" required>

        <label>Amount (KES):</label>
        <input type="number" id="amount" placeholder="Enter amount" required>

        <button type="submit" onclick="validateAndRedirect(event)">Pay Now</button>
    </form>
</div>
</body>
<style>
        /* General Styling */
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
