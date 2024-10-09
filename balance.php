<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bank";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize balance variable
$balance = "No balance records found.";

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from the form
    $user_id = htmlspecialchars(trim($_POST['user_id']));
    
    // Prepare SQL statement to fetch the balance for the user by user_id
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);  // Assuming `id` is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // If a matching record is found, set the balance
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $balance = $row['balance'];
    }
    
    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Display</title>
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: radial-gradient(circle, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Arial', sans-serif;
        }
        .balance-container {
            text-align: center;
            padding: 50px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .balance-title {
            font-size: 2rem;
            color: #4a90e2;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .balance-amount {
            font-size: 4rem;
            font-weight: bold;
            color: #0c6fcd;
        }
    </style>
</head>
<body>

<div class="balance-container">
    <div class="balance-title">Your Balance</div>
    <div class="balance-amount">
        $<?php 
            // Check if the balance is numeric and format it, else display message
            if (is_numeric($balance)) {
                echo htmlspecialchars(number_format($balance, 2)); 
            } else {
                echo htmlspecialchars($balance); 
            }
        ?>
    </div>
</div>

</body>
</html>
