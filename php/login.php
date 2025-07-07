<?php
ob_start(); // Start output buffering
session_start();
include 'php_backup.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim input and convert email to lower-case for case-insensitive matching
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $role = $_POST['role']; // Capture the role from the form

    // Query the users table to validate email and password
    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE LOWER(email) = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables for a successful login
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role']; // This will always be 'client'

            // Check if the user is also a trainer with an approved status
            $trainerStmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE LOWER(email) = ? AND status = 'approved'");
            if (!$trainerStmt) {
                die("Error preparing statement: " . $conn->error);
            }
            $trainerStmt->bind_param("s", $email);
            $trainerStmt->execute();
            $trainerResult = $trainerStmt->get_result();

            if ($trainerResult && $trainerResult->num_rows === 1 && $role === 'coach') {
                // User is both a client and an approved trainer, and selected 'coach' role
                $_SESSION['is_trainer'] = true; // Add a flag to indicate the user is also a trainer
                $trainerResult->free();
                $trainerStmt->close();
                header("Location: trainerdb.php"); // Redirect to the coach dashboard
                exit();
            } elseif ($role === 'client') {
                // User selected 'client' role
                $_SESSION['is_trainer'] = false; // Add a flag to indicate the user is not a trainer
                $trainerStmt->close();
                header("Location: clientdb.php"); // Redirect to the client dashboard
                exit();
            } else {
                $error = "You are not authorized to log in as a coach. Please contact support.";
            }
            if ($trainerResult) {
                $trainerResult->free();
            }
            $trainerStmt->close();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
    if ($result) {
        $result->free();
    }
    $stmt->close();
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Center the error message in the login container */
        .error-message {
            color: red;
            font-size: 0.95em;
            margin: 20px auto;
            text-align: center;
            width: 80%;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <input type="email" name="email" placeholder="Email" required>
            <div style="position: relative;">
                <input type="password" id="password" name="password" placeholder="Password" required style="padding-right: 30px;">
                <i id="toggle-password" class="fa fa-eye" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
            </div>
            <select name="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="coach">Coach</option>
                <option value="client">Client</option>
            </select>
            <button type="submit" name="login">Login</button>
        </form>
        <p><a href="reset_password.html">Forgot Password?</a></p>
        <p>Want to become a coach? <a href="register_coach.html">Register as a New Coach</a></p>
        <p>Don't have an account? <a href="signup.html">Sign up</a></p>
    </div>
    <script>
        document.getElementById("toggle-password").addEventListener("click", function() {
            const passwordField = document.getElementById("password");
            const type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
        document.addEventListener("DOMContentLoaded", function() {
            const errorMessage = document.querySelector(".error-message");
            if (errorMessage && errorMessage.textContent.trim() === "") {
                errorMessage.style.display = "none";
            }
        });
    </script>
</body>
</html>