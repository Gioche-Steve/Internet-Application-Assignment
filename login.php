<?php
// login.php (PHP Backend)
session_start(); // Start session

// Database connection (replace with your credentials)
$servername = "localhost";
$dbusername = "your_db_username";
$dbpassword = "your_db_password";
$dbname = "your_db_name";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prevent SQL injection using prepared statements
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $stored_hash = $result["password_hash"];
        if (password_verify($password, $stored_hash)) {
            // Authentication successful
            $_SESSION["user_id"] = $result["id"]; // Set session variable
            setcookie("user_id", $result["id"], time() + (86400 * 30), "/"); // Set secure cookie (30 days)
            header("Location: welcome.php"); // Redirect to welcome page
            exit;
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }
}
$conn = null; //close connection
?>