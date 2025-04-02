<!-- <?php
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'seku';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin credentials
$username = 'jairo'; // Change if needed
$password = password_hash('1234', PASSWORD_BCRYPT); // Secure password hash

// Check if admin already exists
$sql = "SELECT * FROM admin WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Insert new admin
    $insertSql = "INSERT INTO admin (username, password) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ss", $username, $password);
    if ($insertStmt->execute()) {
        echo "✅ Admin user added successfully!";
    } else {
        echo "❌ Error: " . $insertStmt->error;
    }
} else {
    echo "⚠️ Admin user already exists!";
}

$conn->close();
?> -->
