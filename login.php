<?php
if (isset($_POST['phone']) && isset($_POST['password'])) {
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Sanitize inputs
    $email = validate($_POST['phone']);
    $pass = validate($_POST['password']);

    $conn = new mysqli('localhost', 'root', '', 'bank');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to fetch the user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Verify the password
        if ($pass=== $row['password']) { // Ensure you hash passwords in the DB
            header("Location: portal.html");
            exit();
        } else {
            header("Location: login.html?error=incorrect password");
            exit();
        }
    } else {
        header("Location: login.html?error=incorrect username and password");
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>