<?php

include "db.php";

// Make sure the form was submitted
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// Get form values
$name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$mobileno = isset($_POST["mobileno"]) ? trim($_POST["mobileno"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

// Validate
if ($name === "") {
    die("Name is required.");
}

if ($email === "") {
    die("Email is required.");
}

if ($mobileno === "") {
    die("Mobile number is required.");
}

if ($password === "") {
    die("Password is required.");
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

// Hash password
$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// Check whether email already exists
$check = $conn->prepare(
    "SELECT id FROM studentdetails WHERE email = ?"
);

if (!$check) {
    die("Database error: " . $conn->error);
}

$check->bind_param("s", $email);

$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {

    $check->close();
    $conn->close();

    die("Email already registered!");
}

$check->close();


// Insert student
$stmt = $conn->prepare(
    "INSERT INTO studentdetails
    (name, email, mobileno, password)
    VALUES (?, ?, ?, ?)"
);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ssss",
    $name,
    $email,
    $mobileno,
    $hashed_password
);


if ($stmt->execute()) {

    echo '
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Registration Successful</title>

        <style>

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background: #f0f2f5;
                font-family: Arial, sans-serif;
            }

            .success-box {
                width: 90%;
                max-width: 450px;
                padding: 40px 30px;
                background: white;
                border-radius: 20px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            }

            .success-icon {
                width: 70px;
                height: 70px;
                margin: 0 auto 20px;
                border-radius: 50%;
                background: #28a745;
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 40px;
            }

            .success-box h2 {
                color: #28a745;
                margin-bottom: 15px;
            }

            .success-box p {
                color: #555;
                font-size: 16px;
                margin-bottom: 25px;
                line-height: 1.6;
            }

            .login-btn {
                display: inline-block;
                padding: 12px 30px;
                background: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-size: 16px;
                transition: 0.3s;
            }

            .login-btn:hover {
                background: #0056b3;
            }

        </style>
    </head>

    <body>

        <div class="success-box">

            <div class="success-icon">
                ✓
            </div>

            <h2>Registration Successful!</h2>

            <p>
                Welcome to <strong>NMINDS ACADEMY</strong>,<br><br>
                <strong>' . htmlspecialchars(strtoupper($name)) . '</strong>
            </p>

            <a href="index.html" class="login-btn">
                Login Here
            </a>

        </div>

    </body>

    </html>
    ';

} else {

    echo "Registration failed: " . $stmt->error;
}


$stmt->close();
$conn->close();

?>
