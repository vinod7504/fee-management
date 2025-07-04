<?php
require_once '../config/init.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password";
        header("Location: ../index.php");
        exit();
    }
    
    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Password is correct, create session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            
            // Redirect to dashboard
            header("Location: ../dashboard.php");
            exit();
        }
    }
    
    // If we get here, login failed
    $_SESSION['error'] = "Invalid username or password";
    header("Location: ../index.php");
    exit();
} else {
    // If someone tries to access this file directly
    header("Location: ../index.php");
    exit();
} 