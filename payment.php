<?php
session_start();

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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_number = htmlspecialchars(trim($_POST['id']));  // Use this as both account_number and user_id
    $transaction_type = htmlspecialchars(trim($_POST['transaction-type']));
    $amount = htmlspecialchars(trim($_POST['amount']));

    // Validate inputs
    if (empty($account_number) || empty($transaction_type) || empty($amount) || !is_numeric($amount) || $amount <= 0) {
        die("Please fill in all fields correctly.");
    }

    // Fetch user's name from the users table using account number (which is also user_id)
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $account_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $user_name = $row['name'];

        // Fetch current balance from the user's table
        $stmt = $conn->prepare("SELECT balance FROM $user_name ORDER BY timestamp DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $current_balance = $row['balance'];

            // Update the balance based on the transaction type
            if ($transaction_type === "credit") {
                $new_balance = $current_balance + $amount;

                // Log the transaction
                $stmt = $conn->prepare("INSERT INTO $user_name (credit, balance, acc_no) VALUES (?, ?, ?)");
                $stmt->bind_param("ddi", $amount, $new_balance, $account_number);
                $stmt->execute();

            } elseif ($transaction_type === "debit") {
                if ($current_balance < $amount) {
                    echo "Insufficient balance for this transaction.";
                    exit; // End execution if balance is insufficient
                }
                $new_balance = $current_balance - $amount;

                // Log the transaction
                $stmt = $conn->prepare("INSERT INTO $user_name (debit, balance, acc_no) VALUES (?, ?, ?)");
                $stmt->bind_param("ddi", $amount, $new_balance, $account_number);
                $stmt->execute();
            } else {
                echo "Invalid transaction type.";
                exit; // End execution for invalid transaction type
            }

            // Update balance in the users table
            $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $new_balance, $account_number);
            $stmt->execute();

            echo ucfirst($transaction_type) . " transaction successful! New balance: $" . htmlspecialchars($new_balance);
        } else {
            echo "No balance found for the user.";
        }
    } else {
        echo "User not found.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
