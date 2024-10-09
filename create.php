<?php
// Database configuration
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "Bank"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example of inserting data (assuming form data is sent via POST method)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    
    // Sanitize name to avoid special characters
    $sanitized_name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);

    // SQL query to create the user's table
    $sql = "CREATE TABLE `$sanitized_name` (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        credit DECIMAL(10, 2) NOT NULL DEFAULT 0,
        debit DECIMAL(10, 2) NOT NULL DEFAULT 0,
        acc_no INT(11) NOT NULL DEFAULT 0,
        balance DECIMAL(10, 2) NOT NULL DEFAULT 0,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        phone_number VARCHAR(15) DEFAULT '$phone',
        CHECK (credit >= 0),
        CHECK (debit >= 0)
    )";

    // Execute the query to create the table
    if ($conn->query($sql) === TRUE) {
        // Automatically insert a first row into the new table
        $initial_credit = 0;
        $initial_debit = 0;
        $initial_balance = 0;
        $initial_acc_no = 0; // You may want to generate a unique account number

        $insert_initial_row = "INSERT INTO `$sanitized_name` (credit, debit, balance, acc_no, phone_number) 
                                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_initial_row);
        $stmt->bind_param("ddiss", $initial_credit, $initial_debit, $initial_balance, $initial_acc_no, $phone);

        if ($stmt->execute()) {
            header("Location: login.html");
            exit();
        } else {
            echo "Error inserting initial row: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error occurred: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
