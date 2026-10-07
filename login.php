<?php

session_start();

include "db.php";

// Get login details from the form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Check that fields are filled
if (empty($email) || empty($password)) {
    die("Please enter email and password.");
}

// Find the user
$stmt = $conn->prepare(
    "SELECT id, name, email, mobileno, password
     FROM studentdetails
     WHERE email = ?"
);

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        header("Location: documents.html");
        exit;

    } else {

        echo "Invalid password!";
    }

} else {

    echo "Account not found!";
}

$stmt->close();
$conn->close();

?>
