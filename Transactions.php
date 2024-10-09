<?php
// Database configuration
$servername = "localhost";  // Database server (change if necessary)
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "bank";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the form has been submitted and table name is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    // Get table name from the form, sanitize and validate
    $name = htmlspecialchars(trim($_POST['name']));

    // Make sure the table name exists in the database (basic validation)
    if (!empty($name) && preg_match('/^[a-zA-Z0-9_]+$/', $name)) {

        // SQL query to fetch all records from the specified table
        $sql = "SELECT * FROM `$name`";  // Table name is wrapped in backticks
        $result = $conn->query($sql);

        // Check if the query was successful
        if ($result) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Records</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    table, th, td {
                        border: 1px solid #ddd;
                    }
                    th, td {
                        padding: 10px;
                        text-align: left;
                    }
                    th {
                        background-color: #007BFF;
                        color: white;
                    }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>

            <h2>All Records from Table: <?php echo htmlspecialchars($name); ?></h2>

            <?php
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr>";

                // Fetch the table headers dynamically based on the table columns
                while ($field_info = $result->fetch_field()) {
                    echo "<th>" . $field_info->name . "</th>";
                }
                echo "</tr>";

                // Fetch and display each record as a row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $data) {
                        echo "<td>" . htmlspecialchars($data) . "</td>";
                    }
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "No records found.";
            }

            ?>
            <a href="Poratl.html">Back to Poratl</a>

            </body>
            </html>

            <?php
        } else {
            echo "Error executing query: " . $conn->error;
        }
    } else {
        echo "Invalid table name.";
    }
} else {
    echo "No table name provided.";
}

$conn->close();
?>
