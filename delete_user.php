<?php
// Database configuration
$servername = "localhost";  // Database server (change if necessary)
$username = "root";          // Database username
$password = "";              // Database password
$dbname = "bank";   // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from the form
    $user_id = intval($_POST['user_id']); // Ensure the ID is an integer

    // SQL query to delete the user
    $sql = "DELETE FROM users WHERE id = $user_id";  // Replace 'users' with your actual table name

    // Execute the query and check for success
    if ($conn->query($sql) === TRUE) {
        header("Manager.php");
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
