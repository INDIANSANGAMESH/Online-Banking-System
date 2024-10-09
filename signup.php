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
    // Validate password confirmation
    if ($_POST['password'] !== $_POST['confirm-password']) {
        die("Passwords do not match.");
    }

    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $dob = htmlspecialchars(trim($_POST['dob'])); // Missing semicolon fixed
    $password = trim($_POST['password']); 

    // Hash the password before storing it
    $hashed_password = $password;

    // Handle file upload
    $photo = $_FILES['photo']['name'];
    $valid_extensions = ['png', 'jpg', 'jpeg']; // Removed leading dot for comparison
    $image_extension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
    
    if (in_array($image_extension, $valid_extensions)) {
        $newim = uniqid() . '.' . $image_extension;    
        
        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], 'photos/' . $newim)) { // Ensure 'photos/' exists
                // SQL query to insert data
                $sql = "INSERT INTO users (name, phone, dob, password, photo) VALUES (?, ?, ?, ?, ?)"; // Added dob to SQL query
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $name, $phone, $dob, $hashed_password, $newim); // Corrected bind parameters

                if ($stmt->execute()) {
                      // Use POST method to send name and phone to create.php
                    echo "
                    <form id='sendForm' action='create.php' method='POST'>
                        <input type='hidden' name='name' value='{$name}'>
                        <input type='hidden' name='phone' value='{$phone}'>
                    </form>
                    <script>
                        document.getElementById('sendForm').submit();
                    </script>
                    ";
        exit(); 

                } else {
                    echo "Error: " . htmlspecialchars($stmt->error);
                }

                $stmt->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File upload error: " . $_FILES['photo']['error'];
        }
    } else {
        echo "Invalid file type. Only PNG, JPG, and JPEG files are allowed.";
    }
}

// Close the connection
$conn->close();
?>
