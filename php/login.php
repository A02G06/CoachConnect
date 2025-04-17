<!-- filepath: c:\xampp\htdocs\CoachConnect\php\login.php -->
<?php
session_start();
include 'php_backup.php';

// Function to validate login credentials
function validateLogin($conn, $email, $password, $role) {
    $query = "SELECT id FROM users WHERE email = ? AND password = ? AND role = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $email, $password, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc(); // Return user data if valid
    }
    return null; // Return null if invalid
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the role from the login form

    // Validate login credentials
    $user = validateLogin($conn, $email, $password, $role);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role === 'coach') {
            header("Location: ../html/trainersdash.html");
        } elseif ($role === 'client') {
            header("Location: ../php/clientdb.php");
        } else {
            header("Location: ../html/login.html?error=unexpected_role");
        }
        exit();
    } else {
        header("Location: ../html/login.html?error=invalid_credentials");
        exit();
    }
}
?>