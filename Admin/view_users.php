<?php
session_start();
include "../config/session_cleanup.php";
include "../config/db_connect.php";


if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

cleanupSession();

try {
    $query = "SELECT * FROM users ORDER BY created_at DESC";
    if (!$result = $conn->query($query)) {
        throw new Exception("Query failed: " . $conn->error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $error = "Failed to fetch users.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Registered Users</h2>
        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo (int)$user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo (int)$user['id']; ?>" 
                                       class="edit-btn">Edit</a>
                                    <a href="delete_user.php?id=<?php echo (int)$user['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this user?')"
                                       class="delete-btn">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-results">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>