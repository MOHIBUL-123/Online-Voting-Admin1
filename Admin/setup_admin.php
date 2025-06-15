<?php
include "../config/db_connect.php";


try {
    
    $conn->query("DROP TABLE IF EXISTS admin");

    
    $createTable = "CREATE TABLE admin (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";
    
    if (!$conn->query($createTable)) {
        throw new Exception("Table creation failed: " . $conn->error);
    }

    
    $username = 'admin';
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO admin (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $hashedPassword);
    
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }

    echo "<div style='margin: 20px; padding: 10px; border: 1px solid green;'>";
    echo "<h3>Setup Successful!</h3>";
    echo "Admin account created:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br><br>";
    echo "<a href='admin_login.php' style='color: blue;'>Go to Login Page</a>";
    echo "</div>";

} catch (Exception $e) {
    die("<div style='margin: 20px; padding: 10px; border: 1px solid red; color: red;'>" . 
        "Setup failed: " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>