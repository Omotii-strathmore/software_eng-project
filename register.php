<?php
include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = "Customer";

    // Fullname validation
    if (!preg_match("/^[A-Za-z ]+$/", $fullname)) {
        die("Invalid full name: Only letters and spaces allowed.");
    }

    // Email validation (fixed)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Phone validation
    if (!preg_match("/^\d{10}$/", $phone)) {
        die("Invalid phone number. Must be 10 digits.");
    }

    // Password length
    if (strlen($password) !== 8) {
        die("Password must be exactly 8 characters.");
    }

    if (empty($username)) {
        die("Username cannot be empty.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email exists
    $checkQuery = "SELECT * FROM users WHERE username=? OR email=?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Username or Email already exists.");
    }

    // Insert user
    $insertQuery = "INSERT INTO users (fullname, email, phone, username, password, role)
                    VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssss", $fullname, $email, $phone, $username, $hashedPassword, $role);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful! Redirecting to login...');
                window.location.href = 'login.html';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
