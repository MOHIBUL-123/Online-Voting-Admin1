<?php
session_start();
include "../config/db_connect.php";
include "../config/session_cleanup.php";

$error = '';

try {
    if (isset($_POST['login'])) {
        
        if (empty($_POST['username']) || empty($_POST['password'])) {
            throw new Exception("Username and password are required");
        }

        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $password = $_POST['password'];

        
        error_log("Login attempt for username: " . $username);

        $query = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            if (!isset($admin['password'])) {
                error_log("Password field not found in admin record");
                throw new Exception("Invalid admin record structure");
            }
            
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['last_activity'] = time();
                
                header('Location: admin_dashboard.php');
                exit();
            }
        }
        $error = "Invalid username or password";
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $error = "System error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>